<?php

class Cupon_mod extends CI_Model
{
	function __construct()
    {
        parent::__construct();
        $this->mpdi = $this->load->database('mpdi', TRUE);       
    }


// SELECT 
//         *
// FROM 
//      promo_list as promo
// INNER JOIN promo_setup setup ON setup.promo_id = promo.promo_id
// WHERE 
//         '2023-03-29' >=  promo.date_from    
//    AND 
//         '2023-03-29' <= promo.date_to
//    AND  
//         setup.vatable = 'NO VAT'
//    AND 
//        setup.quantity <=29
//    ORDER BY  setup.quantity desc    
  
// LIMIT 1; 

    // Pharma-Admin Start
    function getPromoItems($ind){ 
        $promo_arr = array("promo_item_list","promo_item_list_sr");
        $this->db->select('*');
        $this->db->from($promo_arr[$ind]);
        $query = $this->db->get();
        $result = $query->result_array();
        $list = array();
        foreach($result as $item){  
            $items["item_id"] = $item["item_id"];          
            $items["item_code"] = $item["item_code"];
            if(isset($item["uom"]))
                $items["uom"] = $item["uom"]; // Health Plus
            else
                $items["uom"] = $item["UOM"]; // Patient Compliance
            $items["description"] = $this->getItemDescNav($item["item_code"],$items["uom"]);
            $list[] = $items;
        }

        return $list;
    }

    function getItemDescNav($item_code,$uom){ 
        $get_connection = $this->get_connection(13);
        foreach($get_connection  as $con)
        {
            $username    = $con['username'];
            $password    = $con['password']; 
            $connection  = $con['db_name'];
            $sub_db_name = $con['sub_db_name'];
        }

        $connect      = odbc_connect($connection, $username, $password);
        $table        = '['.$sub_db_name.'$Item]';
        
        $table_query = "SELECT [Description] FROM ".$table." WHERE [No_]='".$item_code."' AND [Base Unit of Measure]='".$uom."'";
        
        $table_row    = odbc_exec($connect, $table_query);  
        
        $desc = '';
        if(odbc_num_rows($table_row) > 0)
        {
            while(odbc_fetch_row($table_row))
            {
                $desc = odbc_result($table_row, 1);
            }
        }

        return $desc;
    }


    function getItem_uom_Nav($item_code)
    { 
        $get_connection = $this->get_connection(91);
        foreach($get_connection  as $con)
        {
            $username    = $con['username'];
            $password    = $con['password']; 
            $connection  = $con['db_name'];
            $sub_db_name = $con['sub_db_name'];
        }



        $connect      = odbc_connect($connection, $username, $password);
        $table        = '['.$sub_db_name.'$Item]';
        
        $table_query = "SELECT [Base Unit of Measure] FROM ".$table." WHERE [No_]='".$item_code."'";
        
        

        $table_row    = odbc_exec($connect, $table_query);  
        
        $uom = '';
        if(odbc_num_rows($table_row) > 0)
        {
            while($item = odbc_fetch_array($table_row))
            {
                $uom = $item['Base Unit of Measure'];
            }
        }

        
         

        return $uom;
    }



    function getPromoItemCountByCodeAndUOM($ind,$item_code,$uom){
        $promo_arr = array("promo_item_list","promo_item_list_sr");
        $col_arr = array("UOM","uom");

        $this->db->select('COUNT(*) as count_');
        $this->db->from($promo_arr[$ind]);
        $this->db->where("item_code",$item_code);
        $this->db->where($col_arr[$ind],$uom);
        $query = $this->db->get();
        return $query->row_array()["count_"];
    }

    function insertPromoItem($ind,$item_code,$uom){
        $promo_arr = array("promo_item_list","promo_item_list_sr");
        $col_arr = array("UOM","uom");

        $insert_array["item_code"] = $item_code;
        $insert_array[$col_arr[$ind]] = $uom;
        $insert_array["promo_id"] = 1;

        $this->db->insert($promo_arr[$ind],$insert_array);
    }

    function deletePromoItem($ind,$item_id){
        $promo_arr = array("promo_item_list","promo_item_list_sr");
        $this->db->where('item_id',$item_id)->delete($promo_arr[$ind]);
    }

    function retrieveCouponUsers(){
        $list = array();
        $cmd = "SELECT * FROM cupon_users";
        $query = $this->db->query($cmd);
        $result = $query->result_array();
        foreach($result as $user){
            $users["user_id"] = $user["user_id"];
            $users["firstname"] = $user["fname"];
            $users["lastname"] = $user["lname"];
            $users["username"] = $user["username"];
            $users["store"] = $this->getStoreName($user["db_id"]);
            $users["access"] = $user["access_type"];

            $list[] = $users; 
        }

        return $list;

    }

    function retrieveCouponUserById($id){
        $user = array();
        $cmd = "SELECT * FROM cupon_users WHERE user_id=?";
        $query = $this->db->query($cmd,array($id));
        $row = $query->row_array();
        if(isset($row)){
            $user["user_id"] = $row["user_id"];
            $user["firstname"] = $row["fname"];
            $user["lastname"] = $row["lname"];
            $user["username"] = $row["username"];
            $user["password"] = $row["password"];
            $user["store"] = $this->getStoreName($row["db_id"]);
            $user["access"] = $row["access_type"];
        }

        return $user;

    }

    function getStoreName($db_id){
        $store = "";
        $cmd = "SELECT store FROM `database` WHERE db_id=?";
        $query = $this->mpdi->query($cmd,array($db_id));
        $row = $query->row_array();
        if(isset($row))
            $store = $row["store"];

        return $store;

    }

    function insertCouponUser($firstname,$lastname,$username,$db_id,$access){
        $insert_array["fname"] = $firstname;
        $insert_array["lname"] = $lastname;
        $insert_array["department"] = "MP";
        $insert_array["username"] = $username;
        $insert_array["password"] = password_hash("143", PASSWORD_DEFAULT);
        $insert_array["db_id"] = $db_id;
        $insert_array["access_type"] = $access;

        $this->db->insert("cupon_users",$insert_array);

    }

    function updateCouponUser($update_array,$id){
        $where_array = array("user_id"=>$id);
        $this->db->update("cupon_users",$update_array,$where_array);
    }
    // Pharma-Admin End

    function get_db_id_cupon_data()
    {
         $this->db->select('db_id');
         $this->db->from('cupon_data');
         $this->db->group_by('db_id');
         $query = $this->db->get();
         return $query->result_array();
    }


    function get_billed_batch($batch_id)
    {
          $this->db->select('*,cup.db_id as database_id');
          $this->db->from('promo_billing_batch as batch');
          $this->db->join('cupon_data as  cup','cup.batch_id = batch.batch_id','INNER');
          $this->db->join('cupon_users as user','user.user_id = batch.user_id','inner');
          $this->db->join('item_transact as trans','trans.cupon_id = cup.cupon_id','INNER');
          $this->db->where('batch.batch_id',$batch_id);
          $this->db->where('trans.discounted_price !=','0.00');
          $query = $this->db->get();
          return $query->result_array();
    }


    function get_total_billed_batch($batch_id)
    {
          $this->db->select('sum(discounted_price) as total_disc_amount');
          $this->db->from('promo_billing_batch as batch');
          $this->db->join('cupon_data as  cup','cup.batch_id = batch.batch_id','INNER');
          $this->db->join('cupon_users as user','user.user_id = batch.user_id','inner');
          $this->db->join('item_transact as trans','trans.cupon_id = cup.cupon_id','INNER');
          $this->db->where('batch.batch_id',$batch_id);
          $this->db->where('trans.discounted_price !=','0.00');
          $query = $this->db->get();
          return $query->result_array();
    }



    function get_promo_data($vatable,$quantity,$item_code)
    {
           $this->db->select('*');
           $this->db->from('promo_brand as brand');
           $this->db->join('promo_list  as promo','promo.brand_id = brand.brand_id','INNER'); 
           $this->db->join('promo_setup as setup','setup.promo_id = promo.promo_id','INNER');
           $this->db->join('promo_item_list as item','item.promo_id = promo.promo_id','INNER');
           $this->db->where('promo.date_from <=',date('Y-m-d'));
           $this->db->where('promo.date_to   >=',date('Y-m-d'));
           $this->db->where('setup.vatable',$vatable);
           $this->db->where('setup.quantity <=',$quantity);
           $this->db->where('item.item_code',$item_code);
          
           $this->db->where('brand.brand_id',$_SESSION['brand_id']);
           // $this->db->where('promo.promo_id',$_SESSION['promo_id']);
           $this->db->order_by('setup.quantity','desc');
           $this->db->limit(1);
           $query = $this->db->get();
           return $query->result_array();
    }     


    function get_promo_list_details($filter)
    {
         $this->db->select('*');
         $this->db->from('promo_list');
         if($filter == '')
         {
             $this->db->where('promo_id',$_SESSION['promo_id']);
         }
         $query = $this->db->get();
         return $query->result_array();
    }




    function get_brand_details()
    {
         $this->db->select('*');
         $this->db->from('promo_brand as brand');
         $this->db->where('brand.brand_id',$_SESSION['brand_id']);
         $query = $this->db->get();
         return $query->result_array();
    }



    function get_promo_list($filter)
    {
           $this->db->select('*');
           $this->db->from('promo_list  as promo'); 
           if($filter == '')
           {            
               $this->db->where('promo.date_from <=',date('Y-m-d'));
               $this->db->where('promo.date_to   >=',date('Y-m-d'));
           }
           else 
           {
              $this->db->where('promo.brand_id',$filter);
           }
           $query = $this->db->get();
           return $query->result_array();
    }


    function get_brand_list($filter)
    {
         $this->db->select('*');
         $this->db->from('promo_brand as brand');   
         $this->db->join('promo_list  as promo','promo.brand_id = brand.brand_id','INNER');
         if($filter == '')
         {            
             $this->db->where('promo.date_from <=',date('Y-m-d'));
             $this->db->where('promo.date_to   >=',date('Y-m-d'));
         }
         $this->db->group_by('brand.brand_id');
         $query = $this->db->get();
         return $query->result_array();

    }


    function search_user($username)
    {

         $this->db->select('*');
         $this->db->from('cupon_users');
         $this->db->where('username',$username);
         $query =$this->db->get();
         return $query->result_array();
    }


    function get_user_connection()
    {
         $this->db->select('*');
         $this->db->from('cupon_users');
         $this->db->where('username',$_SESSION['username']);
         $query = $this->db->get();
         return $query->result_array();
    }



    function get_user_details($user_id)
    {
         $this->db->select('*');
         $this->db->from('cupon_users');
         $this->db->where('user_id',$user_id);
         $query = $this->db->get();
         return $query->result_array();
    }


    function get_variance($from_date,$dateTo,$status,$db_id)
    {
           
           // SELECT 
           //         cup.date_transact,cup.ordering_number,sum( (trans.unit_price * trans.discount) * quantity)  as MUDC_disc, cup.nav_discount, (sum( (trans.unit_price * trans.discount) * quantity) - cup.nav_discount) as variance 
           //  FROM 
           //        cupon_data as cup
           //  INNER JOIN 
           //        item_transact  as trans on trans.cupon_id = cup.cupon_id
           //  WHERE
           //        date(cup.date_transact) <= '2023-04-11'      
           //    AND
           //        date(cup.date_transact) >= '2023-04-11'      
           //    and 
           //        cup.db_id = '13'
           //  GROUP BY cup.cupon_id   

          //$this->db->select('cup.cupon_id,cup.status,cup.date_transact,cup.ordering_number,sum( (trans.unit_price * trans.discount) * quantity)  as MUDC_disc, cup.nav_discount, (sum( (trans.unit_price * trans.discount) * quantity) - cup.nav_discount) as variance');
          $this->db->select('cup.cupon_id,cup.status,cup.date_transact,cup.ordering_number,sum(trans.discounted_price)  as MUDC_disc, cup.nav_discount, sum(discounted_price) - cup.nav_discount as variance');
          $this->db->from('cupon_data as cup');
          $this->db->join('item_transact  as trans','trans.cupon_id = cup.cupon_id','INNER');
          $this->db->where('date(cup.date_transact) >=',$from_date);
          $this->db->where('date(cup.date_transact) <=',$dateTo);

          if(in_array($_SESSION['access_type'],array('accounting')))
          {
             if($db_id != 'all')
             {
                 $this->db->where('cup.db_id',$db_id);
             }
          }
          else 
          {
             $this->db->where('cup.db_id',$_SESSION['db_id']);
          }

          
          if($status != '')
          {
             $this->db->where('cup.status',$status);
          }
          $this->db->GROUP_BY('cup.cupon_id');
          $query = $this->db->get();
          return $query->result_array();
    }


    function count_promo_code($batch_id)
    {   
        $this->db->select('count(promo_code)  as total_promo_code');
        $this->db->from('cupon_data as  cup');
        $this->db->join('promo_billing_batch as batch','cup.batch_id = batch.batch_id','INNER');
        $this->db->where('batch.batch_id',$batch_id);
        $query = $this->db->get();
        return $query->result_array();
    }   


    function get_connection($db_id)
    {

           $this->mpdi->select('*');
           $this->mpdi->from('mpdi.database as db');
           $this->mpdi->join('store_info as inf','inf.address_id = db.address_id','INNER');
           $this->mpdi->where('db_id',$db_id);
           $query = $this->mpdi->get();
           return $query->result_array();
    }




    function search_item($search)
    {
         $search_arr = explode('****',$search);

         $this->db->select('*');
         $this->db->from('item_discounting_masterfile');
         $this->db->like('item_code',$search_arr[0],'both');
         $this->db->or_like('item_name',$search_arr[0],'both');
         if(count($search_arr) >1)
         {            
             $this->db->or_like('item_code',$search_arr[1],'both');
             $this->db->or_like('item_name',$search_arr[1],'both');
         }        
         $query = $this->db->get();
         return $query->result_array();
    }


    function search_item_by_item_code($item_code,$uom)
    {       

        $this->db->select('*');
        $this->db->from('promo_brand as brand');
        $this->db->join('promo_list as promo','brand.brand_id = promo.brand_id');
        $this->db->join('promo_item_list as item','item.promo_id = promo.promo_id','INNER');
        $this->db->where('promo.date_from <=',date('Y-m-d')); 
        $this->db->where('promo.date_to   >=',date('Y-m-d')); 
        $this->db->where('item.item_code',$item_code);
        $this->db->where('item.UOM',$uom);

        
        $this->db->where('brand.brand_id',$_SESSION['brand_id']);
        // $this->db->where('promo.promo_id',$_SESSION['promo_id']);
        $query = $this->db->get();
        return $query->result_array();
    }


    function search_name($name)
    {
         $this->db->select('*');
         $this->db->from('cupon_data');
         $this->db->like('full_name',$name,'both');
         $this->db->group_by('full_name');
         $query = $this->db->get();
         return $query->result_array();
    }

    function get_cupon_data($from,$to,$store)
    {
         $this->db->select('sum(trans.quantity) as total_quantity,trans.item_code,uom,cup.db_id');
         $this->db->from('promo_brand as brand');
         $this->db->join('promo_list as list','list.brand_id = brand.brand_id');
         $this->db->join('cupon_data as cup','cup.promo_id = list.promo_id');
         $this->db->join('item_transact as trans','trans.cupon_id = cup.cupon_id','INNER');
         $this->db->join('customer_data as cust','cust.customer_id = cup.customer_id','INNER');
         $this->db->where('date(cup.date_transact) >= ',$from);
         $this->db->where('date(cup.date_transact) <= ',$to);
        
         // $this->db->where('cup.promo_id',$_SESSION['promo_id']);
         $this->db->where('brand.brand_id',$_SESSION['brand_id']);
       
         if($store != 'all')
         {
             $this->db->where('cup.db_id',$store);
         }
         $this->db->where('trans.discount !=','0.00');
         $this->db->group_by('trans.item_code');
         $this->db->group_by('trans.uom');

         $query = $this->db->get();
         return $query->result_array();
    }


    


    function get_item_transact_per_line($from_date,$dateTo,$report,$store)
    {

         $this->db->select('*');    
         $this->db->from('item_transact as transact');
         $this->db->join('cupon_data as cup','cup.cupon_id = transact.cupon_id'); 
         $this->db->join('cupon_users as users','users.user_id = cup.transact_by');
         $this->db->join('promo_list as promo','promo.promo_id = cup.promo_id');
         $this->db->join('promo_brand as brand','brand.brand_id = promo.brand_id');

         // $this->db->where('cup.promo_id',$_SESSION['promo_id']);
         $this->db->where('brand.brand_id',$_SESSION['brand_id']);
         

         if($report == 'EOM')
         {            
            $this->db->where('date(cup.date_transact) >=',$from_date);
            $this->db->where('date(cup.date_transact) <=',$dateTo);
         }
         else 
         if($report == 'EOD')            
         {
            $this->db->where('date(cup.date_transact)',$from_date);
         }


         if($_SESSION['access_type'] == 'mpdi')
         {
            if($report != 'EOM')
            {                
                $this->db->where('cup.status','billed-acctg');
            }
            
            if(!in_array( $store,array('','all') ) )
            {

             $this->db->where('users.db_id',$store);                                      
            }
         }
         else 
         if(in_array($_SESSION['access_type'],array('liquidation')) )
         {
             $this->db->where('cup.db_id',$_SESSION['db_id']);                          
         }


         if(in_array($_SESSION['access_type'],array('accounting')))
         {  
             if($store != 'all')
             {
                 $this->db->where('cup.db_id',$store);                                      
             } 
         }


         
         $this->db->where('transact.discount !=','0.00');
         $this->db->order_by('cup.date_transact','ASC');
         $query= $this->db->get();
         return $query->result_array();
    }


    function search_promo_code($promo_code)
    {
         $this->db->select('*');
         $this->db->from('cupon_data as cup');
         $this->db->join('customer_data as cus','cus.customer_id = cup.customer_id','INNER');
         $this->db->where('cup.promo_code',$promo_code);      
         $this->db->where('db_id',$_SESSION['db_id']);   
         $query = $this->db->get();
         return $query->result_array();
    }

    function search_cupon_data($column_name,$value)
    {
         $this->db->select('*');    
         $this->db->from('cupon_data');
         $this->db->where($column_name,$value); 
         $this->db->where('db_id',$_SESSION['db_id']);        
         $query = $this->db->get();
         return $query->result_array();
    }


    function search_customer($column_name_arr,$column_value_arr)
    {
           $this->db->select('*'); 
           $this->db->from('customer_data');           
           for($a=0;$a<count($column_name_arr);$a++)
           {
                if($column_value_arr[$a] != '')
                {
                     $this->db->like($column_name_arr[$a],$column_value_arr[$a],'both'); 
                }
           }

           $query = $this->db->get();
           return $query->result_array();
    }



    function insert_customer_data($column_name_arr,$column_value_arr)
    {
           for($a=0;$a<count($column_name_arr);$a++)
           {
               $this->db->set($column_name_arr[$a],$column_value_arr[$a]);
           }
           $this->db->insert('customer_data');
           $new_customer_id = $this->db->insert_id();
           return $new_customer_id;
    }


  function get_promo_billing_batch($batch_id,$from_date,$dateTo)
  {
     $this->db->select('*');
     $this->db->from('promo_billing_batch as batch');
     $this->db->join('cupon_users as user','user.user_id = batch.user_id','inner');     
     if($_SESSION['access_type']  == 'accounting')
     {
         $this->db->where('batch.user_id',$_SESSION['user_id']);
         $this->db->where('db_id',$_SESSION['db_id']);   
     }
     // else 
     // if($_SESSION['access_type']  == 'mpdi')
     // {
     //     $this->db->where('batch.mpdi-status', NULL);
     // }


    // if($batch_id != '')
     if(!in_array($batch_id,array('mpdi','') ) )
     {
         $this->db->where('batch_id',$batch_id);
     }
     else 
     if($batch_id == 'mpdi')   
     {
        $this->db->where('batch.from_date >=',$from_date);
        $this->db->where('batch.to_date <=',$dateTo);

     }


     $query = $this->db->get();     
     return $query->result_array();
  }


  function update_promo_billing_batch($batch_id,$invoice_number)
  {
      $this->db->set('paid_invoice_number',$invoice_number);
      $this->db->set('mpdi-status','PAID');
      $this->db->where('batch_id',$batch_id);
      $this->db->update('promo_billing_batch');
  }


  function update_date_extracted($batch_id)
  {
      $this->db->set('mpdi-date-extracted',date('Y/m/d'));
      $this->db->where('batch_id',$batch_id);
      $this->db->update('promo_billing_batch');
  }

   
  function update_cupon_data_billed_mpdi($batch_id)
  {
      $this->db->set('status','billed-mpdi'); 
      $this->db->where('batch_id',$batch_id);
      $this->db->update('cupon_data'); 
  }


    function insert_promo_billing_batch($from_date,$to_date)
    {
         
         $this->db->set('from_date',$from_date);
         $this->db->set('to_date',$to_date);
         $this->db->set('date_generated',date('Y-m-d'));
         $this->db->set('user_id',$_SESSION['user_id']);
         $this->db->set('mpdi-status','FOR BILLING');
         $this->db->insert('promo_billing_batch');

         $new_batch_id = $this->db->insert_id();
         return $new_batch_id;
    }



    function insert_cupon_data($fname,$lname,$promo_code,$order_number,$phone_no,$year)
    {
        //$user_data = $this->get_user_connection(); 

        $column_name_arr  = array('fname','lname','phone_no','birth_year'); 
        $column_value_arr = array($fname,$lname,$phone_no,$year);       
        $search_user      = $this->search_customer($column_name_arr,$column_value_arr);  

        if(empty($search_user))
        {
             $customer_id = $this->insert_customer_data($column_name_arr,$column_value_arr);
        }
        else 
        {
             $customer_id = $search_user[0]['customer_id'];
        }


        $this->db->set('customer_id',$customer_id);
        $this->db->set('promo_code',$promo_code);
        $this->db->set('date_transact',date('Y-m-d H:i:s')); 
        $this->db->set('ordering_number',$order_number);
        $this->db->set('db_id',$_SESSION['db_id']);
        $this->db->set('transact_by',$_SESSION['user_id']);
        $this->db->set('promo_id',$_SESSION['promo_id']);
        $this->db->insert('cupon_data');

        $new_cupon_id = $this->db->insert_id();
        return $new_cupon_id;
    }

    function update_password($password,$username){
          $this->db->update('cupon_users', array('password' => $password), array('username' => $_SESSION['username']));
    }

    function update_pass($password){
          $this->db->update('cupon_users', array('password' => $password), array('username' => $_SESSION['username']));
    }

    function insert_item_transact($column_data,$cupon_id)
    {
         $column_name = array('item_code','uom','quantity','unit_price','discount','discounted_price');   
         unset($column_data[1]);
         //unset($column_data[count($column_name)]);
         $column_data = array_values($column_data);
         
        
         $this->db->set('cupon_id',$cupon_id);
         for($a=0;$a<count($column_name);$a++)
         {             
             $this->db->set($column_name[$a],$column_data[$a]);
         }
         $this->db->insert('item_transact');
    }


    function update_cupon_data($column_name_arr,$column_value_arr,$ordering_number,$db_id)
    {
         for($a=0;$a<count($column_name_arr);$a++)
         {
             $this->db->set($column_name_arr[$a],$column_value_arr[$a]);
         }
         $this->db->where('ordering_number',$ordering_number);
         $this->db->where('db_id',$db_id);
         $this->db->update('cupon_data');
    }


    function update_cupon_data_billing($column_name_arr,$column_value_arr,$cupon_id)
    {
         for($a=0;$a<count($column_name_arr);$a++)
         {
             $this->db->set($column_name_arr[$a],$column_value_arr[$a]);
         }             
         $this->db->where('cupon_id',$cupon_id);
         $this->db->update('cupon_data');
    }


    function update_cupon_data_paid($batch_id)
    {
         $this->db->set('status','paid'); 
         $this->db->where('batch_id',$batch_id);
         $this->db->update('cupon_data');
    }



    function get_cupon_data_per_batch($batch_id)
    {
        $this->db->select('*');
        $this->db->from('cupon_data');
        $this->db->where('batch_id',$batch_id);
        $query = $this->db->get();
        return $query->result_array();
    }


    function count_transactions($db_id,$from,$to)
    {         
        $this->db->select('count(ordering_number) as total');
        $this->db->from('cupon_data');
        if($db_id != 'all')
        {
            $this->db->where('db_id',$db_id);
        }
        $this->db->where('date(date_transact) >=',$from);
        $this->db->where('date(date_transact) <=',$to);
        $query = $this->db->get();
        return $query->result_array();
    }


     function insert($table,$insert_data)
     {  
         $this->db->set($insert_data);
         $this->db->insert($table);
         return $this->db->insert_id();
     }


     function update($table,$update_data,$where)
     {
         $this->db->set($update_data);
         $this->db->where($where);
         $this->db->update($table);
     }


     function check_promo_list($promo_name,$date_from,$date_to,$tender_type,$brand_id)
     {
         $this->db->select("*");
         $this->db->from('promo_list as promo');
         $this->db->where("promo_name",$promo_name);
         $this->db->where("date_from",$date_from);
         $this->db->where("date_to",$date_to);
         $this->db->where("tender_type",$tender_type);
         $this->db->where("brand_id",$brand_id);
         $query = $this->db->get();
         return $query->result_array();
     }


     function get_promo_item_list($promo_id)
     {
         $this->db->select("*");
         $this->db->from('promo_item_list as list');
         $this->db->where('list.promo_id',$promo_id);
         $query = $this->db->get();
         return $query->result_array();
     }



    function delete_promo_item($item_id) 
    {
        $this->db->where('item_id', $item_id);
        $this->db->delete('promo_item_list');
    }

    function search_promo_item($item_code,$promo_id) 
    {
        $this->db->select('*'); 
        $this->db->from('promo_item_list');
        $this->db->where('item_code', $item_code);
        $this->db->where('promo_id',$promo_id);
        $query = $this->db->get();
        return $query->result_array();
    }
  

    function get_promo_brand_list($filter)
    {
         $this->db->select('*');
         $this->db->from("promo_brand as brand");
         if($filter != '')
         {
            $this->db->where('brand.brand_name',$filter);
         }
         $query =  $this->db->get();
         return $query->result_array();
    }


    function get_promo_brand_by_id($brand_id)
    {        
         $this->db->select('*');
         $this->db->from("promo_brand as brand"); 
         $this->db->where('brand.brand_id',$brand_id);
         $query =  $this->db->get();
         return $query->result_array();
    }   
}