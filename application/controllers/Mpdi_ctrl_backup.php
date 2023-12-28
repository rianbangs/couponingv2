<?php
/**
 * 
 */
class Mpdi_ctrl extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('simplify/simplify','simplify');
        $this->load->model('simplify/pdf_simplify','pdf_');
        $this->load->model('Mpdi_mod');

        if(!isset($_SESSION['user_id']) || $this->Mpdi_mod->getUserCountById($_SESSION['user_id'])<1){
            redirect(base_url('Mpdi_log_ctrl/index'));
        }
        
    }

    /*rian code---------------------------------------------------*/   
  


     public function generate_password()
     {
        $pword ='1234';
        $new_hash = password_hash($pword, PASSWORD_BCRYPT);
        echo $new_hash;
     }

     function validate_managers_key()
     {
         $username = $_POST['username'];
         $password = $_POST['password'];

         $user_data = $this->Mpdi_mod->get_user($username);

         if(empty($user_data))
         {
            $reponse = "username not found";
         }
         else 
         {
            if (password_verify($password, $user_data[0]['password'])) 
            {

                  $get_pad_number = $_POST['get_pad_number'];
                  $header_data = $this->Mpdi_mod->get_pad_id($get_pad_number);
                  if(empty($header_data))
                  {                    
                     $reponse = "success";  
                  }
                  else 
                  {
                     $reponse = "Sales Invoice already exist";   
                  }
            }
            else 
            {
                   $reponse = "invalid password";    
            }
           
         }

         $data['response'] = $reponse; 
         echo json_encode($data);
     }
/*end of rian code---------------------------------------------------*/ 

    function get_si_details()
    {
         $Document_num = $_POST['Document_no'];
         $html         = '<input hidden type="text" class="document_num" value="'.$Document_num.'"><h5>Sales Invoice: '.$Document_num.'</h5>';
         $table_header = array("QTY","UOM","ITEM NO.","DESCRIPTION","LOT NO.","EXPIRY DATE","DEAL","LINE DISCOUNT %","UNIT PRICE","NET PRICE","LINE DISCOUNT AMOUNT","AMOUNT");
         $table_id     = "sales_inv_details";
         $html        .= $this->simplify->populate_header_table($table_id,$table_header);

         
         $table          = '[PHARMA WHOLESALE TEST$Sales Invoice Line]';
         $get_connection = $this->Mpdi_mod->get_connection();
         foreach($get_connection  as $con)
         {
              $username   = $con['username'];
              $password   = $con['password']; 
              $connection = $con['db_name'];
         }


         /*$connection  = "ODBC_Sample";
         $username    = "sa";
         $password    = "Corporate_it"; */
         //$table       = '[Marcela Pharma Distributor Inc$Sales Invoice Line]';
         $connect     = odbc_connect($connection, $username, $password); 
         $table_query = "SELECT * FROM ".$table." WHERE [Document No_]='".$Document_num."'";
         $table_row   = odbc_exec($connect, $table_query);  
         if(odbc_num_rows($table_row) > 0)
          {
                 while(odbc_fetch_row($table_row))
                 {
                     $Document_no         = odbc_result($table_row, 2);
                     $Sell_to_Customer_No = odbc_result($table_row, 4);  
                     $address             = odbc_result($table_row, 7);  
                     $Quantity            = odbc_result($table_row, 13);  
                     $Item_no             = odbc_result($table_row, 6);  
                     $Description         = odbc_result($table_row, 10);  
                     //$Lot_no              = odbc_result($table_row, 106);  
                     $Lot_no              = odbc_result($table_row, 103);  
                     //$Expiry_date         = odbc_result($table_row, 107);  
                     $Expiry_date         = odbc_result($table_row, 104);  
                     //$Deal                = odbc_result($table_row, 108);
                     $Deal                = odbc_result($table_row, 105);
                     $Line_discount       = odbc_result($table_row, 17);
                     $unit_price          = odbc_result($table_row, 14);                     
                     $Line_disc_amount    = odbc_result($table_row, 18);
                     //$Amount              = odbc_result($table_row, 19);
                     $Amount              = odbc_result($table_row, 20);
                     $Unit_of_Measure     = odbc_result($table_row, 12);
                     $net_price           = $Amount/$Quantity;

                     $row1   = array(
                                         number_format($Quantity),
                                         $Unit_of_Measure,
                                         $Item_no,
                                         $Description,
                                         $Lot_no,
                                         date('m-d-Y',strtotime(date($Expiry_date))),
                                         $Deal,
                                         number_format($Line_discount,2),
                                         number_format($unit_price,2),
                                         number_format($net_price,2),
                                         number_format($Line_disc_amount,2),
                                         number_format($Amount,2)
                                    );
                             
                     $style1 = array(
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;",
                                         "text-align:center;"
                                    );
                     $tr_class ='tr_';
                     $html  .= $this->simplify->populate_table_rows($row1,$style1,$tr_class);

                                   
                 }
          }


          $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#sales_inv_details").dataTable({                            
                                         "order":[0,"asc"]
                                       });

                             </script>';

           $data['html'] = $html;


          $get_header =  $this->Mpdi_mod->get_header($Document_num);

          if(empty($get_header))
          { 

            $footer = '
                          <input type="text" class="get_pad_number" placeholder="TIN number">
                          <button class="btn btn-info btn-xs gen_report" onclick="gen_report()">generate report</button>                            
                      ';
          }
          else 
          {
            $footer = '
                          <input type="text" class="get_pad_number" placeholder="TIN number">
                          <button class="btn btn-danger btn-xs gen_reprint_report" onclick="gen_reprint_report()">reprint report</button>                          
                      ';
          }

          $footer .= '<button type="button" class="btn btn-default" data-dismiss="modal" id="close" style="padding: 3px 10px;">Close</button>';


          $data['footer'] = $footer;


         echo json_encode($data);
    }



    function search()
    {
            //PWS2-SPIN00046038

          $html = '';

          $search = $_POST['search']; 
          //$table          = '[Marcela Pharma Distributor Inc$Sales Invoice Line]';
          $table          = '[PHARMA WHOLESALE TEST$Sales Invoice Line]';
          $get_connection = $this->Mpdi_mod->get_connection();
          foreach($get_connection  as $con)
          {
              $username   = $con['username'];
              $password   = $con['password']; 
              $connection = $con['db_name'];
          }
         
          $connect    = odbc_connect($connection, $username, $password);            


          $document_no_arr = array(); 
          $column          = '[Document No_]';
          $table_query     = "SELECT * FROM ".$table."WHERE ".$column." = '".$search."'";
          $table_row       = odbc_exec($connect, $table_query);     

          if(odbc_num_rows($table_row) > 0)
          {
                 while(odbc_fetch_row($table_row))
                 {
                       $Document_no = odbc_result($table_row, 2);                      
                       $Amount      = odbc_result($table_row, 19); 

                       if(!in_array($Document_no,$document_no_arr))  
                       {
                         array_push($document_no_arr,$Document_no);
                       } 
                 }
          }    




          $data['html'] = $document_no_arr; 
          echo json_encode($data);

    }



    function  home()
    {
          $html ='';
          $table_header = array("SALES INVOICE NO.","ACTION");
          $table_id     = "sales_inv_tbl";
          $html        .= $this->simplify->populate_header_table($table_id,$table_header);

       


          $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#sales_inv_tbl").dataTable({                            
                                         "order":[0,"asc"]
                                       });

                             </script>';

          $data['html'] = $html; 
          echo json_encode($data);
    }




    function home_()  //ok ra ni nga code
    {
        
          $html ='';

          $table_header = array("SALES INVOICE NO.","ACTION");
          $table_id     = "sales_inv_tbl";
          $html        .= $this->simplify->populate_header_table($table_id,$table_header);

          $connection = "ODBC_Sample";
          $username   = "sa";
          $password   = "Corporate_it"; 
          $table      = '[Marcela Pharma Distributor Inc$Sales Invoice Line]';
          $connect    = odbc_connect($connection, $username, $password);

          $document_no_arr = array(); 

          $table_query = "SELECT * FROM ".$table;
          $table_row   = odbc_exec($connect, $table_query);         

          if(odbc_num_rows($table_row) > 0)
          {
                 while(odbc_fetch_row($table_row))
                 {
                     $Document_no         = odbc_result($table_row, 2);
                     $Sell_to_Customer_No = odbc_result($table_row, 4);  
                     $address             = odbc_result($table_row, 7);  
                     $Quantity            = odbc_result($table_row, 13);  
                     $Item_no             = odbc_result($table_row, 6);  
                     $Description         = odbc_result($table_row, 10);  
                     $Lot_no              = odbc_result($table_row, 106);  
                     $Expiry_date         = odbc_result($table_row, 107);  
                     $Deal                = odbc_result($table_row, 108);
                     $Line_discount       = odbc_result($table_row, 17);
                     $unit_price          = odbc_result($table_row, 14);                     
                     $Line_disc_amount    = odbc_result($table_row, 18);
                     $Amount              = odbc_result($table_row, 19);
                     $net_price           = $Amount/$Quantity;

                     if(!in_array("$Document_no",$document_no_arr))  
                     {
                         array_push($document_no_arr,$Document_no);
                     }                   
                 }
          }
           
          for($a=0;$a<count($document_no_arr);$a++)
          {
                 $row1   = array(
                                      $document_no_arr[$a], 
                                      '<button class="btn btn-danger btn-xs"  onclick="view_details('."'".$document_no_arr[$a]."'".')">view</button>'                                               
                                );
                             
                 $style1 = array(
                                     "text-align:center;",
                                     "text-align:center;"
                                );
                 $tr_class ='tr_';
                 $html  .= $this->simplify->populate_table_rows($row1,$style1,$tr_class);
           }

           
           $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#sales_inv_tbl").dataTable({                            
                                         "order":[0,"asc"]
                                       });

                             </script>';

           $data['html'] = $html; 

           echo json_encode($data);
    }



    function mpdi_ui(){
        if($this->Mpdi_mod->getUserType($_SESSION['user_id'])=="Admin")
            redirect(base_url('Mpdi_ctrl/usersPage'));
        else{
           $data['active_nav'] = $this->Mpdi_mod->retrieveUser($_SESSION["user_id"]);
            $this->load->view("mpdi/head_ui",$data);
            $this->load->view("mpdi/mpdi_ui"); 
        }  
    }


    function get_header($pdf,$border,$sold_to,$Sales_inv_no,$address,$so_doc_no,$salesman,$account_type,$posting_date,$batch_no,$ext_doc_no)
    {
         $pdf->ln(10);
         $pdf->Cell(20,5,'',$border,0,'L',0);
         $pdf->Cell(50,5,$sold_to,$border,0,'L',0);
         $pdf->Cell(135,5,'',$border,0,'L',0);
         $pdf->Cell(50,5,$Sales_inv_no,$border,0,'L',0);
         $pdf->ln(5);
         $pdf->Cell(20,5,'',$border,0,'L',0);
         $pdf->Cell(50,5,$address,$border,0,'L',0);
         $pdf->Cell(135,5,'',$border,0,'L',0);
         $pdf->Cell(50,5,$so_doc_no,$border,0,'L',0);
         $pdf->ln(5);
         $pdf->Cell(20,5,'',$border,0,'L',0);
         $pdf->Cell(50,5,$salesman,$border,0,'L',0);
         $pdf->Cell(20,5,'',$border,0,'L',0);
         $pdf->Cell(115,5,$account_type,$border,0,'L',0);
         $pdf->Cell(50,5,date('Y-m-d',strtotime(date($posting_date))),$border,0,'L',0);
         $pdf->ln(5);
         $pdf->Cell(20,5,'',$border,0,'L',0);
         $pdf->Cell(50,5,$batch_no,$border,0,'L',0);
         $pdf->Cell(135,5,'',$border,0,'L',0);
         $pdf->Cell(50,5,$ext_doc_no,$border,0,'L',0);

         $pdf->ln(4);
    } 



    function get_total_row($pdf,$row_height,$border,$total_unit_price,$total_net_price,$total_line_disc,$total_amt)
    {
         $pdf->ln(8);    
         $pdf->Cell(10,$row_height, ''                                 ,$border,0,'L',0);
         $pdf->Cell(10,$row_height, ''                                 ,$border,0,'L',0);
         $pdf->Cell(14,$row_height, ''                                 ,$border,0,'L',0);
         $pdf->Cell(95,$row_height,''                                  ,$border,0,'L',0);
         $pdf->Cell(15,$row_height, ''                                 ,$border,0,'L',0);
         $pdf->Cell(20,$row_height, ''                                 ,$border,0,'L',0);
         $pdf->Cell(12,$row_height, ''                                 ,$border,0,'L',0);
         $pdf->Cell(12,$row_height, ''                                 ,$border,0,'R',0);
         $pdf->Cell(20,$row_height, number_format($total_unit_price,2) ,$border,0,'R',0);
         $pdf->Cell(20,$row_height, number_format($total_net_price,2)  ,$border,0,'R',0);
         $pdf->Cell(20,$row_height, number_format($total_line_disc,2)  ,$border,0,'R',0);
         $pdf->Cell(20,$row_height, number_format($total_amt,2)        ,$border,0,'R',0);
    }

    function get_row($pdf,$row_height,$border,$Quantity,$Unit_of_Measure,$Item_no,$Description,$Lot_no,$Expiry_date,$Deal,$Line_discount,$unit_price,$net_price,$Line_disc_amount,$Amount)
    {
          $pdf->ln(8);        
          $pdf->Cell(10,$row_height, $Quantity                            ,$border,0,'L',0);
          $pdf->Cell(10,$row_height, $Unit_of_Measure                     ,$border,0,'L',0);
          $pdf->Cell(14,$row_height, $Item_no                             ,$border,0,'L',0);
          $pdf->Cell(95,$row_height, $Description                         ,$border,0,'L',0);
          $pdf->Cell(15,$row_height, $Lot_no                              ,$border,0,'L',0);
          $pdf->Cell(20,$row_height, $Expiry_date                         ,$border,0,'L',0);
          $pdf->Cell(12,$row_height, $Deal                                ,$border,0,'L',0);
          $pdf->Cell(12,$row_height, $Line_discount                       ,$border,0,'R',0);
          $pdf->Cell(20,$row_height, $unit_price                          ,$border,0,'R',0);
          $pdf->Cell(20,$row_height, $net_price                           ,$border,0,'R',0);
          $pdf->Cell(20,$row_height, $Line_disc_amount                    ,$border,0,'R',0);
          $pdf->Cell(20,$row_height, $Amount                              ,$border,0,'R',0);
    }




    function get_row_data($Quantity,$Unit_of_Measure,$Item_no,$Description,$Lot_no,$Expiry_date,$Deal,$Line_discount,$unit_price,$net_price,$Line_disc_amount,$Amount,$get_pad_number,$Document_no)
    {

         $alignment = 'text-align:right;';

         $tbl = '<tr style="color:blue;">
                                <td style="'.$alignment.'" >'.$Quantity.'</td>
                                <td>'.$Unit_of_Measure.'</td> 
                                <td>'.$Item_no.'</td>
                                <td style="height: 28px;">'.$Description.'</td>
                                <td>'.$Lot_no.'</td>
                                <td>'.$Expiry_date.'</td>
                                <td>'.$Deal.'</td>
                                <td style="'.$alignment.'" >'.$Line_discount.'</td>
                                <td style="'.$alignment.'" >'.$unit_price.'</td>
                                <td style="'.$alignment.'" >'.$net_price.'</td>
                                <td style="'.$alignment.'" >'.$Line_disc_amount.'</td>
                                <td style="'.$alignment.'" >'.$Amount.'</td>
                          </tr> 
                         ';
        if($Document_no != '')
        {
          $Amount      =   preg_replace("/,/i", '', $Amount);
          $$unit_price =   preg_replace("/,/i", '', $$unit_price);
          $$net_price  =   preg_replace("/,/i", '', $$net_price);



          //$this->Mpdi_mod->insert_sales_invoice_line($Document_no,$Quantity,$Item_no,$Description,$Lot_no,date('Y-m-d',strtotime(date($Expiry_date))),$Deal,$Line_discount,$unit_price,$Line_disc_amount,$Amount,$Unit_of_Measure,$net_price,$get_pad_number);               
        }                 

         return $tbl;
    }

    function get_row_header($border)
    {

        //border 1 ->naay border  0->walay border
        /*$tbl = '
                     <table border="'.$border.'" style="font-size: 7px;">
                         <tr style="text-align: center;font-weight: bold;">
                                 <th style="width:21px;vertical-align: top;">                                     
                                        QTY
                                 </th>
                                 <th style="width:31px;">
                                        UOM
                                 </th>
                                 <th style="width:35px;">                                        
                                        Item No.
                                 </th>
                                 <th style="width:170px;">                                        
                                        Description
                                 </th>
                                 <th style="width:33px;">
                                        Lot No.
                                 </th>
                                 <th style="width:42px;">
                                        Expiry Date 
                                 </th>
                                 <th style="width:24px;">
                                        Deal 
                                 </th>
                                 <th style="width:40px;">                                              
                                        Discount  
                                           %
                                 </th>
                                 <th style="width:48px;">
                                        Unit Price
                                 </th>
                                 <th style="width:48px;">
                                        Net Price
                                 </th>
                                 <th style="width:40px;">
                                          Discount 
                                           Amount
                                 </th>
                                 <th style="width:48px;">
                                        Amount
                                 </th>
                         </tr>        
                '; */  
            $tbl = '
                     <table border="'.$border.'" style="font-size: 7px;">
                         <tr style="text-align: center;font-weight: bold;">
                                 <th style="width:21px;vertical-align: top;">                                     
                                        
                                 </th>
                                 <th style="width:31px;">
                                      
                                 </th>
                                 <th style="width:35px;">                                        
                                        
                                 </th>
                                 <th style="width:170px;height:30px;">                                        
                                       
                                 </th>
                                 <th style="width:33px;">
                                       
                                 </th>
                                 <th style="width:42px;">
                                        
                                 </th>
                                 <th style="width:24px;">
                                        
                                 </th>
                                 <th style="width:40px;">                                              
                                        
                                 </th>
                                 <th style="width:48px;">
                                       
                                 </th>
                                 <th style="width:48px;">
                                       
                                 </th>
                                 <th style="width:40px;">
                                         
                                 </th>
                                 <th style="width:48px;">
                                       
                                 </th>
                         </tr>        
                ';       

           return  $tbl;    
    }


    function header_line($border,$first_column,$second_column,$third_column,$text1,$text2)
    {
         $tbl = '<table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:7px;color:blue;">
                     <tr>
                          <th style="'.$first_column.'"></th>
                          <th style="'.$second_column.'">'.$text1.'</th>
                          <th style="'.$third_column.'">'.$text2.'</th>                  
                     </tr>
                </table>
               '; 

         return $tbl;      

    }

    function get_salesman($salesman_code)
    {
        /*database connection to navision -----------------------------------------------------------------*/
         $Document_num = $_POST['document_num'];

         $table          = '[PHARMA WHOLESALE TEST$Salesperson_Purchaser]';
         $get_connection = $this->Mpdi_mod->get_connection();
         foreach($get_connection  as $con)
         {
              $username   = $con['username'];
              $password   = $con['password']; 
              $connection = $con['db_name'];
         }

         /*$connection   = "ODBC_Sample";
         $username     = "sa";
         $password     = "Corporate_it"; 
         $table        = '[Marcela Pharma Distributor Inc$Salesperson_Purchaser]';*/
         $connect      = odbc_connect($connection, $username, $password); 
         $table_query  = "SELECT * FROM ".$table." WHERE [Code]='".$salesman_code."'";
         $table_row    = odbc_exec($connect, $table_query);  
         /*end of database connection to navision ----------------------------------------------------------*/

         $name = '';

         if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {
                $name = odbc_result($table_row, 3);
             }
          }   

          return $name;
    }

    

    function get_page_header($border,$Sell_to_Customer_No,$Document_no ,$address,$Shipment_Date,$get_pad_number)
    {
         $tbl = ''; 

        /*database connection to navision -----------------------------------------------------------------*/
         $Document_num = $_POST['document_num'];

         $table          = '[PHARMA WHOLESALE TEST$Sales Invoice Header]';
         $get_connection = $this->Mpdi_mod->get_connection();
         foreach($get_connection  as $con)
         {
              $username   = $con['username'];
              $password   = $con['password']; 
              $connection = $con['db_name'];
         }

         /*$connection   = "ODBC_Sample";
         $username     = "sa";
         $password     = "Corporate_it"; 
         $table        = '[Marcela Pharma Distributor Inc$Sales Invoice Header]';*/
         $connect      = odbc_connect($connection, $username, $password); 
         $table_query  = "SELECT * FROM ".$table." WHERE [No_]='".$Document_no."'";
         $table_row    = odbc_exec($connect, $table_query);  
         /*end of database connection to navision ----------------------------------------------------------*/

          if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {
                $Sell_to_Customer_No_ = odbc_result($table_row, 3);
                $bill_to_name         = odbc_result($table_row, 5);
                $Sell_to_Customer_No  = odbc_result($table_row, 3)." - ".odbc_result($table_row, 5);
                $address              = odbc_result($table_row, 7);
                $salesman_code        = odbc_result($table_row, 39);
                $account_type         = odbc_result($table_row, 23);
                $posting_date         = odbc_result($table_row, 20);
                $due_Date             = odbc_result($table_row, 24);
                $batch_no             = odbc_result($table_row, 102);
                $ext_doc_no           = odbc_result($table_row, 73);
                $so_doc_no            = odbc_result($table_row, 40);
                $si_no                = odbc_result($table_row, 2);
             }
         }

         $salesman_name = $this->get_salesman($salesman_code);
         $salesman      = $salesman_code." - ".$salesman_name; 


         //$this->Mpdi_mod->insert_header($Document_no,$Sell_to_Customer_No_,$bill_to_name,$address,$salesman_code,$account_type,$posting_date,$due_Date,$batch_no,$ext_doc_no,$salesman_name,$get_pad_number,'');          
        

        $first_column  = 'width:230px;';
        $second_column = '';
        $third_column  = ''; 
       // $text1         = 'MARCELA PHARMA DISTRIBUTION INC.' ;  
        $text1         = '' ;  
        $text2         = '';           
        $tbl          .= $this->header_line($border,$first_column,$second_column,$third_column,$text1,$text2);       

        $first_column  = 'width:292px;height:12px;'; 
        $second_column = '';  
        $third_column  = '';  
        //$text1         = 'Tagbilaran City' ;
        $text1         = '' ;
        $text2         = '';
        $tbl          .= $this->header_line($border,$first_column,$second_column,$third_column,$text1,$text2);   


        $first_column  = 'width:170px;'; 
        $second_column = 'width:155px;';
        $third_column  = '';
        /*$text1         = 'VAT Reg. TIN#:_______________';
        $text2         = 'Permit #:_______________';*/
        $text1         = '';
        $text2         = '';
        $tbl          .= $this->header_line($border,$first_column,$second_column,$third_column,$text1,$text2);   



        $first_column  = 'width:210px;'; 
        $second_column = 'width:115px;';
        $third_column  = '';
        /*$text1         = 'MIN#:_______________'; 
        $text2         = 'SN:_______________';*/
        $text1         = ''; 
        $text2         = '';
        $tbl          .= $this->header_line($border,$first_column,$second_column,$third_column,$text1,$text2);   

                
        $first_column  = 'width:25px;'; 
        $second_column = 'width:330px;';
        $third_column  = '';
        //$text1         = 'SALES INVOICE';
        $text1         = $Sell_to_Customer_No;
        $text2         = '';
        $tbl          .= $this->header_line($border,$first_column,$second_column,$third_column,$text1,$text2); 


        $firstColumn   = 'width:25px;';   
        $first_column  = 'width:250px;'; 
        $second_column = 'width:135px;'; 
        $third_column  = 'width:120px;';
        $fourth_column = 'width:70px;';

        //$ref_no  =  $get_pad_number;
        $ref_no  =   $si_no;
        $ref_no  =   preg_replace("/SPIN/i", '', $ref_no);
        $so_doc_no           =   preg_replace("/SO/i", '', $so_doc_no);

        $tbl .= '<table cellspacing="1" cellpadding="1" border="'.$border.'"  style="font-size:7px;color:blue;">
                     <tr>
                          <th style="'.$firstColumn.'"></th>
                          <th style="'.$first_column.'">'.$get_pad_number.'</th>
                          <th style="'.$second_column.'"></th>
                          <th style="'.$third_column.'" >'.date('m-d-Y',strtotime(date($posting_date))).'</th>    
                          <th style="'.$fourth_column.'"></th>                      
                     </tr>
                     <tr>
                          <td style="'.$firstColumn.'"></td>
                          <td style="'.$first_column.'">'.$address.'</td>
                          <td style="'.$second_column.'"></td>
                          <td style="'.$third_column.'">'.$account_type.'</td> 
                          <td style="'.$fourth_column.'"></td>                         
                     </tr>
                     <tr>
                          <td style="'.$firstColumn.'"></td>
                          <td style="'.$first_column.'">'.$salesman.'</td> 
                          <td style="'.$second_column.'"></td> 
                          <td style="'.$third_column.'">'.date('m-d-Y',strtotime(date($due_Date))).'</td> 
                          <td style="'.$fourth_column.'"></td>
                     </tr>
                     <tr>
                          <td style="'.$firstColumn.'"></td>
                          <td style="width:35px;">'.$batch_no.'</td> 
                          <td style="width:350px;">'.$ref_no.'</td> 
                          <td style="'.$third_column.'"></td> 
                          <td style="'.$fourth_column.'"></td>
                     </tr>
                   </table>';

        return $tbl;           
    }



    function gen_report()
    {
        // $this->load->library('tcpdf');
         $this->ppdf = new TCPDF();
         $this->ppdf->SetTitle("Marcela Pharma Distribution Inc.");
        /* $this->ppdf->SetMargins(15, 5, 10); //left top right
         $this->ppdf->setPrintHeader(false);
         $this->ppdf->SetFont('', '', 11, '', true);           
         $this->ppdf->AddPage("L"); //landscape
         $this->ppdf->AddPage("P");*/   //<----- landscape settings


         //$this->ppdf->SetMargins(2, 5, 2); //left top right
         $this->ppdf->SetMargins(5, 15, 0.20, true);
         $this->ppdf->setPrintHeader(false);
         $this->ppdf->SetFont('', '', 10, '', true);                    
         $this->ppdf->AddPage("P");
         

         $this->ppdf->SetAutoPageBreak(false);

        // $this->ppdf->Cell(120,5, "ICM Northwing, Dampas District, Tagbilaran City",0,1,'C');
          /* <table cellspacing="1" cellpadding="1" border="1" >
                    <tr>
                        <td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3</td>
                        <td>COL 2 - <br> ROW 1</td>
                        <td>COL 3 - ROW 1</td>
                    </tr>
                    <tr>
                        <td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text </td>
                        <td>COL 3 - ROW 2</td>
                    </tr>
                    <tr>
                       <td>COL 3 - ROW 3</td>
                    </tr>
                 
                </table>*/ 

      /*  $tbl = '
                <table cellspacing="1" cellpadding="1" border="1" >
                    <tr>
                        <td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3</td>                        
                    </tr>
                    <tr>
                        <td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text </td>                         
                    </tr>
                    <tr>
                       <td>COL 3 - ROW 3</td>
                    </tr>
                     <tr>
                       <td>COL  - ROW 3</td>
                    </tr>                 
                </table>
            ';*/

         $tbl  = '';    
         
                

/*database connection to navision -----------------------------------------------------------------*/
         $table          = '[PHARMA WHOLESALE TEST$Sales Invoice Line]';
         $get_connection = $this->Mpdi_mod->get_connection();
         foreach($get_connection  as $con)
         {
              $username   = $con['username'];
              $password   = $con['password']; 
              $connection = $con['db_name'];
         }

         $Document_num   = $_POST['document_num'];
         $get_pad_number = $_POST['get_pad_number']; 
        /* $connection   = "ODBC_Sample";
         $username     = "sa";
         $password     = "Corporate_it"; 
         $table        = '[Marcela Pharma Distributor Inc$Sales Invoice Line]';*/
         $connect      = odbc_connect($connection, $username, $password); 
         $table_query  = "SELECT * FROM ".$table." WHERE [Document No_]='".$Document_num."'";
         $table_row    = odbc_exec($connect, $table_query);  
/*end of database connection to navision ----------------------------------------------------------*/
         $total_unit_price = 0.00;
         $total_net_price  = 0.00;
         $total_line_disc  = 0.00;
         $total_amt        = 0.00;
         $line_counter     = 1;   
         $border           = 0;

         if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {

                 $Document_no         = odbc_result($table_row, 2);
                 $Sell_to_Customer_No = odbc_result($table_row, 4);  
                 $address             = odbc_result($table_row, 7);  
                 $Quantity            = odbc_result($table_row, 13);  
                 $Item_no             = odbc_result($table_row, 6);  
                 $Description         = odbc_result($table_row, 10); 
                 $generic             = $this->Mpdi_mod->get_generic_name($Item_no); 
                 if(!empty($generic))
                 {
                      $Description .=  "<br>[".$generic[0]['generic_name']."]";   
                 }                  

                 //$Lot_no              = odbc_result($table_row, 106);  
                 $Lot_no              = odbc_result($table_row, 103);  
                 //$Expiry_date         = date('Y-m-d',strtotime(date(odbc_result($table_row, 107))) );  
                 $Expiry_date         = date('Y-m-d',strtotime(date(odbc_result($table_row, 104))) );  
                 $Deal                = odbc_result($table_row, 105);
                 $Line_discount       = odbc_result($table_row, 17);
                 $unit_price          = odbc_result($table_row, 14);                     
                 $Line_disc_amount    = odbc_result($table_row, 18);
                 //$Amount              = odbc_result($table_row, 19);
                 $Amount              = odbc_result($table_row, 20);
                 $Unit_of_Measure     = odbc_result($table_row, 12);
                 $Service_ord_no      = odbc_result($table_row, 85);
                 $Shipment_Date       = odbc_result($table_row, 9);
                 
                 $net_price           = $Amount/$Quantity;
                 $total_unit_price   += $unit_price;
                 $total_net_price    += $net_price;
                 $total_line_disc    += $Line_disc_amount ;
                 $total_amt          += $Amount;      
                  
                 $account_type        = 'account_type';

                  if($line_counter == 1 )
                       {                             
                             $tbl .= $this->get_page_header($border,$Sell_to_Customer_No,$Document_no ,$address,$Shipment_Date,$get_pad_number);   
                             $tbl .= $this->get_row_header($border);  
                             $tbl .= $this->get_row_data(number_format($Quantity),$Unit_of_Measure,$Item_no,$Description,$Lot_no,date('m-d-Y',strtotime(date($Expiry_date))),$Deal,number_format($Line_discount,2),number_format($unit_price,2),number_format($net_price,2),number_format($Line_disc_amount,2),number_format($Amount,2),$get_pad_number,$Document_no );                 
                             $line_counter += 1;      


                       }
                       else
                       if($line_counter == 14)    
                       {
                              $line_counter = 1;                                 
                                                                   
                              $tbl .= $this->get_row_data('','','','','','','','',number_format($total_unit_price,2),number_format($total_net_price,2),number_format($total_line_disc,2),number_format($total_amt,2),$get_pad_number,$Document_no );                 

                              $total_unit_price = 0.00;
                              $total_net_price  = 0.00;
                              $total_line_disc  = 0.00;
                              $total_amt        = 0.00;
                              $this->ppdf->AddPage('L');                        
                       }
                       else 
                       {
                            $line_counter += 1;     
                            $tbl .= $this->get_row_data(number_format($Quantity),$Unit_of_Measure,$Item_no,$Description,$Lot_no,date('m-d-Y',strtotime(date($Expiry_date))),$Deal,number_format($Line_discount,2),number_format($unit_price,2),number_format($net_price,2),number_format($Line_disc_amount,2),number_format($Amount,2),$get_pad_number,$Document_no );                 
                       }   

             }
         } 



         //if($line_counter<16)//   //14 entries
         if($line_counter<21)
         {
           //$remaining_lines = 16 - $line_counter;  //14 entries
             $remaining_lines = 21 - $line_counter;  //14 entries

           for($a=0;$a<$remaining_lines;$a++)
           {
               if( ($remaining_lines-1) == $a)
               {
                   $tbl .= $this->get_row_data('','','','','','','','',number_format($total_unit_price,2),number_format($total_net_price,2),number_format($total_line_disc,2),number_format($total_amt,2),'','' );                 
               }
               else 
               {
                   $tbl .= $this->get_row_data('','','','','','','','','','','','','','' );                                
               }
           }
         }



         $tbl .=' 
                    </table>
               ';                 
  

         $this->ppdf->writeHTML($tbl, true, false, false, false, '');

         ob_end_clean();
         $this->ppdf->Output();
    }

    function gen_report_()
    {
         $Document_num = $_POST['document_num'];
         $connection   = "ODBC_Sample";
         $username     = "sa";
         $password     = "Corporate_it"; 
         $table        = '[Marcela Pharma Distributor Inc$Sales Invoice Line]';
         $connect      = odbc_connect($connection, $username, $password); 
         $table_query  = "SELECT * FROM ".$table." WHERE [Document No_]='".$Document_num."'";
         $table_row    = odbc_exec($connect, $table_query);  

         $module = "Marcela Pharma Distribution Inc.";
         $cutoff = date('Y-m-d');
         $this->load->library('fpdf');
         $pdf = new fpdf();

         $pdf->AddPage('L');
         $pdf->SetFont('Arial','',10);
         $pdf->SetAutoPageBreak(TRUE, 17);
         $pdf->SetTitle($module);
          

         $border     = 1; //1-> enable border  0 -> no border
         $row_height = 8; 
         

         

         //$pdf->MultiCell(15,3,'tessdfsdfsdfsdft',$border,4);
         $total_unit_price = 0.00;
         $total_net_price  = 0.00;
         $total_line_disc  = 0.00;
         $total_amt        = 0.00;
         $line_counter     = 1;

         if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {
                      $Document_no         = odbc_result($table_row, 2);
                      $Sell_to_Customer_No = odbc_result($table_row, 4);  
                      $address             = odbc_result($table_row, 7);  
                      $Quantity            = odbc_result($table_row, 13);  
                      $Item_no             = odbc_result($table_row, 6);  
                      $Description         = odbc_result($table_row, 10);  
                      $Lot_no              = odbc_result($table_row, 106);  
                      $Expiry_date         = date('Y-m-d',strtotime(date(odbc_result($table_row, 107))) );  
                      $Deal                = odbc_result($table_row, 108);
                      $Line_discount       = odbc_result($table_row, 17);
                      $unit_price          = odbc_result($table_row, 14);                     
                      $Line_disc_amount    = odbc_result($table_row, 18);
                      $Amount              = odbc_result($table_row, 19);
                      $Unit_of_Measure     = odbc_result($table_row, 12);
                      $Service_ord_no      = odbc_result($table_row, 85);
                      $Shipment_Date       = odbc_result($table_row, 9);
                      
                      $net_price           = $Amount/$Quantity;
                      $total_unit_price   += $unit_price;
                      $total_net_price    += $net_price;
                      $total_line_disc    += $Line_disc_amount ;
                      $total_amt          += $Amount;

                      $so_doc_no           = 'so_doc_no'; 
                      $salesman            = 'salesman';
                      $account_type        = 'account_type';
                      $batch_no            = '$batch_no';
                      $ext_doc_no          = '$ext_doc_no';


                       if($line_counter == 1 )
                       {
                             $this->get_header($pdf,$border,$Sell_to_Customer_No,$Document_no ,$address,$so_doc_no,$salesman,$account_type,$Shipment_Date,$batch_no,$ext_doc_no);
                             $this->get_row($pdf,20,$border,'QTY','UOM','item No.','Description','Lot No.','Expiry Date','Deal','Line Disc. %','Unit Price','Net Price','Line Disc Amount','Amount');   
                             $pdf->ln(10); 
                             $this->get_row($pdf,$row_height,$border,number_format($Quantity),$Unit_of_Measure,$Item_no,$Description,$Lot_no,$Expiry_date,$Deal,number_format($Line_discount,2),number_format($unit_price,2),number_format($net_price,2),number_format($Line_disc_amount,2),number_format($Amount,2) );               
                             $line_counter += 1;                
                       }
                       else
                       if($line_counter == 14)    
                       {
                              $line_counter = 1;                                 
                                                                   
                              $this->get_total_row($pdf,$row_height,$border,$total_unit_price,$total_net_price,$total_line_disc,$total_amt);

                              $total_unit_price = 0.00;
                              $total_net_price  = 0.00;
                              $total_line_disc  = 0.00;
                              $total_amt        = 0.00;
                              $pdf->AddPage('L');                        
                       }
                       else 
                       {
                            $line_counter += 1;     
                            $this->get_row($pdf,$row_height,$border,number_format($Quantity),$Unit_of_Measure,$Item_no,$Description,$Lot_no,$Expiry_date,$Deal,number_format($Line_discount,2),number_format($unit_price,2),number_format($net_price,2),number_format($Line_disc_amount,2),number_format($Amount,2) );               
                       }
             }
         }

         if($line_counter<16)
         {
           $remaining_lines = 16 - $line_counter;

           for($a=0;$a<$remaining_lines;$a++)
           {
               if( ($remaining_lines-1) == $a)
               {
                   $this->get_total_row($pdf,$row_height,$border,$total_unit_price,$total_net_price,$total_line_disc,$total_amt);
               }
               else 
               {
                   $this->get_row($pdf,$row_height,$border,'','','','','','','','','','','','','','','');               
               }
           }
         }

         $pdf->Output();

    }


    // Gershom

    public function usersPage(){
        if($this->Mpdi_mod->getUserType($_SESSION['user_id'])!="Admin")
            redirect(base_url('Mpdi_ctrl/mpdi_ui'));
        else{
            $data['active_nav'] = $this->Mpdi_mod->retrieveUser($_SESSION["user_id"]);
            $this->load->view("mpdi/head_ui",$data);
            $this->load->view("mpdi/user_body");
        }
    }
   
    public function logout(){
        unset($_SESSION['user_id']);
        redirect(base_url('Mpdi_log_ctrl/index'));
    }

    public function registerClientAccount(){
        $fn = trim($_POST["u_fn"]);
        $ln = trim($_POST["u_ln"]);
        $user = $_POST["user"];
        $pass = $_POST["pass"];
        $cpass = $_POST["c_pass"];

        if($fn=="")
            echo json_encode(array("Pls Input First Name!","Error"));
        else if(!preg_match("/^[a-zA-Z\s]+$/",$fn))
            echo json_encode(array("First Name must only contain letters.","Error"));
        else if($ln=="")
            echo json_encode(array("Pls Input Last Name!","Error"));
        else if(!preg_match("/^[a-zA-Z\s]+$/",$ln))
            echo json_encode(array("Last Name must only contain letters.","Error"));
        else if($user=="")
            echo json_encode(array("Pls Input Username!","Error"));
        else if(!preg_match("/^[a-zA-Z0-9]+$/",$user))
            echo json_encode(array("Username only allows letters and numbers.","Error"));
        else if($this->Mpdi_mod->getUsernameCount($user)>0)
            echo json_encode(array("Username already exists.","Error"));
        else if($pass=="")
            echo json_encode(array("Pls Input Password!","Error"));
         else if(strlen($pass)<6)
            echo json_encode(array("Password length must be greater than 5.","Error"));
        else if(!preg_match("/^[a-zA-Z0-9]+$/",$pass))
            echo json_encode(array("Password only allows letters and numbers.","Error"));
        else if($cpass!=$pass)
            echo json_encode(array("Confirm Password must be equal to Password.","Error"));
        else{
            $res = $this->Mpdi_mod->addUserAccount($fn,$ln,$user,password_hash($pass, PASSWORD_BCRYPT));
            if($res)
                echo json_encode(array("Successfully Registered!","Success"));
            else
                echo json_encode(array("Error Occurred!","Error"));
        }
    }

    public function getListOfUsers(){
        echo json_encode($this->Mpdi_mod->retrieveUsers());
    }


    public function updateAccount(){
        $user = $_POST["update_user"];
        $pass = $_POST["oldpass"];
        $npass = $_POST["newpass"];
        $cpass = $_POST["conpass"];

        if($user!="" && !preg_match("/^[a-zA-Z0-9]+$/",$user))
            echo json_encode(array("Username only allows letters and numbers.","Error"));
        else if($user!="" && $this->Mpdi_mod->getUsernameCount($user)>0)
            echo json_encode(array("Username already exists.","Error"));
        else if($pass!="" && !password_verify($pass,$this->Mpdi_mod->retrieveUser($_SESSION["user_id"])[4]))
            echo json_encode(array("Old Password Incorrect!","Error"));
        else if($pass!="" && $npass=="")
            echo json_encode(array("Pls Input New Password!","Error"));
        else if($pass!="" && strlen($npass)<6)
            echo json_encode(array("Password length must be greater than 5.","Error"));
        else if($pass!="" && !preg_match("/^[a-zA-Z0-9]+$/",$npass))
            echo json_encode(array("Password only allows letters and numbers.","Error"));
        else if($pass!="" && $cpass!=$npass)
            echo json_encode(array("Confirm Password must be equal to New Password.","Error"));
        else{
            $elem_arr = array();
            $txtres = "";
            if($user!=""){
                $elem_arr["username"] = $user;
                $txtres .= " Username ";
            }

            if($pass!=""){
                $elem_arr["password"] = password_hash($npass, PASSWORD_BCRYPT);
                $txtres .= " Password ";
            }

            if(empty($elem_arr))
                echo json_encode(array("No Updates Made!","Info"));
            else{
                $res = $this->Mpdi_mod->updateUser($elem_arr,$_SESSION["user_id"]);
                if($res)
                    echo json_encode(array("Successfully Updated! (".$txtres.")","Success"));
                else
                    echo json_encode(array("Error Occurred!","Error"));
                    
                //echo json_encode(array($res,"Info"));
            }
        }
    }
    


    
}   