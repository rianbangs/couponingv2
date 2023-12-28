<div class="card col-12" style="margin-left:3px;">
   <div class="card-header">
       <!-- <h4 class="card-title">Vertical Form with Icons</h4> -->
   </div>
          <div class="card-content">
              <div class="card-body">
                   
                  <div class="form-body">                      
                       
                        <div class="row" style="margin-left: 221px;"> 
                        <div class="col-2">
                            <?php
                                   //if( $_SESSION['access_type'] != 'mpdi')
                                   if(!in_array($_SESSION['access_type'],array('mpdi','accounting')))
                                   {
                                         $visible = 'hidden'; 
                                   }
                                   else 
                                   {
                                         $visible = ''; 
                                   }
                             ?>
                             <fieldset class="form-group" <?php echo $visible; ?>>
                                        <select class="form-select"   id="store" onchange="validateDates()">
                                            <option value="all">all</option>
                                           <?php 
                                                    for($a=0;$a<count($store_arr);$a++)
                                                    { 
                                                        echo '<option value="'.$store_arr[$a]['db_id'].'">'.$store_arr[$a]['store'].'</option>';    
                                                    }
                                            ?>                                                                               
                                        </select>
                             </fieldset>
                        </div>                       
                          <div class="col-10" style="display: flex;">                             
                           <label for="date_from" style="margin-right: 10px;">From:</label>
                                <input type="date" id="date_from" name="date" style="font-family:sans-serif; margin-right: 10px;" onchange="validateDates()">                           
                                <label  for="date_to" style="margin-left:20px; margin-right: 10px;">To:</label>
                                <input  type="date" id="date_to" name="date" style="font-family:sans-serif; margin-right: 10px;" onchange="validateDates()">   
                                <?php if(in_array($_SESSION['access_type'],array('accounting','liquidation','mpdi')))
                                      {
                                ?>                         
                                             <a class="btn icon icon-left btn-success btn-sm" style="margin-left:20px; margin-right: 96px;" onclick="generate_eom_report()"><i data-feather="file"></i> Generate Report</a>
                                <?php } ?>             
                            <!-- <form action="" name="save_textfile" id="save_textfile" enctype="multipart/form-data" method="post">                                 
                              <input type="hidden" name="MAX_FILE_SIZE" value="8000000">                                 
                              <input type="file" class="btn btn-default" name="files[]" id="txt_file" multiple="multiple" style="display: inline-block; padding: 0px; width: 47%; margin-right: 10px;">                              
                              <button type="button" class="btn btn-info" id="view-btn" style="padding: 1px 6px; margin-right: 10px;" onclick="extract_file('EOM')"><i class="glyphicon glyphicon-upload"></i> upload Navision files</button>    
                            </form>  -->
                 
                          </div>
                        </div>



                         <script>
                                          // Get the current date
                                          var currentDate = new Date();

                                          // Format the current date as "yyyy-MM-dd"
                                          var formattedDate = currentDate.toISOString().substr(0, 10);

                                          // Set the value of the input field to the current date
                                          document.getElementById("date_from").value = formattedDate;
                                          document.getElementById("date_to").value = formattedDate;


                                              // Add an event listener to the "date_to" field to validate it against "date_from"
                                        function validateDates()
                                        {
                                            var dateFrom = new Date($("#date_from").val());
                                            var dateTo = new Date($("#date_to").val());
                                            if (dateTo < dateFrom) 
                                            {
                                                //alert("Date To cannot be less than Date From!");
                                                swal_display('error','opps','There is no transaction ahead from the current day');                                                
                                                document.getElementById("date_from").value = formattedDate;
                                                document.getElementById("date_to").value = formattedDate;
                                            }
                                            else 
                                            {
                                                var table         = $('#eod-table').DataTable(); 
                                                loader_();
                                                $.ajax({
                                                            type:'POST',
                                                           // url:'<?php echo base_url(); ?>Cuponing_ctrl/load_eod_ui_filter',
                                                            url:'<?php echo base_url(); ?>Cuponing_ctrl/EOM_report',
                                                            data:{
                                                                    'dateFrom':$("#date_from").val(),
                                                                     'dateTo':$("#date_to").val(),
                                                                     'store': $("#store").val()

                                                                 },
                                                            dataType:'JSON',
                                                            success: function(data)
                                                            {


                                                                   swal.close(); 
                                                                   table.clear().draw(); //gi clear una para populatetan balik ang table
                                                                   console.log(data.eod_list);
                                                                    
                                                                   for(var a=0;a<data.eod_list.length;a++)
                                                                   {
                                                                         
                                                                        var newRow = [
                                                                                         data.eod_list[a].branch_name,
                                                                                         data.eod_list[a].date,
                                                                                         data.eod_list[a].time,
                                                                                         data.eod_list[a].product_name,                                                                                         
                                                                                         data.eod_list[a].discount,                                                                                              
                                                                                         data.eod_list[a].discount_amount,                                                                                              
                                                                                         data.eod_list[a].qty,                                                                                              
                                                                                         data.eod_list[a].Receipt,                                                                                             
                                                                                         data.eod_list[a].promo_code                                                                                            
                                                                                     ];                                 
                                                                           
                                                                          // Add the new row to the DataTable
                                                                         var rowNode = table.row.add(newRow).draw().node();                            
                                                                         $(rowNode).find('td:nth-child(5), td:nth-child(6)').css(
                                                                         {
                                                                              'color': 'black',
                                                                              'font-family': 'sans-serif',
                                                                              'text-align': 'right'
                                                                         });
                                                                         
                                                                         $(rowNode).find('td').not(':nth-child(5), :nth-child(6)').css(
                                                                         {
                                                                              'color': 'black',
                                                                              'font-family': 'sans-serif',
                                                                              'text-align': 'right'
                                                                         });
                                                                   }

                                                            }                                                                 
                                                       });
                                            }
                                        } 

                                         
                         </script>

                      
                      <br>                 
                      <div class="row">                      
                             <div class="col-12 table-responsive">
                                    <table id="eod-table" class="table  table-bordered table-hover" style="font-family:sans-serif;">
                                         <thead>
                                           <tr style="background-color:#d2eaf7;text-align:center;">
                                             <th>BRANCH NAME</th>
                                             <th>DATE</th>
                                             <th>TIME</th>
                                             <th>PRODUCT NAME (SKU)</th>     
                                             <th>DISCOUNT</th>  
                                             <th>DISCOUNT AMOUNT</th>                                                                                       
                                             <th>QTY PURCHASED</th>
                                             <th>RECEIPT</th>
                                             <th>PROMO CODE</th>
                                           </tr>
                                         </thead>
                                     <tbody>
                                        <?php 

                                                for($a=0;$a<count($eod_list);$a++)
                                                {
                                                       // $user_data = $this->Cupon_mod->get_user_connection();

                                                       // $get_connection = $this->Cupon_mod->get_connection($user_data[0]['db_id']);
                                                       // foreach($get_connection  as $con)
                                                       // {
                                                       //      $username    = $con['username'];
                                                       //      $password    = $con['password']; 
                                                       //      $connection  = $con['db_name'];
                                                       //      $sub_db_name = $con['sub_db_name'];
                                                       //  }


                                                       //  $connect      = odbc_connect($connection, $username, $password);
                                                       //  $table        = '['.$sub_db_name.'$Item]';
                                                       //  $table_query  = "SELECT * FROM ".$table." WHERE [No_] = '".$cup['item_code']."'";
                                                       //  $table_row    = odbc_exec($connect, $table_query);  
                                                       //  if(odbc_num_rows($table_row) > 0)
                                                       //  {
                                                       //      while(odbc_fetch_row($table_row))
                                                       //      {
                                                       //           $brand_name     = odbc_result($table_row, 4);  
                                                       //           $generic_name   = odbc_result($table_row, 104);  

                                                       //      }
                                                       //  }



                                                    $style ='font-family:sans-serif;font-size: 16px;color:black;'; 
                                                    echo '<tr>
                                                                <td style="'.$style.'">'.$eod_list[$a]['branch_name'].'</td>
                                                                <td style="'.$style.'">'.$eod_list[$a]['date'].'</td>
                                                                <td style="'.$style.'">'.$eod_list[$a]['time'].'</td>
                                                                <td style="'.$style.'">'.$eod_list[$a]['product_name'].'</td>
                                                                <td style="'.$style.'text-align:right;">'.$eod_list[$a]['discount'].'</td>
                                                                <td style="'.$style.'text-align:right;">'.$eod_list[$a]['discount_amount'].'</td>
                                                                <td style="'.$style.'text-align:right;">'.$eod_list[$a]['qty'].'</td>
                                                                <td style="'.$style.'text-align:right;">'.$eod_list[$a]['Receipt'].'</td>
                                                                <td style="'.$style.'">'.$eod_list[$a]['promo_code'].'</td>
                                                         </tr>';
                                                }


                                        ?>                                       
                                     </tbody>
                                    </table>
                             </div>
                          

                      </div>
                  </div>
              </div>
          </div>
</div>
<script>
       $("#eod-table").dataTable(
       {                            
             "order":false,
             "columnDefs": [
                             { "width": "90px", "targets": 0 },
                             { "width": "70px", "targets": 1 },
                             { "width": "70px", "targets": 2 },
                             { "width": "300px", "targets": 3 }
                           ]
       });
       <?php echo "swal.close();" ?>
</script>