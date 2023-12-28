<div class="card col-12" style="margin-left:3px;">
   <div class="card-header">
       <!-- <h4 class="card-title">Vertical Form with Icons</h4> -->
   </div>
          <div class="card-content">
              <div class="card-body">
                   
                  <div class="form-body">                      
                       
                         <div class="row">
                          <div class="col-2">
                            <?php
                                   if(in_array($_SESSION['access_type'],array('accounting')))
                                   {
                                         $visible = '';                                    }
                                   else 
                                   {
                                         $visible = 'hidden'; 
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
                          <div class="col-7" style="display: flex;">                             
                              <label for="date_from">From:</label>
                              <input type="date" id="date_from" name="date" style="font-family:sans-serif;" onchange="validateDates()">                           
                              <label for="date_to" style="margin-left:20px;">To:</label>
                              <input type="date" id="date_to" name="date" style="font-family:sans-serif;" onchange="validateDates()">   
                              <?php if( in_array($_SESSION['access_type'],array('mpdi','accounting','liquidation') ) )
                                    { 
                               ?>                         
                                          <a class="btn icon icon-left btn-success btn-sm" style="margin-left:20px;" onclick="generate_Monitoring_List_report()"><i data-feather="file"></i> Generate Report</a>                            
                             <?php  } ?>
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
                                                swal_display('error','opps','DATE TO  cannot be less than DATE FROM!');                                                
                                                document.getElementById("date_from").value = formattedDate;
                                                document.getElementById("date_to").value = formattedDate;
                                            }
                                            else 
                                            {
                                                var table         = $('#sales-table').DataTable(); 
                                                loader_();
                                                $.ajax({
                                                            type:'POST',
                                                            url:'<?php echo base_url(); ?>Cuponing_ctrl/load_sales_report_ui_Filter',
                                                            data:{
                                                                    'dateFrom':$("#date_from").val(),
                                                                     'dateTo':$("#date_to").val(),
                                                                     'store':$("#store").val()     
                                                                 },
                                                            dataType:'JSON',
                                                            success: function(data)
                                                            {



                                                                   table.clear().draw(); //gi clear una para populatetan balik ang table
                                                                    swal.close(); 
                                                                    
                                                                   for(var a=0;a<data.cupon_list.length;a++)
                                                                   {
                                                                         
                                                                        var newRow = [
                                                                                         data.cupon_list[a].quantity,
                                                                                         data.cupon_list[a].item_code,
                                                                                         data.cupon_list[a].brand_name,
                                                                                         data.cupon_list[a].generic,                                                                                         
                                                                                         data.cupon_list[a].uom,                                                                                              
                                                                                     ];                                 
                                                                           
                                                                          // Add the new row to the DataTable
                                                                         var rowNode = table.row.add(newRow).draw().node();                            
                                                                         $(rowNode).find('td').css({
                                                                                                      'color': 'black',
                                                                                                      'font-family': 'sans-serif',
                                                                                                      'text-align': 'left'
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
                                    <table id="sales-table" class="table table-bordered table-hover" style="font-family:sans-serif;font-size: 16px;">
                                         <thead>
                                           <tr style="background-color:#d2eaf7;text-align:center;">
                                             <th>Total Quantity</th>
                                             <th>Items No</th>
                                             <th>Brand Name</th>
                                             <th>Generic Name</th>     
                                             <th>UOM</th>                                                                                         
                                           </tr>
                                         </thead>
                                     <tbody>
                                        <?php 
                                                foreach($cupon_list as $cup)
                                                {
                                                       $user_data = $this->Cupon_mod->get_user_connection();

                                                       // $get_connection = $this->Cupon_mod->get_connection($user_data[0]['db_id']);
                                                       $get_connection = $this->Cupon_mod->get_connection(91); //dbname: PHARMA_ITEM_SQL  server:172.16.161.45 
                                                       foreach($get_connection  as $con)
                                                       {
                                                            $username    = $con['username'];
                                                            $password    = $con['password']; 
                                                            $connection  = $con['db_name'];
                                                            $sub_db_name = $con['sub_db_name'];
                                                        }


                                                        $connect      = odbc_connect($connection, $username, $password);
                                                        $table        = '['.$sub_db_name.'$Item]';
                                                        $table_query  = "SELECT * FROM ".$table." WHERE [No_] = '".$cup['item_code']."'";
                                                        $table_row    = odbc_exec($connect, $table_query);  
                                                        if(odbc_num_rows($table_row) > 0)
                                                        {
                                                            while(odbc_fetch_row($table_row))
                                                            {
                                                                 $brand_name     = odbc_result($table_row, 4);  
                                                                 $generic_name   = odbc_result($table_row, 104);  

                                                            }
                                                        }



                                                    $style ='font-family:sans-serif;font-size: 16px;color:black;'; 
                                                    echo '<tr>
                                                                <td style="'.$style.'">'.$cup['total_quantity'].'</td>
                                                                <td style="'.$style.'">'.$cup['item_code'].'</td>
                                                                <td style="'.$style.'">'.$brand_name.'</td>
                                                                <td style="'.$style.'">'.$generic_name.'</td>
                                                                <td style="'.$style.'">'.$cup['uom'].'</td>
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
       $("#sales-table").dataTable(
       {                            
             "order":false,
             "columnDefs": [
                             { "width": "90px", "targets": 0 },
                             { "width": "200px", "targets": 1 },
                             { "width": "200px", "targets": 2 }
                           ]
       });
        <?php echo "swal.close();" ?>
</script>