<?php 

class Item_ctrl extends CI_Controller
{	
	 function __construct()
     {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('WMS_mod');
        $this->load->model('simplify/simplify','simplify');
     }

      function dashboard()
     {
         $this->load->view('Dashboard');
     }


     function display_item()
     {       

         /*$table          = '[Islands City Mall - SM Backend$Item]';        
         $table_query = "SELECT * FROM ".$table." WHERE  [No_] <  50";   */      
         
         $table_id       = 'table_item';
         $table_header   = array("Item code","Description","Details");
         $html           = $this->simplify->populate_header_table($table_id,$table_header);


         /*$table_row   = $this->get_connection($table_query); 
         if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {
                $Description = odbc_result($table_row, 2);
                $No_         = odbc_result($table_row, 4);
                $row1        = array(
                                       $Description,
                                       $No_
                                    );
                $style1      = array(
                                      "text-align:center;",
                                      "text-align:center;"
                                    );     
                $tr_class    ='tr_';                    
                $html       .= $this->simplify->populate_table_rows($row1,$style1,$tr_class);
             } 
         }*/


         $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#table_item").dataTable({                            
                                         "order":[0,"asc"],
                                         searching: false, 
                                         paging: true, 
                                         info: false
                                       });

                             </script>';
                                 
         $data['html'] = $html;
         echo json_encode($data);

        
     }

     function item_dashboard()
     {
         $this->load->view('item/Item');  
         $this->load->view('item/Item_js'); 
     }


     function get_connection($table_query)
     {
         $get_connection = $this->WMS_mod->get_connection();
         
         $username       = $get_connection[0]['username'];
         $password       = $get_connection[0]['password']; 
         $connection     = $get_connection[0]['db_name'];
         $connect        = odbc_connect($connection, $username, $password);                   
         $table_row      = odbc_exec($connect, $table_query);  
         return  $table_row ;
     } 


     function item_details()
     {
          $item_no        = $_POST['item_no']; 
          $uom            = $_POST['uom'];
          $table          = '[Islands City Mall - SM Backend$Barcodes]';    
          $table_query    = "SELECT * FROM ".$table." WHERE [Item No_] = '".$item_no."' AND [Unit of Measure Code] = '".$uom."' ";
          $table_id       = 'table_item_details';
          $table_header   = array("Description","Barcode","Last date modified");  
          $html           = $this->simplify->populate_header_table($table_id,$table_header);

          $table_row      = $this->get_connection($table_query);  
          if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {
                  $Description   = odbc_result($table_row, 5);
                  $Barcode       = odbc_result($table_row, 2);
                  $last_modified = odbc_result($table_row, 10);
                  $row1          = array(
                                             $Description,
                                             $Barcode,
                                             date('F d, Y',strtotime(date($last_modified)))
                                        );
                  $style1        = array(
                                             "text-align:center;",
                                             "text-align:center;",
                                             "text-align:center;"
                                        ); 
                  $tr_class    ='tr_';                    
                  $html       .= $this->simplify->populate_table_rows($row1,$style1,$tr_class);
             }
         }


         $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#table_item_details").dataTable({                            
                                         "order":[2,"asc"],
                                         searching: false, 
                                         paging: true, 
                                         info: false
                                       });
                             </script>';

        $data['html'] = $html;   
         echo json_encode($data);



     }


     function search_item()
     {
         $No_            = strtoupper($_POST['search']);
         $html           = ''; 
         $table          = '[Islands City Mall - SM Backend$Item]';       
         
         $table_query    = "SELECT * FROM ".$table." WHERE ( [No_] like '%".$No_."%'   OR  [Description] like '%".$No_."%' )";
         $table_id       = 'table_item';
         $table_header   = array("Item code","Description","Details");
         $html          .= $this->simplify->populate_header_table($table_id,$table_header);

         $table_row      = $this->get_connection($table_query);  

         if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {
                $Description     = odbc_result($table_row, 4);
                $No_             = odbc_result($table_row, 2);
                $unit_of_measure = odbc_result($table_row, 8);
                $button      = '<button type="button" class="btn btn-primary" onclick="item_details('."'".$No_."','".$unit_of_measure."'".')"><i class="bx bxs-layer me-1"></i>details</button>';
               
                $row1        = array(                                       
                                       $No_,
                                       $Description,
                                       $button
                                    );
                $style1      = array(
                                      "text-align:center;",
                                      "text-align:center;",
                                      "text-align:center;"
                                    );     
                $tr_class    ='tr_';                    
                $html       .= $this->simplify->populate_table_rows($row1,$style1,$tr_class);
             } 
         }


         $html .= '
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#table_item").dataTable({                            
                                         "order":[0,"asc"],
                                         searching: false, 
                                         paging: true, 
                                         info: false
                                       });
                             </script>';

        $data['html'] = $html;   
         echo json_encode($data);
     }



     

     //[Islands City Mall - SM Backend$Barcodes]
}