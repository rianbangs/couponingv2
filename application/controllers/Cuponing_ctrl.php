<?php 

class Cuponing_ctrl extends CI_Controller
{    
     function __construct()
     {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Cupon_mod');
        $this->load->model('simplify/simplify','simplify');
       
     }


     function session_check()
     {
          if(!isset($_SESSION['username']))
          {
               $url = site_url('Cuponing_ctrl/login_ui');
               redirect($url);
          }
     }

     function session_check_js()
     {
          if(!isset($_SESSION['username']))
          {
               $response = 'expired'; 
          }
          else 
          {
               $response = 'ok';
          }

          $data['response'] = $response;
          echo json_encode($data);
     }


     function password_generator()
     {
          // Define the password to be hashed
          $password = '143';

          // Generate the hashed password
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);

          // Display the hashed password
          echo $hashed_password;
     }


     function logout()
     {
          session_destroy();   
          $url = site_url('Cuponing_ctrl/login_ui');
          redirect($url);
     }

     function validate_login()
     {
          // Define the password to be verified
          $username = $_POST['username']; 
          $password = $_POST['password'];
          $brand_id = $_POST['brand_id'];

           // Generate the hashed password         


          $search_user = $this->Cupon_mod->search_user($username);
          if(empty($search_user))
          {    
                $response = 'Account not found'; 
          }
          else 
          {
               // Retrieve the hashed password from a database or other source
               $hashed_password = $search_user[0]['password'];

               // Verify the password
               if (password_verify($password, $hashed_password)) 
               {
                     $response = 'Password is valid!';   
                     session_destroy();     
                     session_start();   

                     if($search_user[0]['access_type'] != 'pharma-admin')
                     {
                          $_SESSION['db_id']       = $search_user[0]['db_id'];
                          $_SESSION['brand_id']    = $brand_id; 
                     }
                     else 
                     {
                          $response = 'Password is pharma-admin';                         
                     }

                     $_SESSION['username']    = $username;    
                     $_SESSION['password']    = $password;    
                     $_SESSION['access_type'] = $search_user[0]['access_type'];                    
                     $_SESSION['user_id']     = $search_user[0]['user_id'];       
               } 
               else 
               {
                   $response = 'Invalid password.';
               }               
          }

          
          $data['redirect'] = site_url('Cuponing_ctrl/cupon_ui');

          $data['response'] = $response;
          echo json_encode($data);
     }

     function savePassword(){
          $oldpass = $_POST["acc_pass"];
          $newpass = $_POST["new_pass"];
          $conpass = $_POST["con_pass"];
          $user_details = $this->Cupon_mod->search_user($_SESSION["username"]);
          
          if($oldpass=="" || ctype_space($oldpass))
               echo "Please Input Old Password!";
          else if(!password_verify($oldpass,$user_details[0]["password"]))
               echo "Old Password Incorrect!";
          else if($newpass=="" || ctype_space($newpass))
               echo "Please Input New Password!";
          else if(strlen($newpass)<3)
               echo "Password Length Too Low!";
          else if($newpass!=$conpass)
               echo "Confirm Password must be equal to New Password!";
          else{
               $hashed_password = password_hash($conpass, PASSWORD_DEFAULT);
               $this->Cupon_mod->update_pass($hashed_password);
               echo json_encode(array("Successfully Updated!"));
          }

     }

     function login_ui()
     {
          // $data['promo_list'] = $this->Cupon_mod->get_promo_list();
          $data['brand_list'] = $this->Cupon_mod->get_brand_list('');

          $this->load->view('cupon/login',$data);
          // $this->load->view('cupon/Main_js');

     }



     function search_promo_code()
     {
          $promo_code = $_POST['promo_code'];

          $search_result =  $this->Cupon_mod->search_promo_code($promo_code);
          if(empty($search_result))
          {
               $response = 'success';
          }
          else 
          {
               $response = $search_result[0]['fname']." ".$search_result[0]['lname']."^".date('M d, Y',strtotime(date($search_result[0]['date_transact'])));
          }

          $data['response'] = $response;
          echo json_encode($data);
     }


     function search_order_number()
     {

           $user_data = $this->Cupon_mod->get_user_connection();

           $get_connection = $this->Cupon_mod->get_connection($user_data[0]['db_id']);
           
           if($user_data[0]['db_id'] == '19')    //if alta citta siya
           {
                if(strstr($_POST['inputVal'],'-'))
                {
                     $order_num_exp = explode('-',$_POST['inputVal']);
                     $order_number  = $order_num_exp[1];  
                }
                else 
                {
                     $order_number = $_POST['inputVal'];               
                }
           }
           else 
           {
                $order_number = $_POST['inputVal'];               
           }

           

           foreach($get_connection  as $con)
           {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
            }


           $connect      = odbc_connect($connection, $username, $password);
           //$table        = '[ICM - MP TAKE ORDER SERVER$Take Order Header]';


           if($user_data[0]['db_id'] == '19')    //if alta citta siya
           {
                $table        = '['.$sub_db_name.'$POS Transaction]';
                $table_query  = "SELECT TOP 10 * FROM ".$table." WHERE [Receipt No_] LIKE '%".$order_number."%'";
           }   
           else 
           {               
                $table        = '['.$sub_db_name.'$Take Order Header]';
                $table_query  = "SELECT TOP 10 * FROM ".$table." WHERE [Order No_] LIKE '%".$order_number."%'";
           }


           $table_row    = odbc_exec($connect, $table_query);  

           $ord_num_arr  = array();
           if(odbc_num_rows($table_row) > 0)
           {
                while(odbc_fetch_row($table_row))
                {
                     $order_num   = odbc_result($table_row, 2);                     
                     if(!in_array($order_num,$ord_num_arr))
                     {
                          $search_result = $this->Cupon_mod->search_cupon_data('ordering_number',$order_num);    
                          if(empty($search_result))
                          {
                                if($user_data[0]['db_id'] == '19')    //if alta citta siya
                                {
                                   $order_num_exp = explode('P',$order_num);
                                   $order_num     = "Counter ".substr($order_num_exp[1], 0, 2).'-'.substr($order_num_exp[1], 2);
                                }
                                 
                                array_push($ord_num_arr,$order_num);
                          } 
                     }
                }
           }
          

           $data['ord_num_arr'] = $ord_num_arr;
           echo json_encode($data);
     }



     // function check_ordering_number()
     // {
     //       $order_number  = $_POST['order_number'];    
     //       $search_result = $this->Cupon_mod->search_cupon_data('ordering_number',$order_number);           
     //       if(empty($search_result))
     //       {
     //           $response = 'ok';
     //       }
     //       else 
     //       {
     //           $response = 'exist';
     //       }

     //       $data['response'] = $response;
     //       echo json_encode();
     // }


     function get_alta_uom($item_code)
     {
           $user_data      = $this->Cupon_mod->get_user_connection();  
           $get_connection = $this->Cupon_mod->get_connection($user_data[0]['db_id']);
           foreach($get_connection  as $con)
           {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
           }   
           $connect     = odbc_connect($connection, $username, $password);
           $table       = '['.$sub_db_name.'$Item]';
           $table_query = "SELECT * FROM ".$table." WHERE [No_] = '".$item_code."'";

           $table_row    = odbc_exec($connect, $table_query);    

           $uom = '';
           if(odbc_num_rows($table_row) > 0)
           {
                while(odbc_fetch_row($table_row))
                {
                     $uom = odbc_result($table_row, 84); 
                }
           }
           return $uom;
     }




     function get_Take_Order_Line()
     {
           

           $user_data = $this->Cupon_mod->get_user_connection();


           if($user_data[0]['db_id'] == '19')    //if alta citta siya
           {
                if(strstr($_POST['order_number'],'-'))
                {
                     $order_num_exp = explode('-',$_POST['order_number']);
                     $order_number  = 'P'.substr($order_num_exp[0], -2).$order_num_exp[1];  
                }
                else 
                {
                     $order_number = $_POST['order_number'];               
                }
           }
           else 
           {
                $order_number = $_POST['order_number'];               
           }

            

           $get_connection = $this->Cupon_mod->get_connection($user_data[0]['db_id']);
           foreach($get_connection  as $con)
           {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
           }

           $connect      = odbc_connect($connection, $username, $password);


           if($user_data[0]['db_id'] == '19') //if alta citta
           {
                $table        = '['.$sub_db_name.'$POS Trans_ Line]';
                $table_query  = "SELECT * FROM ".$table." WHERE [Receipt No_] like   '%".$order_number."%'";

           }
           else 
           {               

                $table        = '['.$sub_db_name.'$Take Order Line]';
                $table_query  = "SELECT * FROM ".$table." WHERE [No_] = '".$order_number."'";
           }

           
            

           $table_row    = odbc_exec($connect, $table_query);             
           $item_line    = array();

           if(odbc_num_rows($table_row) > 0)
           {
                while(odbc_fetch_row($table_row))
                {

                     if($user_data[0]['db_id'] == '19') //if alta citta
                     {   

                          $item_code   = odbc_result($table_row, 6); //ani nihunong
                          $uom         = $this->get_alta_uom($item_code);                          
                          //$uom         = odbc_result($table_row, 8); 
                          $price       = odbc_result($table_row, 54);
                          $item_name   = odbc_result($table_row, 10);
                          $quantity    = odbc_result($table_row, 20); 
                     }   
                     else 
                     {
                          $item_code   = odbc_result($table_row, 7);
                          $uom         = odbc_result($table_row, 8); 
                          $price       = odbc_result($table_row, 17);
                          $item_name   = odbc_result($table_row, 13);
                          $quantity    = odbc_result($table_row, 15); 
                     }    




                     $search_res = $this->Cupon_mod->search_item_by_item_code($item_code,$uom);    //if naa siya sa list  sa mga  participating brands
                     if(!empty($search_res))
                     {
                          $item_entry  = array();
                          unset($item_entry);
                          $item_entry  = array($item_code,$price,$item_name,$quantity,$uom);
                          array_push($item_line,$item_entry);                 
                     }
                }
           }

           
           $data['item_line_arr'] = $item_line;
           echo json_encode($data);

     }



     function check_item()
     {
           $item_code     = $_POST['item_code'];
           $quantity      = $_POST['quantity'];
           $item_name     = $_POST['item_name'];
           $price         = $_POST['price'];
           $uom           = $_POST['uom'];
           $new_rows_data = array();     

           
            


           $user_data = $this->Cupon_mod->get_user_connection();

           $get_connection = $this->Cupon_mod->get_connection($user_data[0]['db_id']);
           foreach($get_connection  as $con)
           {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
            }
          
           $connect      = odbc_connect($connection, $username, $password);

           if($user_data[0]['db_id'] == '19') //if alta citta
           {
                $table        = '['.$sub_db_name.'$Item]';
                $table_query  = "SELECT * FROM ".$table." WHERE [No_] = '".$item_code."' AND  [Sales Unit of Measure] = '".$uom."'";
           }
           else
           {
                $table        = '['.$sub_db_name.'$Item]';
                $table_query  = "SELECT * FROM ".$table." WHERE [No_] = '".$item_code."' AND  [Base Unit of Measure] = '".$uom."'";
           }

           

           $table_row    = odbc_exec($connect, $table_query);  

           if(odbc_num_rows($table_row) > 0)
           {
                while(odbc_fetch_row($table_row))
                {
                     if($user_data[0]['db_id'] == '19') //if alta citta
                     {
                          $vatable   = odbc_result($table_row, 57);                     
                     } 
                     else 
                     {
                          $vatable   = odbc_result($table_row, 56);                     
                     }
                }
           }


           
           $discount         = 0.00;
           $discounted_price = 0.00;            
           $cash_quantity    = 0;
    


/*new code--------------------------------------------------------------*/
           $promo_data =   $this->Cupon_mod->get_promo_data($vatable,$quantity,$item_code);
           if(!empty($promo_data))
           {
                $discount             = $promo_data[0]['discount'];
                $promo_quantity       = $promo_data[0]['quantity'];
                $minus_price          = $price * $discount; 
                $_SESSION['promo_id'] = $promo_data[0]['promo_id'];
                //$discounted_price  = ($price * $promo_quantity) - ($minus_price * $promo_quantity);  //net price
                $discounted_price  = ($price * $discount) * $promo_quantity ;   
                if($promo_data[0]['vatable']  == 'VAT12')                  
                {
                     $vatable = 'V';
                }
                else 
                {
                     $vatable = 'EV';
                }

                $cash_quantity = $quantity - $promo_quantity; 
                $disc_quantity = $promo_quantity;    
           }
           else 
           {
                 if($vatable  == 'VAT12')                  
                {
                     $vatable = 'V';
                }
                else 
                {
                     $vatable = 'EV';
                }
                $discount         = 0.00;
                $minus_price      = 0.00; 
                $discounted_price = 0.00;                 
                $cash_quantity    = $quantity; 
                $disc_quantity    = 0;
           }

/*end of new code------------------------------------------------------*/
               

          
           $data['item_code']        = $item_code;
           $data['item_name']        = $item_name;
           $data['price']            = number_format($price,2);
           $data['discount']         = $discount;
           $data['discounted_price'] = number_format($discounted_price,2) ;
           $data['vatable']          = $vatable;
           $data['cash_quantity']    = number_format($cash_quantity);
           $data['disc_quantity']    = number_format($disc_quantity);
           $data['new_rows_data']    = $new_rows_data;
           $data['vatable']          = $vatable;
 
           echo json_encode($data);
     }


     function cupon_ui()
     {
          $this->session_check();
          $user_details = $this->Cupon_mod->search_user($_SESSION['username']);
          $data['fname'] = $user_details[0]['fname'];

          if(isset($_SESSION['brand_id']))
          {
               // $promo_list = $this->Cupon_mod->get_promo_list(); 
               $brand_list = $this->Cupon_mod->get_brand_list(''); 

               foreach($brand_list as $brand)
               {
                   if($_SESSION['brand_id'] == $brand['brand_id'])  
                   {
                       $data['brand_name'] = $brand['brand_name'];
                   }
               }

               // $data['brand_details'] =  $this->Cupon_mod->get_promo_list_details();
               $data['brand_details'] =  $this->Cupon_mod->get_brand_details();
          }

          $this->load->view('cupon/Plugin');
          $this->load->view('cupon/Sidebar',$data);
          $this->load->view('cupon/Body',$data);
          if($user_details[0]['access_type'] == 'ordering')
          {
                $this->load->view('cupon/Data_input_ui');          
          }
          else if($user_details[0]['access_type'] == 'pharma-admin')
          {
               if(!isset($_GET["user"]))
                    $this->load->view('cupon/promolist');
               else
                    $this->load->view('cupon/userlist');         
          }
          else 
          {
                // $current_date       = date('Y-m-d');
                // $data['cupon_list'] = $this->Cupon_mod->get_cupon_data($current_date,$current_date); 

                 $this->load->view('cupon/Dashboard');                         
                //$this->load->view('cupon/sales_report_ui',$data);                         
          }
          
          $this->load->view('cupon/Footer');
          $this->load->view('cupon/Session_checker');
          $this->load->view('cupon/Main_js');
     }

     // Pharma-Admin Start
     function getPromoItems(){
          $program_id = $_POST["program_id"];
          
          $list = $this->Cupon_mod->getPromoItems($program_id);
          echo json_encode($list);
     }

     function savePromoItem(){
          $program_id = $_POST["program_id"];
          $item_code = $_POST["item_code"];
          $uom = $_POST["uom"];

          $desc = $this->Cupon_mod->getItemDescNav($item_code,$uom);
          $count_item = $this->Cupon_mod->getPromoItemCountByCodeAndUOM($program_id,$item_code,$uom);
          
          if($item_code=="" || ctype_space($item_code))
               echo json_encode(array("error","Pls Input Item Code!"));
          else if($desc=="")
               echo json_encode(array("error","Item Not Existing!"));
          else if($count_item>0)
               echo json_encode(array("error","Item Already on the Promo!"));
          else{
               $this->Cupon_mod->insertPromoItem($program_id,$item_code,$uom);
               echo json_encode(array("success","Item Added to Promo!"));
          }
     }

     function deletePromoItem(){
          $program_id = $_POST["program_id"];
          $item_id = $_POST["item_id"];
          $this->Cupon_mod->deletePromoItem($program_id,$item_id);
          echo json_encode(array("success","Promo Item Removed!"));
     }

     function getUserList(){
          $list = $this->Cupon_mod->retrieveCouponUsers();
          echo json_encode($list);
     }

     function addCouponUser(){
          $firstname = $_POST["firstname"];
          $lastname = $_POST['lastname'];
          $username = $_POST['username'];
          $db_id = $_POST['db_id'];
          $access = $_POST["access"];
          $count_username = count($this->Cupon_mod->search_user($username));

          if($firstname=="" || ctype_space($firstname))
               echo json_encode(array("error","Pls Input Firstname!"));
          else if($lastname=="" || ctype_space($lastname))
               echo json_encode(array("error","Pls Input Lastname!"));
          else if($username=="" || ctype_space($username))
               echo json_encode(array("error","Pls Input Username!"));
          else if($count_username>0)
               echo json_encode(array("error","Username Already Exists!"));
          else{
               if($_SESSION['access_type']=="accounting")
                    $db_id = 13;

               $this->Cupon_mod->insertCouponUser($firstname,$lastname,$username,$db_id,$access);
               echo json_encode(array("success","User Added!"));
          }
          
     }

     function getCouponUser(){
          $id = $_POST["id"];
          $list = $this->Cupon_mod->retrieveCouponUserById($id);
          echo json_encode($list);
     }

     function updateCouponUser(){
          $id = $_POST["id"];
          $firstname = $_POST["firstname"];
          $lastname = $_POST['lastname'];
          $username = $_POST['username'];
          $password = $_POST['password'];
          $new_password = $_POST['new_password'];
          $con_password = $_POST['con_password'];
          $count_username = count($this->Cupon_mod->search_user($username));
          $current_details = $this->Cupon_mod->retrieveCouponUserById($id);

          if($firstname=="" || ctype_space($firstname))
               echo json_encode(array("error","Pls Input Firstname!"));
          else if($lastname=="" || ctype_space($lastname))
               echo json_encode(array("error","Pls Input Lastname!"));
          else if($username=="" || ctype_space($username))
               echo json_encode(array("error","Pls Input Username!"));
          else if($username!=$current_details["username"] && $count_username>0)
               echo json_encode(array("error","Username Already Exists!"));
          else if($password!="" && !password_verify($password, $current_details["password"]))
               echo json_encode(array("error","Incorrect Old Password!"));
          else if($password!="" && $new_password=="")
               echo json_encode(array("error","Pls Input New Password!"));
          else if($password!="" && strlen($new_password)<3)
               echo json_encode(array("error","Password Length Too Low!"));
          else if($password!="" && $new_password!=$con_password)
               echo json_encode(array("error","Confirm Password does not match with New Password!"));
          else{
               
               $update_array["fname"] = $firstname;
               $update_array["lname"] = $lastname;
               $update_array["username"] = $username;

               if($password!="")
                    $update_array["password"] = password_hash($new_password, PASSWORD_DEFAULT);

               $this->Cupon_mod->updateCouponUser($update_array,$id);
               echo json_encode(array("success","User Updated!"));
               
          }
     }

     // Pharma-Admin End

     function load_sales_report_ui()
     {
           $current_date       = date('Y-m-d');

           if(in_array($_SESSION['access_type'],array('accounting','mpdi')))
           {
                $access = 'all';
           }
           else 
           {
                $access = $_SESSION['db_id'];               
           }


           $data['cupon_list'] = $this->Cupon_mod->get_cupon_data($current_date,$current_date,$access); 

           $db_id_list = $this->Cupon_mod->get_db_id_cupon_data();
           $store_arr  = array();
           foreach($db_id_list as $db)
           {
                $db_details = $this->Cupon_mod->get_connection($db['db_id']);
                array_push($store_arr,array('store'=>$db_details[0]['display_name'], 'db_id'=>$db['db_id']));
           }           

           $data['store_arr'] = $store_arr;


           $this->load->view('cupon/sales_report_ui',$data);  
     }

     function mpdi_billing_report()
     {
          if(isset($_POST['dateFrom']))
           {
               $from_date = $_POST['dateFrom'];   
               $dateTo    = $_POST['dateTo'];                 
           }
           else 
           {
                $from_date = date('Y-m-d');  
                $dateTo    = date('Y-m-d');  
                $status    = 'unbilled';              
           }

           $batch_list = $this->Cupon_mod->get_promo_billing_batch('mpdi',$from_date,$dateTo);

           if(isset($_POST['dateFrom']))
           {
                $final_arr  = array();
                foreach($batch_list as $batch)
                {
                    $user_details        = $this->Cupon_mod->get_user_details($batch['user_id']);
                    // if($batch['mpdi-status'] == '')
                    // {
                          $checkbox = '<input onclick ="check_checkboxes('."'billing-table'".')" class="checkbox" type="checkbox" name="checkbox" value="'.$batch['batch_id'].'">';
                    // }
                    // else 
                    // {
                    //       $checkbox  = '';
                    // }
                    $get_database = $this->Cupon_mod->get_cupon_data_per_batch($batch['batch_id']);
                    foreach($get_database as $db)
                    {
                          $database_details    = $this->Cupon_mod->get_connection($db['db_id']);

                    }


                    $batch_number        = $database_details[0]['display_name']."-".$user_details[0]['department']."-".$batch['batch_id'];
                    $from_date           = date('m/d/Y', strtotime($batch['from_date']));
                    $to_date             = date('m/d/Y', strtotime($batch['to_date']));
                    $paid_invoice_number = $batch['paid_invoice_number']; 
                    $mpdi_status         = $batch['mpdi-status'];    
                    $button              = '<button type="button" onclick="view_billing_details('."'".$batch['batch_id']."'".')" class="btn btn-danger btn-sm" style="margin-left: 5px;"><span class="d-none d-sm-block">view</span> </button>';
                                      
                    array_push($final_arr,array('checkbox'=>$checkbox,'batch_number'=>$batch_number,'from_date'=>$from_date,'to_date'=>$to_date,'paid_invoice_number'=>$paid_invoice_number,'mpdi_status'=>$mpdi_status,'button'=>$button) );
                }

                $data['billing_list']  = $final_arr;
                echo json_encode($data);
           }
           else 
           {
                $data['batch_list'] = $batch_list;
                $this->load->view('cupon/mpdi_billing_ui',$data);
           }


     }


     function view_billing_details()
     {
           $tbl           = '';
           $batch_id      = $_POST['batch_id'];
           $batch_details = $this->Cupon_mod->get_billed_batch($batch_id);          
           

           $user_data     =  $this->Cupon_mod->get_user_connection();
           $db_data       = $this->Cupon_mod->get_connection($_SESSION['db_id']);
           $header = array("BRANCH NAME","DATE","TIME","PRODUCT NAME(SKU)","DISC. %","DISC. AMT","QTY. PURCH.","RECEIPT","PROMO CODE");
           // $style_header  = array(
           //                                   "width:80px;text-align:center;font-weight: bold;",
           //                                   "width:60px;text-align:center;font-weight: bold;",
           //                                   "width:80px;text-align:center;font-weight: bold;",
           //                                   "width:200px;text-align:center;font-weight: bold;",
           //                                   "width:50px;text-align:center;font-weight: bold;",
           //                                   "width:100px;text-align:center;font-weight: bold;",
           //                                   "width:70px;text-align:center;font-weight: bold;",
           //                                   "width:80px;text-align:center;font-weight: bold;",
           //                                   "width:78px;text-align:center;font-weight: bold;"
           //                                 );



           // $tbl = $this->pdf_table_header($tbl,$column_header,$style_header); 
           $table_id  = 'Transaction_details'; 
           $tbl      .= $this->simplify->populate_header_table($table_id,$header);
           $style     = 'color:black;font-family: sans-serif;';          
           foreach($batch_details as $batch)      
           {
                          $date = date('m/d/Y', strtotime($batch['date_transact']));
                          $time = date('H:i:s', strtotime($batch['date_transact']));
                          $item_details = $this->get_item_details($batch['item_code'],$batch['db_id']);
                          $product_name = $item_details[0];  

                       
                           $user_details     = $this->Cupon_mod->get_user_details($batch['user_id']);
                           $database_details = $this->Cupon_mod->get_connection($user_details[0]['db_id']);  

                           $get_database = $this->Cupon_mod->get_cupon_data_per_batch($batch_id);
                           foreach($get_database as $db)
                           {
                               $database_details = $this->Cupon_mod->get_connection($db['db_id']);
                           }



                           $branch_name      = $database_details[0]['display_name'].'-'.$user_details[0]['department'];




                           $disc_explode     = explode('.',$batch['discount']);
                           if($disc_explode[0] > 0 )
                           {
                               $discount = $disc_explode[0];
                           }
                           else 
                           {
                               $discount = $disc_explode[1];                              
                           }


                          $tbl .= ' 
                                    <tr>    
                                             <td style="'.$style.'"> 
                                              '.$branch_name.'
                                             </td>
                                             <td style="'.$style.'">
                                                   '.$date.'
                                             </td>
                                             <td style="text-align:center;'.$style.'">
                                                   '.$time.'
                                             </td>
                                             <td style="text-align:left;height:20px;'.$style.'">
                                                   '.$product_name.'
                                             </td>
                                             <td style="text-align:right;'.$style.'">
                                                   '.$discount.'
                                             </td>
                                             <td style="text-align:right;'.$style.'">
                                                   '.$batch['discounted_price'].'    
                                             </td>
                                             <td style="text-align:right;'.$style.'">
                                                   '.$batch['quantity'].'    
                                             </td>
                                             <td style="text-align:right;'.$style.'">
                                                   '.$batch['receipt_no'].'    
                                             </td>
                                             <td style="'.$style.'">
                                                   '.$batch['promo_code'].'    
                                             </td> 
                                    </tr>    '; 


           }



           $tbl .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#'.$table_id.'").dataTable({                            
                                         "order":[0,"asc"]
                                       });

                             </script>';

           $data['html'] = $tbl;    


           echo  json_encode($data);
     }


     function billing_report()
     {
           if(isset($_POST['dateFrom']))
           {
               $from_date = $_POST['dateFrom'];   
               $dateTo    = $_POST['dateTo'];   
               $status    = $_POST['status'];
           }
           else 
           {
                $from_date = date('Y-m-d');  
                $dateTo    = date('Y-m-d');  
                $status    = 'unbilled';              
           }


           $db_id_list = $this->Cupon_mod->get_db_id_cupon_data();
           $store_arr  = array();
           foreach($db_id_list as $db)
           {
                $db_details = $this->Cupon_mod->get_connection($db['db_id']);
                array_push($store_arr,array('store'=>$db_details[0]['display_name'], 'db_id'=>$db['db_id']));
           }           

           $data['store_arr'] = $store_arr;


           $db_id = '';
           if(in_array($_SESSION['access_type'],array('accounting')))
           {
                if(isset($_POST['store'])) 
                {
                     $db_id = $_POST['store'];
                }                
           }

           
           // $db_data    = $this->Cupon_mod->get_connection($_SESSION['db_id']);
           // $trans_line = $this->Cupon_mod->get_item_transact_per_line($from_date,$dateTo,'EOM');
           // $user_data  =  $this->Cupon_mod->get_user_connection();

           $billing_list = $this->Cupon_mod->get_variance($from_date,$dateTo,$status,$db_id);               

           

           
           if(isset($_POST['dateFrom']))
           {
                $final_arr  = array();
                foreach($billing_list as $var)
                {                   
                     if(in_array($var['status'], ['unbilled']))     
                     {
                        $checkbox = '<input onclick ="check_checkboxes('."'billing-table'".')" class="checkbox" type="checkbox" name="checkbox-'.$var['status'].'" value="'.$var['cupon_id'].'">';
                     }
                     else 
                     {
                        $checkbox = '';
                     }
                     array_push($final_arr,array('checkbox'=>$checkbox,"date_transact"=>date('m/d/Y', strtotime($var['date_transact'])),"ordering_number"=>$var['ordering_number'],"MUDC_disc"=>number_format($var['MUDC_disc'],2),"status"=>$var['status'],'cupon_id'=>$var['cupon_id']));
                }

                $data['billing_list'] = $final_arr;
                echo json_encode($data);
           }    
           else 
           {
                $data['billing_list'] = $billing_list;
                $this->load->view('cupon/billing_report',$data); 
           }


     }     



     function variance_report()
     {
           if(isset($_POST['dateFrom']))
           {
               $from_date = $_POST['dateFrom'];   
               $dateTo    = $_POST['dateTo'];   
           }
           else 
           {
                $from_date = date('Y-m-d');  
                $dateTo    = date('Y-m-d');                
           }



           $db_id_list = $this->Cupon_mod->get_db_id_cupon_data();
           $store_arr  = array();
           foreach($db_id_list as $db)
           {
                $db_details = $this->Cupon_mod->get_connection($db['db_id']);
                array_push($store_arr,array('store'=>$db_details[0]['display_name'], 'db_id'=>$db['db_id']));
           }           

           $data['store_arr'] = $store_arr;


           $db_id = '';
           if(in_array($_SESSION['access_type'],array('accounting')))
           {
                if(isset($_POST['store'])) 
                {
                     $db_id = $_POST['store'];
                }               
           }


           
           // $db_data    = $this->Cupon_mod->get_connection($_SESSION['db_id']);
           // $trans_line = $this->Cupon_mod->get_item_transact_per_line($from_date,$dateTo,'EOM');
           // $user_data  =  $this->Cupon_mod->get_user_connection();

           $variance_list = $this->Cupon_mod->get_variance($from_date,$dateTo,'',$db_id);               

           

           
           if(isset($_POST['dateFrom']))
           {
                $final_arr  = array();
                foreach($variance_list as $var)
                {                   
                     array_push($final_arr,array("date_transact"=>date('m/d/Y', strtotime($var['date_transact'])),"ordering_number"=>$var['ordering_number'],"MUDC_disc"=>number_format($var['MUDC_disc'],2),"nav_discount"=>number_format($var['nav_discount'],2),"variance"=>number_format($var['variance'],2)));
                }

                $data['variance_list'] = $final_arr;
                echo json_encode($data);
           }    
           else 
           {
                $data['variance_list'] = $variance_list;
                $this->load->view('cupon/variance_report',$data); 
           }
     }





     function EOM_report()
     {
           if(isset($_POST['dateFrom']))
           {
                $from_date = $_POST['dateFrom'];   
                $dateTo    = $_POST['dateTo'];   
                $store     = $_POST['store'];
           }
           else 
           {
                $from_date = date('Y-m-d');  
                $dateTo    = date('Y-m-d');  
                $store     = 'all';              
           }
           
           $trans_line = $this->Cupon_mod->get_item_transact_per_line($from_date,$dateTo,'EOM',$store);
           $user_data  =  $this->Cupon_mod->get_user_connection();
           $final_arr  = array();



           foreach($trans_line as $trn)
           {
                $db_data      = $this->Cupon_mod->get_connection($trn['db_id']);
                $item_details = $this->get_item_details($trn['item_code'],$trn['db_id']);
                $product_name = $item_details[0];                 
                $date         = date('m/d/Y', strtotime($trn['date_transact']));
                $time         = date('H:i:s', strtotime($trn['date_transact']));
                //$trans_date   = date('m/d/Y',strtotime(date($trn['date_transact'])));
                $Receipt      = $trn['receipt_no'];
                $qty          = $trn['quantity'];
                $promo_code   = $trn['promo_code'];
                $discount     = $trn['discount'];
                $branch_name  = $db_data[0]['store'].'-'.$user_data[0]['department'];

                $discount_amount = number_format((($trn['unit_price'] * $trn['discount']) * $trn['quantity']),2);
                
                array_push($final_arr,array("branch_name"=>$branch_name,"product_name"=>$product_name,"discount"=>$discount,"date"=>$date,"time"=>$time,"Receipt"=>$Receipt,"qty"=>$qty,"promo_code"=>$promo_code,"discount_amount"=>$discount_amount));
           }


           $db_id_list = $this->Cupon_mod->get_db_id_cupon_data();
           $store_arr  = array();
           foreach($db_id_list as $db)
           {
                $db_details = $this->Cupon_mod->get_connection($db['db_id']);
                array_push($store_arr,array('store'=>$db_details[0]['display_name'], 'db_id'=>$db['db_id']));
           }
           

           $data['store_arr'] = $store_arr;
           $data['eod_list']  = $final_arr;
           
           if(isset($_POST['dateFrom']))
           {
                echo json_encode($data);
           }    
           else 
           {
                $this->load->view('cupon/eom_report',$data); 
           }
     }



     function promo_masterfile()
     {
           // $data['brand_list'] = $this->Cupon_mod->get_brand_list('all');
           $data['brand_list'] = $this->Cupon_mod->get_promo_brand_list('');

           $this->load->view('cupon/promo_masterfile',$data);       
     }

     function brand_masterfile()
     {
          $this->load->view('cupon/brand_masterfile');  
     }



     function load_promo_list()
     {
          $brand_select = $_POST['brand_select'];
          $table_id     = 'promo_table';
          $promo_list   = $this->Cupon_mod->get_promo_list($brand_select);
          $html         = '';
          $table_header = array("Promo Name","Date From","Date To","Tender Type","Action");              
          $html        .= $this->simplify->populate_header_table($table_id,$table_header);


           $style_td ='font-family: Arial, sans-serif;';
           foreach($promo_list as $list)
           {
                $button = '<button class="btn btn-primary mr-1 mb-1" onclick="promo_modal(\''.$list['promo_id'].'\',\''.$list['promo_name'].'\')">view items</button>'; 
                $html .='<tr>
                              <td style="'.$style_td.'">'.$list['promo_name'].'</td>
                              <td style="'.$style_td.'">'.date('M d, Y',strtotime($list['date_from'])).'</td>
                              <td style="'.$style_td.'">'.date('M d, Y',strtotime($list['date_to'])).'</td>
                              <td style="'.$style_td.'">'.$list['tender_type'].'</td>
                              <td style="'.$style_td.'">'.$button.'</td>
                     </tr>
                  ';
           }    

          $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#'.$table_id.'").dataTable({                            
                                         "order":[0,"asc"]
                                       });

                             </script>';

           $data['html'] = $html;    


           echo  json_encode($data);
     }


     function EOD_report()
     {
           if(isset($_POST['dateFrom']))
           {
               $date = $_POST['dateFrom'];   

           }
           else 
           {
                $date = date('Y-m-d');                
           }





           $db_id_list = $this->Cupon_mod->get_db_id_cupon_data();
           $store_arr  = array();
           foreach($db_id_list as $db)
           {
                $db_details = $this->Cupon_mod->get_connection($db['db_id']);
                array_push($store_arr,array('store'=>$db_details[0]['display_name'], 'db_id'=>$db['db_id']));
           }           

           $data['store_arr'] = $store_arr;


           if(in_array($_SESSION['access_type'],array('accounting')))
           {
                if(isset($_POST['store'])) 
                {
                     $db_id = $_POST['store'];
                }
                else 
                {
                     $db_id = '';
                }
           }
           else 
           {               
                $db_id = $_SESSION['db_id'];
                //$user_data  =  $this->Cupon_mod->get_user_connection();                
           } 


           
          

           $trans_line_alta = $this->Cupon_mod->get_item_transact_per_line($date,'','EOD',$db_id);

           foreach($trans_line_alta as $trn)
           {
               if($trn['db_id'] == 19) //if alta citta siya 
               {
                     $cupon_id        = $trn['cupon_id'];
                     $tender_type     = $trn['tender_type'];
                     $ordering_number = $trn['ordering_number'];
                     $receipt_no      = str_replace(['Counter ', '-'], '', $ordering_number);

                     $get_connection = $this->Cupon_mod->get_connection(23);
                     foreach($get_connection  as $con)
                     {
                        $username    = $con['username'];
                        $password    = $con['password']; 
                        $connection  = $con['db_name'];
                        $sub_db_name = $con['sub_db_name'];
                     }


                     $connect      = odbc_connect($connection, $username, $password);
                     $table        = '['.$sub_db_name.'$Trans_ Payment Entry]';
                     $table_query  = "SELECT * FROM ".$table." WHERE [Receipt No_] like '%P".$receipt_no."' AND [Tender Type] = '".$tender_type."'";
                     $table_row    = odbc_exec($connect, $table_query);  
                     if(odbc_num_rows($table_row) > 0)
                     {
                        while(odbc_fetch_row($table_row))
                        {
                               $nav_discount     = odbc_result($table_row, 11);      
                               $transaction_no   = odbc_result($table_row, 4);  
                               $column_name_arr  = array("receipt_no","nav_discount",'transaction_no','status');
                               $column_value_arr = array('0000000P'.$receipt_no,$nav_discount,$transaction_no,'unbilled');
                               $search_result    = $this->Cupon_mod->search_cupon_data('ordering_number',$ordering_number);    
                               if(!empty($search_result))
                               {
                                     if($search_result[0]['transaction_no'] == '')
                                     {                                         
                                         $this->Cupon_mod->update_cupon_data($column_name_arr,$column_value_arr,$ordering_number,19);                             
                                     }
                               }
                        }
                     }


               }
           }




           $trans_line = $this->Cupon_mod->get_item_transact_per_line($date,'','EOD',$db_id);
           $final_arr  = array();
           foreach($trans_line as $trn)
           {              


                $db_data      = $this->Cupon_mod->get_connection($trn['db_id']);

                $item_details = $this->get_item_details($trn['item_code'],$trn['db_id']);
                $product_name = $item_details[0];                 
                $date         = date('m/d/Y', strtotime($trn['date_transact']));
                $time         = date('H:i:s', strtotime($trn['date_transact']));
                //$trans_date   = date('m/d/Y',strtotime(date($trn['date_transact'])));
                $Receipt      = $trn['receipt_no'];
                $qty          = $trn['quantity'];
                $promo_code   = $trn['promo_code'];
                $discount     = $trn['discount'];

                //$branch_name  = $db_data[0]['store'].'-'.$user_data[0]['department'];
                $branch_name  = $db_data[0]['store'].'-'.$trn['department'];

                $discount_amount = number_format((($trn['unit_price'] * $trn['discount']) * $trn['quantity']),2);
                
                array_push($final_arr,array("branch_name"=>$branch_name,"product_name"=>$product_name,"discount"=>$discount,"date"=>$date,"time"=>$time,"Receipt"=>$Receipt,"qty"=>$qty,"promo_code"=>$promo_code,"discount_amount"=>$discount_amount));
           }

           

           $data['eod_list'] = $final_arr;
           
           if(isset($_POST['dateFrom']))
           {
                echo json_encode($data);
           }    
           else 
           {
                $this->load->view('cupon/eod_report',$data); 
           }
           

           // $db_data    = $this->Cupon_mod->get_connection($_SESSION['db_id']);
           // $trans_line = $this->Cupon_mod->get_item_transact_per_line($date);
           // $user_data  =  $this->Cupon_mod->get_user_connection();
           // $final_arr  = array();

           // foreach($trans_line as $trn)
           // {
           //      $item_details = $this->get_item_details($trn['item_code']);
           //      $product_name = $item_details[0]; 
           //      $trans_date   = $trn['date_transact'];
           //      //$trans_date   = date('m/d/Y',strtotime(date($trn['date_transact'])));
           //      $Receipt      = '';
           //      $qty          = $trn['quantity'];
           //      $promo_code   = $trn['promo_code'];
           //      $discount     = $trn['discount'];
           //      $branch_name  = $db_data[0]['store'].'-'.$user_data[0]['department'];
                
           //      array_push($final_arr,array("branch_name"=>$branch_name,"product_name"=>$product_name,"discount"=>$discount,"trans_date"=>$trans_date,"Receipt"=>$Receipt,"qty"=>$qty,"promo_code"=>$promo_code));
           // }

     }


     function get_item_details($item_code,$db_id)
     {
   

           // if($db_id == '')
           // {
           //      $user_data = $this->Cupon_mod->get_user_connection();
           //      $db_id     = $user_data[0]['db_id'];
           // }


           $get_connection = $this->Cupon_mod->get_connection($db_id);
           foreach($get_connection  as $con)
           {
              $username    = $con['username'];
              $password    = $con['password']; 
              $connection  = $con['db_name'];
              $sub_db_name = $con['sub_db_name'];
           }


           $connect      = odbc_connect($connection, $username, $password);
           $table        = '['.$sub_db_name.'$Item]';
           $table_query  = "SELECT * FROM ".$table." WHERE [No_] = '".$item_code."'";
           $table_row    = odbc_exec($connect, $table_query);  
           if(odbc_num_rows($table_row) > 0)
           {
              while(odbc_fetch_row($table_row))
              {
                   $brand_name     = odbc_result($table_row, 4);  
                   $generic_name   = odbc_result($table_row, 104);  

              }
           }


           $data_arr = array($brand_name,$generic_name);

           return $data_arr;

     }


     function load_sales_report_ui_Filter()
     {
           $dateFrom = $_POST['dateFrom'];
           $dateTo   = $_POST['dateTo'];
           $store    = $_POST['store'];


           if(!in_array($_SESSION['access_type'],array('accounting','mpdi')))
           {
                $store = $_SESSION['db_id'];
           }
           


           $cupon_list = $this->Cupon_mod->get_cupon_data($dateFrom,$dateTo,$store);                     

           $final_data_arr =  array();

           foreach($cupon_list as $cup)
           {               
               $total_quantity =  $cup['total_quantity'];
               $item_code      = $cup['item_code'];
               $uom            = $cup['uom'];
               $item_details   = $this->get_item_details($item_code,$cup['db_id']);   

               array_push($final_data_arr,array("quantity"=>$total_quantity,"item_code"=>$item_code,"brand_name"=>$item_details[0],"generic"=>$item_details[1],"uom"=>$uom ));
           }

           $data['cupon_list'] = $final_data_arr;
           echo json_encode($data);

     }



     function load_eod_ui_filter()
     {
           $date       = date('Y-m-d'); 
           $db_data    = $this->Cupon_mod->get_connection($_SESSION['db_id']);
           $trans_line = $this->Cupon_mod->get_item_transact_per_line($date,'','EOD','');
           $user_data  =  $this->Cupon_mod->get_user_connection();
           $final_arr  = array();

           foreach($trans_line as $trn)
           {
                $item_details = $this->get_item_details($trn['item_code'],$trn['db_id']);
                $product_name = $item_details[0];                 
                $date         = date('m/d/Y', strtotime($trn['date_transact']));
                $time         = date('H:i:s', strtotime($trn['date_transact']));
                //$trans_date   = date('m/d/Y',strtotime(date($trn['date_transact'])));
                $Receipt      = '';
                $qty          = $trn['quantity'];
                $promo_code   = $trn['promo_code'];
                $discount     = $trn['discount'];
                $branch_name  = $db_data[0]['store'].'-'.$user_data[0]['department'];
                
                array_push($final_arr,array("branch_name"=>$branch_name,"product_name"=>$product_name,"discount"=>$discount,"date"=>$date,"time"=>$time,"Receipt"=>$Receipt,"qty"=>$qty,"promo_code"=>$promo_code));
           }

           $data['eod_list'] = $final_arr;

           echo json_encode($data);
     }



     function search_item()
     {
          $search = $_POST['inputVal']; 
          $data   = $this->Cupon_mod->search_item($search);
          echo json_encode($data);
     }


     function search_name()
     {
          $search = $_POST['inputVal']; 
          $data   = $this->Cupon_mod->search_name($search);
          echo json_encode($data);
     }

     function search_name_details()
     {
          $lname = $_POST['lname'];             
          $fname  = $_POST['fname'];

          $column_name_arr  = array('fname','lname'); 
          $column_value_arr = array($fname,$lname);


          $data   = $this->Cupon_mod->search_customer($column_name_arr,$column_value_arr); 
          echo json_encode($data);
     }



     function  submit_order_()
     {
           $fname       = $_POST['fname'];
           $promo_code  = $_POST['promo_code'];
           $allRowsData =  $_POST['allRowsData'];
           $fname       = $_POST['fname'];
           $promo_code  = $_POST['promo_code'];
           //phpinfo();
           // var_dump($allRowsData);
            //  foreach ($allRowsData as $rowData) 
          //  {                   

          //        $this->Cupon_mod->insert_item_transact($rowData,$cupon_id);   
          //        //  $itemCode = $rowData[0];
          //        //  $itemName = $rowData[1];
          //        //  $quantity = $rowData[2];
          //        //  $price = $rowData[3];
          //        //  $discount = $rowData[4];
          //        //  $discounted_price = $rowData[5];

          //        // echo  $itemCode.'--->'.$itemName.'---->'.$quantity.'----->'.$price.'---->'.$discount.'---->'.$discounted_price."<br>";
          // }

          $printer_name = "EPSON TM-H6000V Receipt5"; // Replace with the name of your printer
          $handle = printer_open($printer_name); // Open a connection to the printer
          if ($handle) {
            // Send some text to the printer
            printer_start_doc($handle, "My Document");
            printer_start_page($handle);
            printer_write($handle, "Hello, world!");
            printer_end_page($handle);
            printer_end_doc($handle);

            printer_close($handle); // Close the printer connection
          } else {
            echo "Failed to connect to printer.";
          }
 

        


     }


     function pdf_table_header($tbl,$column_header,$style_header)
     {
            $tbl .= '<table border="1">
                        <tr>
                    ';
            for($a=0;$a<count($column_header);$a++)
            {
                $tbl .= '<th style="'.$style_header[$a].'">'.$column_header[$a].'</th>';
            }                   

            $tbl .= '</tr>';

            return $tbl;
     }


     function generate_billing_report()
     {
           $allRowsData  = json_decode($_POST['allRowsData'], true);
           $from_date    = $_POST['from_date'];
           $date_to      = $_POST['date_to'];
           $batch_id     = $_POST['batch_id'];

           $this->ppdf   = new TCPDF();
           $this->ppdf->SetTitle("Variance Report (Inhouse vs. Navision)");            
           $this->ppdf->SetMargins(5, 15, 5, true);          
           $this->ppdf->setPrintHeader(false);
           $this->ppdf->SetFont('', '', 10, '', true);                    
           $this->ppdf->AddPage("P");
           $this->ppdf->SetAutoPageBreak(false);  

           $conn_details =  $this->Cupon_mod->get_connection($_SESSION['db_id']);


         




           //Image(image path, x, y, width, height, 'PNG')
           
           $this->ppdf->Image(base_url().$conn_details[0]['image_logo_path'], 75, 5, 50, 50, 'PNG');
           
           $batch_details     = $this->Cupon_mod->get_billed_batch($batch_id);
           $total_disc_amount = $this->Cupon_mod->get_total_billed_batch($batch_id);
           $pormo_details     = $this->Cupon_mod->get_promo_list_details('');
           $total_promo_code  = $this->Cupon_mod->count_promo_code($batch_id);


           

          $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
          $words = $formatter->format($total_disc_amount[0]['total_disc_amount']);

          
           //$batch_number_arr = array();
            $cupon_details    = $this->Cupon_mod->get_cupon_data_per_batch($batch_id);
                
            foreach($cupon_details as $cup)
            {
                $conn = $this->Cupon_mod->get_connection($cup['db_id']);    
                foreach($conn as $con)
                {
                  // if(!in_array($con['store']."-".$batch['department']."-".$batch['batch_id'],$batch_number_arr))
                  // {
                      //array_push($batch_number_arr,$con['store']."-".$batch['department']."-".$batch['batch_id']);
                      $address_location = $con['address_location'];
                      $contact_no       = $con['contact_no'];
                      $bill_no          = $con['display_name']."-".$batch_details[0]['department']."-".$batch_details[0]['batch_id'];
                      $branch_name      = $con['display_name']."-".$batch_details[0]['department'];
                      $supervisor       = $con['supervisor'];
                      $doctor           = $con['doctor'];
                      $sup_position     = $con['supervisor_position'];
                      $doc_position     = $con['doctor_position'];
                  //}
                   
                }

            }

            

            


           $border        = 0;
           $tbl           = '
                                   <table>
                                        <tr>
                                             <td>   </td>
                                        </tr>
                                        <tr>
                                             <td>   </td>
                                        </tr>
                                        <tr>
                                             <td>   </td>
                                        </tr>
                                        <tr>
                                             <td>   </td>
                                        </tr>
                                        <tr>
                                             <td>   </td>
                                        </tr>
                                         <tr>
                                             <td>   </td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                             <td style="width:125px;"> </td>                                             
                                             <td style="text-align:center;">'.$address_location.'</td>
                                        </tr>
                                        <tr>
                                             <td style="width:125px;"> </td>                                             
                                             <td style="text-align:center;">'.$contact_no.'</td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                             <td></td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                             <td style="width:40px;"></td>                                            
                                             <td style="width:30px;">Date:</td>
                                             <td style="width:250px;">'.date('F d, Y', strtotime($batch_details[0]['date_generated'])).'</td>                                              
                                             <td style="width:35px;">Bill No.</td>
                                             <td>'.$bill_no.'</td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td></td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                              <td style="width:40px;"></td>                                            
                                              <td style="width:30px;">To:</td>
                                              <td>MPDI</td>
                                             <!-- <td>'.$pormo_details[0]['company_name'].'</td> -->
                                        </tr>
                                         <tr>
                                              <td style="width:40px;"> </td>                                            
                                              <td style="width:30px;"> </td>
                                             <!-- <td>'.$pormo_details[0]['address'].'</td> -->
                                        </tr>
                                   </table>
                                    <table border="'.$border.'">
                                        <tr>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td></td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                             <td style="width:40px;"></td>   
                                             <td style="width:70px;"><strong>Subject:</strong></td>
                                             <td style="width:457px;"><strong>Billing Statement for Patient compliance to Unilab Consumer Health products</strong></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>   
                                             <td style="width:70px;"></td>
                                             <td style="width:457px;"><strong>transacted under '.$pormo_details[0]['company_name'].' '.$total_promo_code[0]['total_promo_code'].' Promo Codes from '.date('F d, Y', strtotime($batch_details[0]['from_date'])).'  to  '.date('F d, Y', strtotime($batch_details[0]['to_date'])).'</strong></td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td></td>
                                        </tr>                                         
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                              <td style="width:40px;"></td>   
                                              <td>To Whom it May Concern,</td>
                                        </tr>
                                         <tr>
                                              <td style="width:40px;"></td>   
                                              <td>Good Day!</td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                              <td style="width:40px;"></td>   
                                              <td style="width:528px;">
                                                                        <p>
                                                                             Please be billed the total amount of  <strong>'.$words.' Pesos (Php '.number_format($total_disc_amount[0]['total_disc_amount'],2).')</strong> 
                                                                             only, representing payment for <strong>Patient compliance to '.$pormo_details[0]['company_name'].' consumer health products transacted
                                                                             under '.$pormo_details[0]['company_name'].'  '.$total_promo_code[0]['total_promo_code'].' Promo Codes</strong> from '.date('F d, Y', strtotime($batch_details[0]['from_date'])).'  
                                                                             up to  '.date('F d, Y', strtotime($batch_details[0]['to_date'])).'. 
                                                                        </p>
                                              </td>
                                        </tr>
                                   </table>
                                   <table border="'.$border.'">
                                        <tr>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td></td>
                                        </tr>                                         
                                   </table>
                                   <table border="'.$border.'">
                                        <tr> 
                                             <td style="width:40px;"></td>  
                                             <td style="width:500px;"> Your prompt attention and early settlement of this account would be highly appreciated.</td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>  
                                             <td></td>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>   
                                             <td> Thank you!</td>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>  
                                             <td></td>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>  
                                             <td></td>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>  
                                             <td></td>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>  
                                             <td>Very Truly Yours</td>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>  
                                             <td></td>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>  
                                             <td></td>
                                             <td></td>
                                        </tr>
                                        <tr>
                                             <td style="width:40px;"></td>  
                                             <td></td>
                                             <td></td>
                                        </tr>
                                       </table> 
                                       <table border="'.$border.'">                                    
                                        <tr>
                                             <td style="width:40px;"></td>
                                             <td style="text-align:center;width:200px;"><strong>'.$supervisor.'</strong></td>
                                             <td></td>
                                             <td style="text-align:center"><strong>'.$doctor.'</strong></td>
                                        </tr>
                                        <tr>
                                             <td></td>  
                                             <td style="text-align:center">'.$sup_position.'</td>
                                             <td></td>                                               
                                             <td style="text-align:center;">'.$doc_position.'</td>
                                        </tr>

                                   </table>
                           ';                            
            $this->ppdf->writeHTML($tbl);                

           //$tbl           = ' <img src="'.base_url().'assets/cuponing/images/logo-mp.png" style="height: 100rem;">';
           //$this->ppdf->writeHTML($tbl);

           // $user_data     =  $this->Cupon_mod->get_user_connection();
           // $db_data       = $this->Cupon_mod->get_connection($_SESSION['db_id']);
           // $branch_name   = $db_data[0]['store'].'-'.$user_data[0]['department'];
           $column_header = array("BRANCH NAME","DATE","TIME","PRODUCT NAME(SKU)","DISC.","DISC. AMT","QTY. PURCH.","RECEIPT","PROMO CODE");
           $style_header  = array(
                                   "width:100px;text-align:center;font-weight: bold;",
                                   "width:60px;text-align:center;font-weight: bold;",
                                   "width:50px;text-align:center;font-weight: bold;",
                                   "width:270px;text-align:center;font-weight: bold;",
                                   "width:35px;text-align:center;font-weight: bold;",
                                   "width:80px;text-align:center;font-weight: bold;",
                                   "width:50px;text-align:center;font-weight: bold;",
                                   "width:80px;text-align:center;font-weight: bold;",
                                   "width:100px;text-align:center;font-weight: bold;"
                                 );
           //$tbl           = $this->pdf_table_header($tbl,$column_header,$style_header);  

           $line_counter = 1;
           foreach($batch_details as $batch)      
           {
                $date = date('m/d/Y', strtotime($batch['date_transact']));
                $time = date('H:i:s', strtotime($batch['date_transact']));
                $item_details = $this->get_item_details($batch['item_code'],$batch['database_id']);
                $product_name = $item_details[0];  

                if($line_counter == 1)
                {
                    //$this->ppdf->writeHTML($tbl, true, false, false, false, '');  
                    $tbl = '';        
                    $this->ppdf->AddPage("L");          
                    $tbl = $this->pdf_table_header($tbl,$column_header,$style_header);  
                     
                } 

                $discount_exp = explode(".",$batch['discount']); 
                if($discount_exp[0] >0)
                {
                    $discount = $discount_exp[0]."%";
                }
                else 
                {
                    $discount = $discount_exp[1]."%";                    
                }
                

                $tbl .= ' 
                          <tr>    
                                   <td> 
                                    '.$branch_name.'
                                   </td>
                                   <td>
                                         '.$date.'
                                   </td>
                                   <td>
                                         '.$time.'
                                   </td>
                                   <td style="text-align:left;height:20px;">
                                         '.$product_name.'
                                   </td>
                                   <td style="text-align:center">
                                         '.$discount.'
                                   </td>
                                   <td style="text-align:right">
                                         '.$batch['discounted_price'].'    
                                   </td>
                                   <td style="text-align:right">
                                         '.$batch['quantity'].'    
                                   </td>
                                   <td style="text-align:right">
                                         '.$batch['receipt_no'].'    
                                   </td>
                                   <td>
                                         '.$batch['promo_code'].'    
                                   </td> 
                          </tr>    ';

                if($line_counter == 25)
                {
                    $tbl .= "</table>";
                    $line_counter =1;
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');  
                } 
                else 
                {
                    $line_counter += 1;
                } 

           }
           

           //$tbl .= "</table>";


           $tbl .= "</table>";
           $this->ppdf->writeHTML($tbl, true, false, false, false, '');
           

           ob_end_clean();
           $this->ppdf->Output();    
     }




     function set_paid()
     {
          $batch_id       = $_POST['batch_id'];
          $invoice_number = $_POST['invoice_number'];

          $this->Cupon_mod->update_promo_billing_batch($batch_id,$invoice_number);
          $this->Cupon_mod->update_cupon_data_paid($batch_id);

          $data['response'] = 'success';

          echo json_encode($data);
     }


     function generate_txt_file()
     {
           $filter_date = $_POST['dateFrom'];
           header("Content-Type: text/plain");
           header("Content-Disposition: attachment; filename=".$filter_date."_dataport.txt");

           $dataport   = '';
           $trans_line = $this->Cupon_mod->get_item_transact_per_line($filter_date,'','EOD','');

           foreach($trans_line as $data)
           {
                if(strstr($data['ordering_number'],'Counter'))
                {
                    $exp_ord      = explode('-',$data['ordering_number']);
                    $order_number = '0000000P'.substr($exp_ord[0],-2).$exp_ord[1]; 
                }
                else 
                {
                    $order_number = $data['ordering_number'];
                }

                $dataport .= $order_number."|";
           }

           echo substr($dataport,0,-1);


     }





     function generate_excel_file()
     {
          header("content-type: application/vnd.ms-excel");
          header("Content-Disposition: attachment; filename=MPDI BILLING_".$_POST['dateFrom']." to ".$_POST['dateTo'].".xls");

            
          $batch_id = json_decode($_POST['checked'], true);          
          $border   = 1;
          $dateFrom = date("F j, Y", strtotime($_POST['dateFrom']));
          $dateTo = date("F j, Y", strtotime($_POST['dateTo']));

          $tbl = '
                   <table>
                             <tr>
                                   <td>
                                         <strong>MPDI BILLING REPORT</strong>       
                                   </td>
                             </tr> 
                             <tr>
                                   <td>
                                         <strong>From:</strong>'.$dateFrom."     
                                   </td>
                                   <td>
                                   </td>
                                   <td>
                                         <strong>To:</strong>".$dateTo."
                                   </td>      
                             </tr>
                             <tr>
                                  <td>
                                  </td> 
                             </tr>
                   </table>" ;

          for($a=0;$a < count($batch_id);$a++)
          {
                     $tbl .= '<table>
                                        <tr>
                                             <td>
                                                  <strong>Billing Number:</strong>'.$batch_id[$a].'
                                             </td>
                                        </tr>
                              </table>';



                     $batch_details = $this->Cupon_mod->get_billed_batch($batch_id[$a]);          
                     $this->Cupon_mod->update_date_extracted($batch_id[$a]);
                     $this->Cupon_mod->update_cupon_data_billed_mpdi($batch_id[$a]);

                     $user_data     =  $this->Cupon_mod->get_user_connection();
                     $db_data       = $this->Cupon_mod->get_connection($_SESSION['db_id']);
                     $column_header = array("BRANCH NAME","DATE","TIME","PRODUCT NAME(SKU)","DISC. %","DISC. AMT","QTY. PURCH.","RECEIPT","PROMO CODE");
                     $style_header  = array(
                                             "width:80px;text-align:center;font-weight: bold;",
                                             "width:60px;text-align:center;font-weight: bold;",
                                             "width:80px;text-align:center;font-weight: bold;",
                                             "width:200px;text-align:center;font-weight: bold;",
                                             "width:50px;text-align:center;font-weight: bold;",
                                             "width:100px;text-align:center;font-weight: bold;",
                                             "width:70px;text-align:center;font-weight: bold;",
                                             "width:80px;text-align:center;font-weight: bold;",
                                             "width:78px;text-align:center;font-weight: bold;"
                                           );



                     $tbl = $this->pdf_table_header($tbl,$column_header,$style_header);  
                     
                     foreach($batch_details as $batch)      
                     {
                          $date = date('m/d/Y', strtotime($batch['date_transact']));
                          $time = date('H:i:s', strtotime($batch['date_transact']));
                          $item_details = $this->get_item_details($batch['item_code'],$batch['db_id']);
                          $product_name = $item_details[0];  

                       
                           $user_details     = $this->Cupon_mod->get_user_details($batch['user_id']);

                           $get_db_det = $this->Cupon_mod->get_cupon_data_per_batch($batch_id[$a]);
                           foreach($get_db_det as $db)
                           {
                               $database_details = $this->Cupon_mod->get_connection($db['db_id']);  
                           } 



                           $branch_name      = $database_details[0]['display_name'].'-'.$user_details[0]['department'];

                           $disc_explode     = explode('.',$batch['discount']);
                           if($disc_explode[0] > 0)
                           {
                               $discount = $disc_explode[0];
                           }
                           else 
                           {
                               $discount = $disc_explode[1];
                           }


                          $tbl .= ' 
                                    <tr>    
                                             <td> 
                                              '.$branch_name.'
                                             </td>
                                             <td>
                                                   '.$date.'
                                             </td>
                                             <td style="text-align:center;">
                                                   '.$time.'
                                             </td>
                                             <td style="text-align:left;height:20px;">
                                                   '.$product_name.'
                                             </td>
                                             <td style="text-align:right">
                                                   '.$discount.'
                                             </td>
                                             <td style="text-align:right">
                                                   '.$batch['discounted_price'].'    
                                             </td>
                                             <td style="text-align:right">
                                                   '.$batch['quantity'].'    
                                             </td>
                                             <td style="text-align:right">
                                                   '.$batch['receipt_no'].'    
                                             </td>
                                             <td>
                                                   '.$batch['promo_code'].'    
                                             </td> 
                                    </tr>    ';             

                     }
                     

                     //$tbl .= "</table>";


                     $tbl .= "</table><br><br>";
          }


           echo $tbl;

     }




     function get_promo_billing_batch()
     {
           $from_date          = $_POST['from_date'];
           $date_to            = $_POST['date_to'];
           $html               = '';
           $table_id           = 'billing_batch';
           // $table_header       = array("Batch Number","Date From","Date To","Invoice Number","Action");           
           // $html              .= $this->simplify->populate_header_table($table_id,$table_header);

           $html              .= '
                                     <table id="'.$table_id .'" class="table table-bordered table-hover">
                                        <thead>
                                             <tr>
                                                  <th style="text-align:center;">Batch Number</th>     
                                                  <th style="width:73px;text-align:center;">Date From</th>     
                                                  <th style="width:52px;text-align:center;">Date To</th>     
                                                  <th style="width:250px;text-align:center;">Invoice Number</th>     
                                                  <th style="text-align:center;">Action</th>     
                                             </tr>
                                        </thead>
                                        <tbody>                       
                                 ';

           $batch_list         = $this->Cupon_mod->get_promo_billing_batch('','','');

           foreach($batch_list as $batch)
           {

                $batch_number_arr = array();
                $cupon_details    = $this->Cupon_mod->get_cupon_data_per_batch($batch['batch_id']);

                foreach($cupon_details as $cup)
                {

                      $conn = $this->Cupon_mod->get_connection($cup['db_id']);    
                      foreach($conn as $con)
                      {
                          if(!in_array($con['store']."-".$batch['department']."-".$batch['batch_id'],$batch_number_arr))
                          {
                              array_push($batch_number_arr,$con['display_name']."-".$batch['department']."-".$batch['batch_id']);
                          }
                           
                      }

                }

                $batch_number = '';
                for($a=0;$a<count($batch_number_arr);$a++) 
                {
                     $batch_number =  $batch_number_arr[$a]."<br>";
                }


                if($batch['paid_invoice_number'] == '')
                {
                     if($_SESSION['access_type'] == 'accounting' &&  !in_array($batch['mpdi-date-extracted'],array('0000-00-00','')) )
                     {
                          $invoice_number = '<input type="text" id="roundText" class="form-control round  invoice-'.$batch['batch_id'].'" placeholder="Input Invoice Number">';
                          $button         = '<div style="display: flex;">
                                                  <button type="button" onclick="post_billing('."'".$batch['batch_id']."'".')" class="btn btn-primary btn-sm" style="margin-left: 5px;">                                       
                                                    <span class="d-none d-sm-block">POST</span>
                                                  </button>';                                       
                     }
                     else 
                     {
                          $invoice_number = '<strong>FOR BILLING<strong>';
                          // $button         = '<div style="display: flex;">
                          //                         <button type="button" onclick="extract_billing('."'".$batch['batch_id']."'".')" class="btn btn-primary btn-sm" style="margin-left: 5px;">                                       
                          //                           <span class="d-none d-sm-block">EXTRACT</span>
                          //                         </button>';
                          $button = '';
                     }

                   
                }
                else 
                {
                    $invoice_number = $batch['paid_invoice_number'];
                    $button         = '';
                }


                if($_SESSION['access_type'] == 'accounting')
                {                    
                     $print_button = '     <button type="button" onclick="print_billing('."'".$batch['batch_id']."'".')" class="btn btn-danger btn-sm" style="margin-left: 5px;">                                       
                                             <span class="d-none d-sm-block">PRINT</span>
                                           </button>
                                       </div>';
                }
                else 
                {
                    $print_button = '';     
                }




                $row    = array(
                                 $batch_number,
                                 date('m/d/Y',strtotime(date($batch['from_date']))),
                                 date('m/d/Y',strtotime(date($batch['to_date']))), 
                                 $invoice_number,
                                 $button.$print_button
                               );
                $style  = array(
                                 "text-align:center;font-family: sans-serif;color:black;",
                                 "text-align:center;font-family: sans-serif;color:black;",
                                 "text-align:center;font-family: sans-serif;color:black;",  
                                 "text-align:center;font-family: sans-serif;color:black;",
                                 "text-align:center;font-family: sans-serif;color:black;"
                               );


                $tr_class ='tr_';
                $html  .= $this->simplify->populate_table_rows($row,$style,$tr_class);
           }


           $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#'.$table_id.'").dataTable({                            
                                         "order":[0,"asc"]
                                       });

                             </script>';

           $data['html'] = $html;           

           echo json_encode($data);
     }


     function generate_variance_report()
     {
           $allRowsData  = json_decode($_POST['allRowsData'], true);
           $from_date    = $_POST['from_date'];
           $date_to      = $_POST['date_to'];
           $this->ppdf   = new TCPDF();
           $this->ppdf->SetTitle("Variance Report (Inhouse vs. Navision)");            
           $this->ppdf->SetMargins(5, 15, 5, true);          
           $this->ppdf->setPrintHeader(false);
           $this->ppdf->SetFont('', '', 10, '', true);                    
           $this->ppdf->AddPage("P");
           $this->ppdf->SetAutoPageBreak(true);  

           $border       = 0;
           $header_style = 'text-align:center;font-weight: bold;font-size:10px;color:black;'; 
           $tbl          = ' 
                               <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: center;font-size:20px;color:black;">
                                    <tr>
                                        <td>
                                             <strong>Variance Report (Inhouse vs. Navision)</strong>
                                        </td>
                                    </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: left;font-size:12px;color:black;">
                                   <tr>
                                        <td>
                                             <strong>From:</strong>'.date('M d, Y',strtotime(date($from_date))).'
                                        </td>                                      
                                   </tr>     
                                   <tr>
                                        <td>
                                             <strong>To:</strong>'.date('M d, Y',strtotime(date($date_to))).'
                                        </td>
                                   </tr>                            
                                   <tr>
                                        <td>
                                        </td>
                                   </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="1"   >
                                   <tr>                                  
                                          <th style="'.$header_style.'width:89px;">Transaction Date</th>
                                          <th style="'.$header_style.'width:55px;">Order No.</th>
                                          <th style="'.$header_style.'width:135px;">Discount (MUDC)</th>                                               
                                          <th style="'.$header_style.'width:135px;">DISCOUNT(Nav)</th>                                   
                                          <th style="'.$header_style.'width:135px;">Variance</th>                                                                             
                                   </tr>
                          ';  


            $data_style = 'text-align:left;';       
            foreach($allRowsData  as $data)
            {
                     $tbl .= '
                                   <tr>
                                         <td style="text-align:center;">'.$data[0].'</td>
                                         <td style="text-align:center;">'.$data[1].'</td>
                                         <td style="text-align:right;">'.$data[2].'</td>
                                         <td style="text-align:right;">'.$data[3].'</td>
                                         <td style="text-align:right;">'.$data[4].'</td>                                                                                                                                                                                                         
                                   </tr>
                             ';
            }       
               



           $tbl .= '</table>';                            



           $this->ppdf->writeHTML($tbl, true, false, false, false, '');
           

           ob_end_clean();
           $this->ppdf->Output();                          
     }







     function generate_eom_report()
     {
           $allRowsData  = json_decode($_POST['allRowsData'], true);
           $from_date    = $_POST['from_date'];
           $date_to      = $_POST['date_to'];
           $db_id        = $_POST['db_id']; 


           $this->ppdf = new TCPDF();
           $this->ppdf->SetTitle("End of Month (EOM) Liquidation Report");            
           $this->ppdf->SetMargins(5, 15, 5, true);
           //$this->ppdf->SetMargins(5, 15, 0.20, true);
           $this->ppdf->setPrintHeader(false);
           $this->ppdf->SetFont('', '', 10, '', true);                    
           $this->ppdf->AddPage("L");
           $this->ppdf->SetAutoPageBreak(true);


           if($db_id == 'all')
           {
               $store = "All Stores";
           }
           else 
           {
               $db_details = $this->Cupon_mod->get_connection($db_id);
               $store = $db_details [0]['display_name'];
           }


           $count_trans = $this->Cupon_mod->count_transactions($db_id,$from_date,$date_to);


             $border       = 0;
           $header_style = 'text-align:center;font-weight: bold;font-size:10px;color:black;'; 
           $tbl          = ' 
                               <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: center;font-size:20px;color:black;">
                                    <tr>
                                        <td>
                                             '.$store.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                             <strong>End of Month (EOM) Liquidation Report</strong>
                                        </td>
                                    </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: left;font-size:12px;color:black;">
                                   <tr>
                                        <td>
                                             <strong>From:</strong>'.date('M d, Y',strtotime(date($from_date))).'
                                        </td>                                      
                                   </tr>     
                                   <tr>
                                        <td>
                                             <strong>To:</strong>'.date('M d, Y',strtotime(date($date_to))).'
                                        </td>
                                   </tr>                            
                                   <tr>
                                        <td>
                                        </td>
                                   </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="1"   >
                                   <tr>                                  
                                          <th style="'.$header_style.'width:55px;">BRANCH NAME</th>
                                          <th style="'.$header_style.'width:55px;">DATE</th>
                                          <th style="'.$header_style.'width:50px;">TIME</th>
                                          <th style="'.$header_style.'width:290px;">PRODUCT NAME(SKU)</th>     
                                          <th style="'.$header_style.'width:55px;">DISCOUNT</th>                                   
                                          <th style="'.$header_style.'width:55px;">DISCOUNT AMOUNT</th>                                   
                                          <th style="'.$header_style.'width:80px;">QTY PURCHASED</th>                                   
                                          <th style="'.$header_style.'width:70px;">RECEIPT</th>                                                                             
                                          <th style="'.$header_style.'width:90px;">PROMO CODE</th>                                                                             
                                   </tr>
                          ';

            $data_style = 'text-align:left;';   
            $total_disc_amount =0.00;    
            foreach($allRowsData  as $data)
            {

                     $total_disc_amount += str_replace(",",'',$data[5]);

                     $tbl .= '
                                   <tr>
                                         <td style="text-align:center;">'.$data[0].'</td>
                                         <td style="text-align:center;">'.$data[1].'</td>
                                         <td style="text-align:center;">'.$data[2].'</td>
                                         <td style="'.$data_style.'">'.$data[3].'</td>
                                         <td style="text-align:right;">'.$data[4].'</td>
                                         <td style="text-align:right;">'.$data[5].'</td>
                                         <td style="text-align:right;">'.$data[6].'</td>
                                         <td style="text-align:right;">'.$data[7].'</td>
                                         <td style="text-align:right;">'.$data[8].'</td>
                                   </tr>
                             ';
            }       


            $tbl .= '</table>'; 

            $tbl .= '
                         <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: left;font-size:12px;color:black;">  
                                   <tr>
                                        <td></td>
                                   </tr>    
                                   <tr>
                                        <td></td>
                                   </tr>                               
                         </table>          
                         <table cellspacing="1" cellpadding="" border="'.$border.'"  style="text-align: left;font-size:12px;color:black;">  
                                   <tr>
                                        <td style="width:500px;"></td>
                                        <td style="width:180px;"><h3>Total Discounted Amount:</h3></td>
                                        <td style="width:100px;">'.number_format($total_disc_amount,2).'</td>
                                   </tr>
                                        <td style="width:500px;"></td>
                                        <td style="width:180px;"><h3>Total Transactions:</h3></td>
                                        <td style="width:100px;">'.number_format($count_trans[0]['total']).'</td>
                                   <tr>
                                   </tr>
                         </table>
                              ';                           



           $this->ppdf->writeHTML($tbl, true, false, false, false, '');
           

           ob_end_clean();
           $this->ppdf->Output();
     }    













     function generate_eod_report()
     {
           $allRowsData  = json_decode($_POST['allRowsData'], true);
           $from_date     = $_POST['from_date'];
           $this->ppdf = new TCPDF();
           $this->ppdf->SetTitle("End of Day (EOD) Liquidation Report");            
           $this->ppdf->SetMargins(5, 15, 5, true);
           //$this->ppdf->SetMargins(5, 15, 0.20, true);
           $this->ppdf->setPrintHeader(false);
           $this->ppdf->SetFont('', '', 10, '', true);                    
           $this->ppdf->AddPage("L");
           $this->ppdf->SetAutoPageBreak(true);


           $border       = 0;
           $header_style = 'text-align:center;font-weight: bold;font-size:10px;color:black;'; 
           $tbl          = ' 
                               <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: center;font-size:20px;color:black;">
                                    <tr>
                                        <td>
                                             <strong>End of Day (EOD) Liquidation Report</strong>
                                        </td>
                                    </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: left;font-size:12px;color:black;">
                                   <tr>
                                        <td>
                                             <strong>Date:</strong>'.date('M d, Y',strtotime(date($from_date))).'
                                        </td>
                                   </tr>                                 
                                   <tr>
                                        <td>
                                        </td>
                                   </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="1"   >
                                   <tr>                                  
                                          <th style="'.$header_style.'width:55px;">BRANCH NAME</th>
                                          <th style="'.$header_style.'width:55px;">DATE</th>
                                          <th style="'.$header_style.'width:50px;">TIME</th>
                                          <th style="'.$header_style.'width:290px;">PRODUCT NAME(SKU)</th>     
                                          <th style="'.$header_style.'width:55px;">DISCOUNT</th>                                   
                                          <th style="'.$header_style.'width:55px;">DISCOUNT AMOUNT</th>                                   
                                          <th style="'.$header_style.'width:80px;">QTY PURCHASED</th>                                   
                                          <th style="'.$header_style.'width:70px;">RECEIPT</th>                                                                             
                                          <th style="'.$header_style.'width:90px;">PROMO CODE</th>                                                                             
                                   </tr>
                          ';



            $data_style = 'text-align:left;';       
            $total_disc_amount =0.00;    
            foreach($allRowsData  as $data)
            {
                $total_disc_amount += str_replace(",",'',$data[5]);
                $tbl               .= '
                                             <tr>
                                                   <td style="text-align:center;">'.$data[0].'</td>
                                                   <td style="text-align:center;">'.$data[1].'</td>
                                                   <td style="text-align:center;">'.$data[2].'</td>
                                                   <td style="'.$data_style.'">'.$data[3].'</td>
                                                   <td style="text-align:right;">'.$data[4].'</td>
                                                   <td style="text-align:right;">'.$data[5].'</td>
                                                   <td style="text-align:right;">'.$data[6].'</td>
                                                   <td style="text-align:right;">'.$data[7].'</td>
                                                   <td style="text-align:right;">'.$data[8].'</td>
                                             </tr>
                                       ';
            }       


            $tbl .= '</table>';                            



            $tbl .= '
                         <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: left;font-size:12px;color:black;">  
                                   <tr>
                                        <td></td>
                                   </tr>                                  
                         </table>          
                         <table cellspacing="1" cellpadding="" border="'.$border.'"  style="text-align: left;font-size:12px;color:black;">  
                                   <tr>
                                        <td style="width:500px;"></td>
                                        <td style="width:180px;"><h3>Total Discounted Amount:</h3></td>
                                        <td style="width:100px;">'.number_format($total_disc_amount,2).'</td>
                                   </tr>
                         </table>
                              ';   

           $this->ppdf->writeHTML($tbl, true, false, false, false, '');
           

           ob_end_clean();
           $this->ppdf->Output();

     }


     function generate_Monitoring_List_report()
     {
           $allRowsData  = json_decode($_POST['allRowsData'], true);
           $from_date    = $_POST['from_date'];
           $date_to      = $_POST['date_to'];
           $db_id        = $_POST['db_id']; 
           

           if($db_id == 'all')
           {
               $store = "All Stores";
           }
           else 
           {
               $db_details = $this->Cupon_mod->get_connection($db_id);
               $store = $db_details [0]['display_name'];
           }




           $this->ppdf = new TCPDF();
           $this->ppdf->SetTitle("Monitoring List for Discounted Items");            
           $this->ppdf->SetMargins(5, 15, 5, true);
           //$this->ppdf->SetMargins(5, 15, 0.20, true);
           $this->ppdf->setPrintHeader(false);
           $this->ppdf->SetFont('', '', 10, '', true);                    
           $this->ppdf->AddPage("P");
           $this->ppdf->SetAutoPageBreak(true);


           $border       = 0;
           $header_style = 'text-align: center;font-weight: bold;font-size:12px;color:black;'; 
           $tbl    = ' 
                          <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: center;font-size:20px;color:black;">
                               <tr>
                                   <td>
                                        '.$store.'
                                   </td>
                               </tr>
                               <tr>
                                   <td>
                                        <strong>Monitoring List for Discounted Items</strong>
                                   </td>
                               </tr>
                          </table>
                          <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="text-align: left;font-size:12px;color:black;">
                              <tr>
                                   <td>
                                        <strong>From:</strong>'.date('M d, Y',strtotime(date($from_date))).'
                                   </td>
                              </tr>
                              <tr>
                                   <td>
                                        <strong>To:</strong>'.date('M d, Y',strtotime(date($date_to))).'
                                   </td>
                              </tr>
                              <tr>
                                   <td>
                                   </td>
                              </tr>
                          </table>
                          <table cellspacing="1" cellpadding="1" border="1"   >
                              <tr>                                  
                                     <th style="'.$header_style.'width:55px;">Total Quantity</th>
                                     <th style="'.$header_style.'width:40px;">Items No</th>
                                     <th style="'.$header_style.'width:250px;">Brand Name</th>
                                     <th style="'.$header_style.'width:180px;">Generic Name</th>     
                                     <th style="'.$header_style.'width:36px;">UOM</th>                                   
                              </tr>
                     ';

              $data_style = 'text-align:center';       
              foreach($allRowsData  as $data)
              {
                     $tbl .= '
                                   <tr>
                                         <td style="'.$data_style.'">'.$data[0].'</td>
                                         <td style="'.$data_style.'">'.$data[1].'</td>
                                         <td style="'.$data_style.'">'.$data[2].'</td>
                                         <td style="'.$data_style.'">'.$data[3].'</td>
                                         <td style="'.$data_style.'">'.$data[4].'</td>
                                   </tr>
                             ';
              }       


            $tbl .= '</table>';         

           $this->ppdf->writeHTML($tbl, true, false, false, false, '');
           

           ob_end_clean();
           $this->ppdf->Output();
     }


     function submit_order()
     {
           $fname         = $_POST['fname'];
           $lname         = $_POST['lname'];
           $promo_code    = $_POST['promo_code'];
           $allRowsData   = json_decode($_POST['allRowsData'], true);
           $fname         = $_POST['fname'];
           $order_number  = $_POST['order_number'];
           $phone_no      = $_POST['phone_no'];
           $year          = $_POST['year'];



           $search_promo = $this->Cupon_mod->search_promo_code($promo_code);
           if(empty($search_promo) && isset($_SESSION['db_id']))
           {
               $cupon_id    = $this->Cupon_mod->insert_cupon_data($fname,$lname,$promo_code,$order_number,$phone_no,$year);
               foreach ($allRowsData as $rowData) 
               {                   

                      $this->Cupon_mod->insert_item_transact($rowData,$cupon_id);   
                      //  $itemCode = $rowData[0];
                      //  $itemName = $rowData[1];
                      //  $quantity = $rowData[2];
                      //  $price = $rowData[3];
                      //  $discount = $rowData[4];
                      //  $discounted_price = $rowData[5];

                      // echo  $itemCode.'--->'.$itemName.'---->'.$quantity.'----->'.$price.'---->'.$discount.'---->'.$discounted_price."<br>";
               }
           }    


           $this->ppdf = new TCPDF('P', 'mm', array(80, 297), true, 'UTF-8', false);
           $this->ppdf->SetTitle("Medplus Cuponing");           
           //$this->ppdf->SetMargins(5, 15, 0.20, true);
           $this->ppdf->SetMargins(5, 15,5, true);
           $this->ppdf->setPrintHeader(false);
           $this->ppdf->SetFont('', '', 10, '', true);
           $this->ppdf->AddPage();

         

          $this->ppdf->SetAutoPageBreak(true);

          // $counter = 0;
          // for($a=0;$a<1000;$a++)
          // {
          //      $tbl .= $a."<br>";
               
                    
          // }

          
           $amount_due         = 0.00;
           $counter            = 0;
           $border             = 0;
           $no_items           = 0;
           $total_disc_Availed = 0;
           //$bold    = 'font-weight: bold;';
           
           // $tbl     = '   
           //                <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:7px;color:black;">
           //                  <tr>
           //                      <td><strong>Ordering no.:</strong>'.$order_number.'</td>
           //                  </tr>
           //                </table>                            
           //                <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:7px;color:black;">
           //                  <tr>
           //                         <th style="width:100px;"><strong>Date:</strong>'.date('d/m/Y').'</th>                           
           //                         <th><strong>Promo Code:</strong>'.$promo_code.'</th>                                    
           //                  </tr>
           //                </table>
           //               <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:7px;color:black;">  
           //                  <tr>
           //                         <td><strong>Customer Name:</strong>'.$fname.' '.$lname.'</td>                                  
           //                  </tr>
           //               </table>   
           //               <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:7px;color:black;">  
           //                  <tr>
           //                        <td style="width:66px;"><strong>Description</strong></td> 
           //                        <td></td>
           //                        <td><strong>Amount</strong></td>
           //                  </tr>  
           //                  <tr>
           //                         <td></td>
           //                         <td></td>
           //                         <td></td>
           //                  </tr>
           //               </table>    
                        
           //                  '; 

            $tbl     = '   
                          <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">
                             <tr>
                                   <td style="text-align:center;"><strong>PATIENT COMPLIANCE</strong></td>
                             </tr>
                             <tr>
                                   <td></td>
                             </tr>
                          </table>    
                          <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">
                            <tr>
                                <td><strong>Ordering no.:</strong>'.$order_number.'</td>
                            </tr>
                          </table>                            
                          <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">
                            <tr>
                                   <th style="width:80px;"><strong>Date:</strong>'.date('d/m/Y').'</th>                           
                                   <th style="width:150px;"><strong>Transaction Code:</strong>'.$promo_code.'</th>                                    
                            </tr>
                          </table>
                         <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">  
                            <tr>
                                   <td><strong>Customer Name:</strong>'.$fname.' '.$lname.'</td>                                  
                            </tr>
                         </table>   
                         <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">  
                            <tr>
                                  <td style="width:70px;text-align:center;"><strong>Description</strong></td> 
                                  <td></td>
                                  <td></td>
                            </tr>                           
                         </table>    
                         <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">  
                            <tr>
                                   <td style="width:33px;"><strong>Item Code</strong></td>
                                   <td style="width:23px;"><strong>Qty</strong></td>
                                   <td style="width:23px;"><strong>UOM</strong></td>
                                   <td style="width:23px;text-align:right;"><strong>Disc</strong></td>
                                   <td style="width:34px;text-align:center;"><strong>Net  Amt.</strong></td>
                                   <td style="width:38px;text-align:center;"><strong>Disc Amt.</strong></td>
                            </tr>  
                         </table>    
                            ';


                                         
                                         
                                          
                                         


           //var_dump($allRowsData);                 
           foreach ($allRowsData as $rowData) 
           {                   

                 // $this->Cupon_mod->insert_item_transact($rowData,$cupon_id);   

                  $itemCode = $rowData[0];
                  $itemName = $rowData[1];
                  $uom      = $rowData[2];
                  $quantity = $rowData[3];
                  $price    = $rowData[4];
                  $discount = $rowData[5];
                  $discounted_price = $rowData[6];
                  $vatable          = $rowData[7];  
                  $dis_or_no        = $rowData[8];




                  $item_details = $this->Cupon_mod->search_item_by_item_code($itemCode);

                  // $tbl .= '   <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">  
                  //                  <tr>
                  //                        <td style="width:119px;">'.$itemName.'</td>    
                  //                        <td style="width:65px;">'.$vatable.'</td>
                  //                  </tr>
                  //             </table>
                  //             <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;"> 
                  //                  <tr>
                  //                        <td style="width:10px;"></td>
                  //                        <td style="width:40px;">'.$itemCode.'</td>
                  //                        <td>'.$quantity.' '.$uom.'</td>  
                  //                        <td style="width:45px;"><strong>Disc:</strong>'.$discount.'</td>  
                  //                        <td style="width:40px;">';
                  $tbl .= '   <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">  
                                   <tr>
                                         <td style="width:142px;">'.$itemName.'</td>    
                                         <td style="width:65px;">'.$vatable.'</td>
                                   </tr>
                              </table>
                              <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;"> 
                                   <tr>                                         
                                         <td style="width:33px;">'.$itemCode.'</td>
                                         <td style="width:23px;text-align:center;">'.$quantity.'</td> 
                                         <td style="width:23px;text-align:center;">'.$uom.'</td> 
                                         <td style="width:23px;text-align:right;">'.$discount.'</td>  
                                         <td style="width:34px;text-align:right;">';


                                         if($dis_or_no == 'NO DISC')
                                         {
                                              $final_price  = ($quantity * $price);                                              
                                              $disc_line    = number_format('0.00',2);
                                              $tbl         .= number_format($final_price,2);

                                              $amount_due += $final_price;
                                         }
                                         else 
                                         {
                                              
                                              $disc_line           = round(($quantity * ($price * $discount)),2);
                                              $total_disc_Availed += str_replace(',','',$disc_line);
                                              //$disc_line           = number_format($disc_line,2);
                                              //$total_disc_Availed += $disc_line;

                                              $final_price         = ($quantity * $price) - $disc_line;
                                              $amount_due         += $final_price;

                                              $tbl                .= number_format($final_price,2);                                              
                                         }                      

                   $tbl .=               '</td> 
                                          <td style="width:36px;text-align:right;">
                                              '.number_format($disc_line,2).'
                                          </td>            
                                         <td style="width:25px;font-size:6px;"><strong>'.$dis_or_no.'</strong></td>
                                   </tr>
                              </table>
                          ';
                                   
                                    // <td>".$quantity."</td>    
                                    // <td>".$price."</td>    
                                   
                                    

                $discounted_price = str_replace(",",'',$discounted_price);          
                $total_gross     += ($quantity * $price);         
                $no_items        += $quantity;
                 // echo  $itemCode.'--->'.$itemName.'---->'.$quantity.'----->'.$price.'---->'.$discount.'---->'.$discounted_price."<br>";
          }

          $tbl .= '
                      <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;"> 
                          <tr>
                                <td></td>
                                <td></td>
                                <td></td> 
                          </tr>     
                           <tr>
                                <td style="width:45px;"></td>
                                <td style="width:87px;"><strong>No. of items:</strong></td>
                                <td style="text-align:right;width:50px;">'.$no_items.'</td> 
                          </tr>    
                           <tr>
                                <td></td>   
                                <td style="width:87px;"><strong>Total Gross:</strong></td>
                                <td style="text-align:right;width:50px;">'.number_format($total_gross,2).'</td>
                          </tr>
                          <tr>
                                <td></td>   
                                <td style="width:87px;"><strong>Total Disc Availed:</strong></td>
                                <td style="text-align:right;width:50px;">'.number_format($total_disc_Availed,2).'</td>
                          </tr>
                       </table>   ';
          $this->ppdf->writeHTML($tbl, true, false, false, false, '');   

          $total_disc_Availed = strval(round($total_disc_Availed,2)); 
          
          $this->ppdf->write1DBarcode($total_disc_Availed, 'C128', '', '', '', 12, 1.3, $style = '', 'N');

          $tbl   =' <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:9px;color:black;">                                  
                          <tr>
                                <td style="width:45px;"></td>   
                                <td style="width:87px;"><strong>Total Net Amount:</strong></td>
                                <td style="text-align:right;width:50px;">'.number_format($amount_due,2).'</td>
                          </tr>                         
                      </table>
                  ';

          // $tbl .= '
          //             <table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:7px;color:black;"> 
          //                 <tr>
          //                       <td></td>
          //                       <td></td>
          //                       <td></td> 
          //                 </tr>     
          //                  <tr>
          //                       <td></td>
          //                       <td><strong>No. of items:</strong></td>
          //                       <td>'.$no_items.'</td> 
          //                 </tr>    
          //                 <tr>
          //                       <td></td>   
          //                       <td><strong>Total Disc Availed:</strong></td>
          //                       <td>'.$total_disc_Availed.'</td>
          //                 </tr>
          //             </table>
          //         ';
                   // <td><strong>Amount Due:</strong></td>
                   // <td>'.number_format($amount_due,2).'</td>

          $this->ppdf->writeHTML($tbl, true, false, false, false, '');

          $this->ppdf->write1DBarcode($promo_code, 'C128', '', '', '', 12, 1.3, $style = '', 'N');

          //$this->ppdf->write1DBarcode($promo_code, 'C128', '', '', '', 12, 1.3, $style = '', 'N'); //12 width , 1.3 height

          ob_end_clean();
          $this->ppdf->Output();
 
          // Close the preview window after printing
        // echo "<script>setTimeout(function() { window.close(); }, 1000);</script>";


          //$printer_name = "EPSON TM-H6000V Receipt5"; // Replace with the name of your default printer
          // $printer_name = 'smb://172.16.43.168/EPSON TM-H6000V Receipt5';
          // $text_to_print = "This is the text to be printed.\nThis is a new line.";
          // $temp_file     = tempnam(sys_get_temp_dir(), 'print-');

          // file_put_contents($temp_file, $text_to_print); 
          
          // $print_command = "lp -d {$printer_name} {$temp_file}";
          // // $print_command = "print \"{$temp_file}\""; // Use the default printer
          // $result        = shell_exec($print_command); 
          // unlink($temp_file);

          // $printer_name = "EPSON TM-H6000V Receipt5"; // Replace with the name of your printer
          // $handle = printer_open($printer_name); // Open a connection to the printer
          // if ($handle) {
          //   // Send some text to the printer
          //   printer_start_doc($handle, "My Document");
          //   printer_start_page($handle);
          //   printer_write($handle, "Hello, world!");
          //   printer_end_page($handle);
          //   printer_end_doc($handle);

          //   printer_close($handle); // Close the printer connection
          // } else {
          //   echo "Failed to connect to printer.";
          // }

            
     }



     function extract_file()
     {
           $memory_limit = ini_get('memory_limit');
           ini_set('memory_limit',-1);
           ini_set('max_execution_time', 0);

           if(count($_FILES['files']['name']) == 2)
           {

                $transaction_arr   = array();
                $trans_payment_arr = array();

                for($i=0; $i<count($_FILES['files']['name']); $i++)
                {
                     $RESS2 = ''; 
                     if(!empty($_FILES["files"]["name"]))
                     {
                          $PDFfileName = basename($_FILES["files"]["name"][$i]); 
                          $PDFfileType = pathinfo($PDFfileName, PATHINFO_EXTENSION); 
                          $allowTypes  = array('txt'); 

                          if(in_array($PDFfileType, $allowTypes))
                          {
                                if(!empty($_FILES['files']['tmp_name'])):
                         
                                      $fileName = $_FILES['files']['tmp_name'][$i];                
                                      $file     = fopen($fileName,"r") or exit("Unable to open file!");
                                      while(!feof($file)) 
                                      {
                                          @$RESS2 .= fgets($file). "";
                                      }
                                endif; 
                                $ress_sanitize = explode(PHP_EOL, $RESS2);    
                               //$kuhag_double_qoute=preg_replace('/"/', "", $ress_sanitize );

                                for($a=0;$a<count($ress_sanitize);$a++)
                                {
                                       $row_explode = explode('","',$ress_sanitize[$a]);                                 
                                       // kuhaon ang "  ug , sa ani nga array $row_explode -----------------------
                                       $pattern = '/["\,]/';  //<-------pampakuha sa double quote(") ug comma (,)
                                       $cleaned_row_arr  = array_map(function($value) use ($pattern) 
                                       {
                                              return preg_replace($pattern, '', $value);
                                       }, $row_explode);
                                        // -----------------------------------------------------------------------
                                       //var_dump($cleaned_row_arr); 
                                     
                                       if(count($cleaned_row_arr) == 74) 
                                       {
                                               //$response       = 'success';
                                               $trans_line_arr =  array(
                                                                           "transaction_no" => $cleaned_row_arr[0], 
                                                                           "receipt_no"     => $cleaned_row_arr[2], 
                                                                           "store"          => $cleaned_row_arr[4],
                                                                           "ordering_no"    => $cleaned_row_arr[53],
                                                                           "discount_amount"=> $cleaned_row_arr[21] 
                                                                       );

                                               array_push($transaction_arr,$trans_line_arr);

                                       }
                                       else 
                                       if(count($cleaned_row_arr) == 32) 
                                       {
                                              $payment_line_arr = array(
                                                                         "transaction_no"  => $cleaned_row_arr[0], 
                                                                         "receipt_no"      => $cleaned_row_arr[2], 
                                                                         "tender_type"     => $cleaned_row_arr[6], 
                                                                         "amount_tendered" => $cleaned_row_arr[7]
                                                                      );
                                               array_push($trans_payment_arr,$payment_line_arr);
                                               // $response = 'success';
                                       }

                                           //  $receipt_no       = $cleaned_row_arr[2];
                                           //  $store            = $cleaned_row_arr[4];
                                           //  $ordering_no      = $cleaned_row_arr[53];
                                           //  $discount_amount  = $cleaned_row_arr[21];

                                           //  $login_details    = $this->Cupon_mod->get_user_connection();
                                           //  $database_details = $this->Cupon_mod->get_connection($login_details[0]['db_id']);
                                           //  $db_id            = $database_details[0]['db_id'];


                                           // // $this->Cupon_mod->update_cupon_data($column_name_arr,$column_value_arr,$ordering_no,$db_id);

                                           //  if(strstr($store,$database_details[0]['store']) ) //if ang user nga nag login kay allowed ani nga texfile nga mag upload.....  example: if ang nag login nga user kay ICM iyang store nga allowed so dapat ICM ra pud nga store nga textfile ang pwede nya ma upload
                                           //  {                                       
                                           //       $column_name_arr  = array("receipt_no","nav_discount");
                                           //       $column_value_arr = array($receipt_no,$discount_amount);
                                           //       $this->Cupon_mod->update_cupon_data($column_name_arr,$column_value_arr,$ordering_no,$db_id);
                                           //  }


                                }
                                
                          }
                          else
                          { 
                               $response = 'only textfile is allowed to upload.'; 
                          }     
                     }  
                }

                if(count($transaction_arr) == 0)
                {
                     $response = 'Transaction textfile is missing, please upload it together with Transaction Payment Entry textfile';
                }
                else 
                if(count($trans_payment_arr) == 0)
                {
                     $response = 'Transaction Payment Entry  textfile is missing, please upload it together with Transaction textfile';

                }
                else 
                {
                     $response    = 'success';
                     $cup_details = $this->Cupon_mod->get_promo_list_details('');

                     for($a=0;$a<count($transaction_arr);$a++)
                     {
                          $transaction_no = $transaction_arr[$a]['transaction_no'];
                          $receipt_no     = $transaction_arr[$a]['receipt_no'];
                          $ordering_no    = $transaction_arr[$a]['ordering_no'];
                          $store          = $transaction_arr[$a]['store'];

                          foreach ($trans_payment_arr as $key => $value) 
                          {
                              
                               if($value['transaction_no'] == $transaction_no && $value['receipt_no'] == $receipt_no && !strstr($value['amount_tendered'],'-') && $value['tender_type'] == $cup_details[0]['tender_type']) 
                               {                                     

                                      $login_details    = $this->Cupon_mod->get_user_connection();
                                      $database_details = $this->Cupon_mod->get_connection($login_details[0]['db_id']);
                                      $db_id            = $database_details[0]['db_id'];
                                     
                                      if(strstr($store,$database_details[0]['store']) ) //if ang user nga nag login kay allowed ani nga texfile nga mag upload.....  example: if ang nag login nga user kay ICM iyang nga allowed so dapat ICM ra pud nga store nga textfile ang pwede nya ma upload
                                      {                    

                                            $discount_amount  = $value['amount_tendered'];                    
                                            $column_name_arr  = array("receipt_no","nav_discount",'transaction_no','status');
                                            $column_value_arr = array($receipt_no,$discount_amount,$value['transaction_no'],'unbilled');
                                            $this->Cupon_mod->update_cupon_data($column_name_arr,$column_value_arr,$ordering_no,$db_id);
                                      }                               
                               }
                          }                         
                     }
                }
                
           }
           else 
           if(count($_FILES['files']['name']) > 2)
           {
                $response = 'Two files only allowed to upload simultaneously. Transaction and Transaction Payment Entry';
           }   
           else  
           {
                $response = 'Transaction and Transaction Payment Entry should be uploaded simultaneously';
           }


           ini_set('memory_limit',$memory_limit );
           $data['response'] = $response;
           echo json_encode($data);
     }


     function process_billing()
     {
           $checked   = $_POST['checked'];
           $status    = $_POST['status'];
           $from_date = $_POST['from_date'];
           $to_date   = $_POST['date_to'];


           $new_batch_id = $this->Cupon_mod->insert_promo_billing_batch($from_date,$to_date);

           $column_name_arr  = array("status","batch_id");
           $column_value_arr = array('billed-acctg',$new_batch_id); 
           for($a=0;$a<count($checked);$a++)     
           {               
                $this->Cupon_mod->update_cupon_data_billing($column_name_arr,$column_value_arr,$checked[$a]);
           }

           $data['response'] = 'success';
           $data['batch_id'] = $new_batch_id;
          //var_dump($status,$checked);

           echo json_encode($data);
     }


     function update_brand_with_image()
     {
          $brandName = $this->input->post('brandName');
          $brand_id = $this->input->post('brand_id');
           

          if(isset($_FILES["file"]["name"]) &&  $_FILES["file"]["name"] != '' )
          {
                   $destination_path = getcwd()."./assets/cuponing/images/";
                   $target_path = $destination_path . basename( $_FILES["file"]["name"]);

                   $file_path   = 'assets/cuponing/images/'.basename( $_FILES["file"]["name"]);

                   @move_uploaded_file($_FILES['file']['tmp_name'], $target_path);           
                   
                   $update_data['image_logo_path'] = $file_path;
          }

          $table = "promo_brand";
          $update_data['brand_name'] = $brandName;
          $where['brand_id'] = $brand_id;
          $this->Cupon_mod->update($table,$update_data,$where);

              
          echo 'success';
          
     }



     // function upload_item()
     // {
     //      $promo_id = $this->input->post('promo_id');    
     //      if ($_SERVER['REQUEST_METHOD'] === 'POST')
     //       {
     //               if (isset($_FILES["files"]) && !empty($_FILES["files"]["name"][0])) {
     //                   $files = $_FILES["files"];

     //                   // Loop through all the uploaded files
     //                   for ($i = 0; $i < count($files['name']); $i++) 
     //                   {
     //                       $file_path = $files["tmp_name"][$i];
     //                       var_dump($file_path,$promo_id);

     //                       // // Check if the file is a CSV file (optional)
     //                       // $file_info = pathinfo($files["name"][$i]);
     //                       // $extension = strtolower($file_info["extension"]);

     //                       // if ($extension == "csv") {
     //                       //     // Process the CSV file
     //                       //     $destination = "uploads/" . $files["name"][$i];
     //                       //     move_uploaded_file($file_path, $destination);

     //                       //     // Additional processing or storing file information in a database can be done here
     //                       // } else {
     //                       //     echo "File " . $files["name"][$i] . " is not a valid CSV file. Skipping...";
     //                       // }
     //                   }

     //                   echo "Files uploaded successfully.";
     //               } else {
     //                   echo "No files were uploaded.";
     //               }
     //           } else {
     //               echo "Invalid request method.";
     //           }
     // }






     function upload_item()
     {
            $promo_id = $this->input->post('promo_id');   

           // if(isset($_FILES["file"]) && $_FILES["file"]["name"] != '') 
           // {
                    
                 if ($_SERVER['REQUEST_METHOD'] === 'POST')
                 {
                      if (isset($_FILES["files"]) && !empty($_FILES["files"]["name"][0])) 
                      {
                          $files = $_FILES["files"];

                          // Loop through all the uploaded files
                          for ($i = 0; $i < count($files['name']); $i++) 
                          {
                                     $file_path = $files["tmp_name"][$i];                                    



                                    // $file_path = $_FILES["file"]["tmp_name"];

                                    // Check if the file is a CSV file
                                    $file_info = pathinfo($files['name'][$i]);
                                    if(strtolower($file_info["extension"]) == "csv") 
                                    {
                                           // Get the content of the CSV file
                                           $csv_content = file_get_contents($file_path);

                                           // Explode the content into an array of lines using EOL
                                           $lines = explode(PHP_EOL, $csv_content);

                                           for($a=1;$a<count($lines);$a++)
                                           {
                                              $exp_row = explode(",",$lines[$a]);
                                              if(count($exp_row) == 1)
                                              {
                                                    $item_code= $lines[$a];

                                                   // Validate if $item_code contains only numbers and has exactly 6 digits
                                                   if (preg_match('/^[0-9]{6}$/', $item_code) && $item_code !='') 
                                                   {                                  
                                                         
                                                        $search_item = $this->Cupon_mod->search_promo_item($item_code,$promo_id);

                                                        $table = 'promo_item_list';


                                                         $item_data['item_code'] = $item_code;
                                                         $uom                      = $this->Cupon_mod->getItem_uom_Nav($item_code);
                                                         $item_data['promo_id']  = $promo_id;
                                                         $item_data['UOM']       = $uom;

                                                        if(empty($search_item))
                                                        {
                                                             $this->Cupon_mod->insert($table,$item_data);
                                                        }
                                                        else 
                                                        {
                                                            $where['item_id']  = $search_item[0]['item_id'];
                                                            $this->Cupon_mod->update($table,$item_data,$where);
                                                        }

                                                        $response    = 'success';
                                                   }
                                                   else
                                                   if($item_code !='')    
                                                   {
                                                       $response = "Invalid item code. It should contain only numbers and have exactly 6 digits.";
                                                       break;
                                                   }                                   
                                              }
                                              else 
                                              {
                                                   $response = 'Incorrect Selection of file';
                                                   break;
                                              }

                                           }

                                          

                                           // Now $csv_content contains the content of the CSV file
                                           // You can process the content as needed
                                           // Example: Display the content
                                     }
                                     else
                                     {
                                           echo "Invalid file type. Please upload a CSV file.";
                                     }

                                     if($response != 'success')
                                     {
                                        break;
                                     }
                           }

                           echo $response;

                            
                       }
                       else
                       {
                          echo "No files were uploaded.";
                       }
                  }
                  else
                  {
                     echo "Invalid request method.";
                  }
           // }
     }


     function save_brand()
     {      

        $brandName = $this->input->post('brandName');
        $search_brand =  $this->Cupon_mod->get_promo_brand_list($brandName);
        if(empty($search_brand))
        {

             if($_FILES["file"]["name"] != '')
             {
                   $destination_path = getcwd()."./assets/cuponing/images/";
                   $target_path = $destination_path . basename( $_FILES["file"]["name"]);

                   $file_path   = 'assets/cuponing/images/'.basename( $_FILES["file"]["name"]);

                   @move_uploaded_file($_FILES['file']['tmp_name'], $target_path);           
                   
             }

              $table = "promo_brand";
              $insert_data['brand_name'] = $brandName;
              $insert_data['image_logo_path'] = $file_path;

              $this->Cupon_mod->insert($table,$insert_data);
              echo 'success';
        }
        else 
        {
           echo "Brand Already Exists";
        }
     }




     function view_add_brand_modal()
     {

          $font_style = 'font-family: Arial, sans-serif;'; 
          $html ='
                     <div class="form-group" style="'.$font_style.'">                     
                          <label for="promoName">Brand Name:</label>
                          <input type="text" class="form-control" id="brandName" placeholder="Enter Brand Name" required>
                     </div> 
                     <div class="form-group" style="'.$font_style.'">
                     <label for="brand_file">Brand Image:</label>
                         <input type="file" class="btn"   name="files[]" id="brand_file" style="display: inline-block; padding: 1px;">   
                     </div>   


                     <script>
                                const brandFile = document.getElementById("brand_file");
                                brandFile.addEventListener("change", function() 
                                {
                                  const file = brandFile.files[0];
                                  if (!file || file.type.match(/^image\//)) 
                                  {
                                      // Valid image file selected
                                  }
                                  else
                                  {                                       
                                      Swal.fire({
                                                    icon: "error",
                                                    title: "error",
                                                    text: "Invalid file type. Please select an image file."                                  
                                 });    
                                      brandFile.value = ""; // Reset the input field
                                  }
                               });
                     </script>                      
                 ';

           $data['html'] = $html;

           $data["footer"] = '
                               <button type="button" class="btn btn-success ml-1" onclick="save_brand()">
                                     <i class="bx bx-check d-block d-sm-none"></i>
                                     <span class="d-none d-sm-block">save</span>
                               </button>

                               <button type="button" onclick="close_brand_modal()" class="btn btn-danger" data-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block ">Close</span>
                               </button>
                            ';


           echo json_encode($data);        
     }



     function update_brand_view()
     {
           $brand_id = $_POST['brand_id'];
           $brand_details = $this->Cupon_mod->get_promo_brand_by_id($brand_id);

          $font_style = 'font-family: Arial, sans-serif;'; 
          $html ='
                     <div class="form-group" style="'.$font_style.'">                     
                          <label for="promoName">Brand Name:</label>
                          <input type="text" class="form-control" id="brandName" value="'.$brand_details[0]['brand_name'].'" placeholder="Enter Brand Name" required>
                     </div> 
                     <div class="form-group" style="'.$font_style.'">
                     <label for="brand_file">Brand Image:</label>
                         <input type="file" class="btn"   name="files[]" id="brand_file" style="display: inline-block; padding: 1px;">   
                     </div>   


                     <script>
                                const brandFile = document.getElementById("brand_file");
                                brandFile.addEventListener("change", function() 
                                {
                                  const file = brandFile.files[0];
                                  if (!file || file.type.match(/^image\//)) 
                                  {
                                      // Valid image file selected
                                  }
                                  else
                                  {                                       
                                      Swal.fire({
                                                    icon: "error",
                                                    title: "error",
                                                    text: "Invalid file type. Please select an image file."                                  
                                 });    
                                      brandFile.value = ""; // Reset the input field
                                  }
                               });
                     </script>                      
                 ';

           $data['html'] = $html;

           $data["footer"] = '
                               <button type="button" class="btn btn-success ml-1" onclick="update_brand(\''.$brand_id.'\')">
                                     <i class="bx bx-check d-block d-sm-none"></i>
                                     <span class="d-none d-sm-block">save</span>
                               </button>

                               <button type="button" onclick="close_brand_modal()" class="btn btn-danger" data-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block ">Close</span>
                               </button>
                            ';


           echo json_encode($data);    
     }




     function add_promo_ui()
     {
          $brand = $_POST['brand'];
          $font_style = 'font-family: Arial, sans-serif;';
          $html = '

                       <div class="form-group" style="'.$font_style.'">
                          <input id="brand_id" value="'.$brand.'" hidden>
                          <label for="promoName">Promo Name:</label>
                          <input type="text" class="form-control" id="promoName" placeholder="Enter Promo Name" required>
                       </div>
                       <div class="form-group" style="'.$font_style.'">
                          <label for="dateFrom">Date From:</label>
                          <input type="date" class="form-control" id="dateFrom" required onchange="validateForm()">
                       </div>
                       <div class="form-group" style="'.$font_style.'">
                          <label for="dateTo">Date To:</label>
                          <input type="date" class="form-control" id="dateTo" required onchange="validateForm()" >
                       </div>
                       <div class="form-group" style="'.$font_style.'">
                          <label for="tenderType">Tender Type:</label>
                          <input type="number" class="form-control" id="tenderType" placeholder="Enter Tender Type (Numbers only)" required>
                       </div>
                       <div class="form-group" style="'.$font_style.'">
                          <label for="quantity">Quantity:</label>
                          <input type="number" class="form-control" id="quantity" placeholder="Enter Tender Quantity" required>
                       </div>
                       <div class="form-group" >
                         <label for="percentage">(VAT)Percentage:</label>
                         <div class="input-group" style="'.$font_style.'">
                            <input type="number" class="form-control" id="percentage" placeholder="Enter Percentage (VAT" required>
                            <div class="input-group-append">
                              <span class="input-group-text">%</span>
                            </div>
                          </div>
                      </div>
                      <div class="form-group" >
                         <label for="no_vat_percentage">(NO VAT)Percentage:</label>
                         <div class="input-group" style="'.$font_style.'">
                            <input type="number" class="form-control" id="no_vat_percentage" placeholder="Enter Percentage(NO VAT)" required>
                            <div class="input-group-append">
                              <span class="input-group-text">%</span>
                            </div>
                          </div>
                      </div>

                      <script>
                           // Add event listener to format the percentage on input
                           var timeout;
                           document.getElementById("percentage").addEventListener("input", function()
                           {
                             clearTimeout(timeout); // Clear the previous timeout
                             timeout = setTimeout(function() 
                             {
                               var currentValue = $("#percentage").val();
                               if (!isNaN(currentValue)) 
                               {
                                 $("#percentage").val(parseFloat(currentValue).toFixed(2));
                               }
                               console.log("User inputted percentage:", $("#percentage").val());
                             }, 2000);
                           });


                           document.getElementById("no_vat_percentage").addEventListener("input", function()
                           {
                             clearTimeout(timeout); // Clear the previous timeout
                             timeout = setTimeout(function() 
                             {
                               var currentValue = $("#no_vat_percentage").val();
                               if (!isNaN(currentValue)) 
                               {
                                 $("#no_vat_percentage").val(parseFloat(currentValue).toFixed(2));
                               }
                               console.log("User inputted percentage:", $("#no_vat_percentage").val());
                             }, 2000);
                           });
                      </script>

                  ';
          $data['html'] = $html;

          $data["footer"] = '
                               <button type="button" class="btn btn-success ml-1" onclick="save_promo()">
                                     <i class="bx bx-check d-block d-sm-none"></i>
                                     <span class="d-none d-sm-block">save</span>
                               </button>

                               <button type="button" onclick="close_promo_modal()" class="btn btn-danger" data-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block ">Close</span>
                               </button>
                            ';

          echo json_encode($data);         
     }


     function save_promo()
     {
           $brand_id          = $_POST['brand_id'];     
           $promoName         = $_POST['promoName'];
           $dateFrom          = $_POST['dateFrom'];
           $dateTo            = $_POST['dateTo'];
           $tenderType        = $_POST['tenderType'];
           $quantity          = $_POST['quantity'];
           $percentage        = $_POST['percentage'];
           $no_vat_percentage = $_POST['no_vat_percentage'];

           $check_promo = $this->Cupon_mod->check_promo_list($promoName,$dateFrom,$dateTo,$tenderType,$brand_id);
           if(empty($check_promo))
           {
                $data['response'] = 'success';     

                $table = 'promo_list';
                $insert_data['promo_name'] = $promoName;
                $insert_data['date_from']   = $dateFrom;
                $insert_data['date_to']     = $dateTo;
                $insert_data['tender_type'] = $tenderType;
                $insert_data['brand_id']    = $brand_id;

                $promo_id =  $this->Cupon_mod->insert($table,$insert_data);     

                $table = 'promo_setup';    
                $insert_data = array();
                $insert_data['quantity'] = $quantity;
                $insert_data['discount'] = $percentage;
                $insert_data['vatable']  = 'VAT12';
                $insert_data['promo_id'] = $promo_id;
                $this->Cupon_mod->insert($table,$insert_data);   

                $insert_data = array();
                $insert_data['quantity'] = $quantity;
                $insert_data['discount'] = $no_vat_percentage;
                $insert_data['vatable']  = 'NO VAT';
                $insert_data['promo_id'] = $promo_id;
                $this->Cupon_mod->insert($table,$insert_data);   

           }
           else 
           {
                $data['response'] = 'exist';               
           }

           echo json_encode($data);
     }


     function download_csv_template()
     {
           header('Content-Type: text/plain');
           header('Content-Disposition: attachment; filename="pharma_item_template.CSV"');
           header("Content-Transfer-Encoding: binary");
           ob_clean();

           echo 'Item code';
     }


     function promo_modal()
     {
         $promo_id = $_POST['promo_id'];
         $style_td ='font-family: Arial, sans-serif;';
         $html     = '';
         $html    .= '
                         <div class="row">
                              <div class="col-sm-3">
                                    <a href="#" onclick="download_csv_template()">Dowload CSV Template</a>  
                              </div>
                              <div class="col-sm-8">
                                 <form id="uploadForm" enctype="multipart/form-data">   
                                    <button type="button" class="btn btn-success ml-1" onclick="upload_item(\''.$promo_id.'\')">
                                     <i class="bx bx-check d-block d-sm-none"></i>
                                     <span class="d-none d-sm-block">upload CSV</span>
                                    </button> 
                                    <input type="file" class="btn" onchange="revert_color(\'item_txt_file\')"  name="files[]" id="item_txt_file" multiple="multiple"   style="display: inline-block; padding: 1px;" >                        
                                 </form>   
                              </div>

                         </div><br>
                         <div class="row">
                              <div class="col-sm-2 form-group" style="'.$style_td.'">                                                                   
                                    <input type="text" class="form-control" id="item_code" placeholder="item code" required>
                              </div>
                              <div class="col-sm-2">
                                   <button type="button" class="btn btn-success ml-1" onclick="save_item(\''.$promo_id.'\')">
                                     <i class="bx bx-check d-block d-sm-none"></i>
                                     <span class="d-none d-sm-block">save</span>
                                   </button>
                              </div>
                         </div>
                     '; 


         $table_id = "item_list_table";
         $table_header = array("Item Code","Description","Unit of Measure","Action");              
         $html        .= $this->simplify->populate_header_table($table_id,$table_header);

         $promo_list = $this->Cupon_mod->get_promo_item_list($promo_id);
         foreach($promo_list as $promo)
         {
            $description = $this->Cupon_mod->getItemDescNav($promo['item_code'],$promo['UOM']);
            $html .= '
                         <tr>
                              <td style="'.$style_td.'">'.$promo['item_code'].'</td>
                              <td style="'.$style_td.'">'.$description.'</td>
                              <td style="'.$style_td.'">'.$promo['UOM'].'</td>
                              <td style="'.$style_td.'">
                                                         <button type="button" class="btn btn-danger" onclick="delete_item(\''.$promo['item_id'].'\',\''.$promo_id.'\')"> Delete </button>
                              </td>
                         </tr>
                     ';
         }


         $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#'.$table_id.'").dataTable({                            
                                         "order":[0,"asc"]
                                       });

                             </script>';

         $data['html'] = $html;    

         $data["footer"] = '
                               <button type="button" onclick="close_promo_modal()" class="btn btn-danger" data-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block ">Close</span>
                               </button>
                            ';

         echo  json_encode($data);
     }


     function delete_item()
     {
          $item_id =  $_POST['item_id'];

          $this->Cupon_mod->delete_promo_item($item_id) ;

          $data['response'] = 'success';
          echo json_encode($data);
     }

     function save_item()
     {
          $promo_id  = $_POST['promo_id'];
          $item_code = $_POST['item_code'];

          $table = 'promo_item_list';
          $insert_data['item_code'] = $item_code;
          $insert_data['promo_id']  = $promo_id;
          $insert_data['UOM']       = 'PC';         
          $this->Cupon_mod->insert($table,$insert_data);     

          $data['response'] = 'success';
          echo json_encode($data);
     }


     function display_brand_table()
     {
           $brand_list = $this->Cupon_mod->get_promo_brand_list('');
           $html = '';
           

           $table_id     = "brand_list_table";
           $table_header = array("brand","logo","");              
           $html        .= $this->simplify->populate_header_table($table_id,$table_header);

           $style_td ='font-family: Arial, sans-serif;';
           foreach($brand_list as $list)
           {               
                 if($list['image_logo_path'] == '')
                 {
                    $image_path = 'assets/cuponing/images/no-image.png';
                 }
                 else 
                 {
                    $image_path = $list['image_logo_path'];                  
                 }

                 $html .= '
                              <tr>
                                   <td style="'.$style_td.'">'.$list['brand_name'].'</td>
                                   <td style="'.$style_td.'"><img src="'.base_url().$image_path.'" alt="" style="width: 328px; height: 164px; /*object-fit: cover;*/" ></td>                                   
                                   <td style="'.$style_td.'">
                                                              <button type="button" class="btn btn-danger" onclick="update_brand_view(\''.$list['brand_id'].'\')"> udpate </button>
                                   </td>
                              </tr>
                          ';
           }

           $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#'.$table_id.'").dataTable({                            
                                         "order":[0,"asc"]
                                       });

                             </script>';

           $data['html'] = $html;    

           echo json_encode($data);
     }
}