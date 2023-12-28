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
                          <div class="col-10" style="display: flex;">                             
                           <label for="date_from" style="margin-right: 10px;">From:</label>
                                <input type="date" id="date_from" name="date" style="font-family:sans-serif; margin-right: 10px;" onchange="validateDates()">                           
                                <label  for="date_to" style="margin-left:20px; margin-right: 10px;">To:</label>
                                <input  type="date" id="date_to" name="date" style="font-family:sans-serif; margin-right: 10px;" onchange="validateDates()">   
                                <?php if( in_array($_SESSION['access_type'],array('mpdi','accounting','liquidation') ) )
                                      { 
                                ?>                         
                                         <a class="btn icon icon-left btn-success btn-sm" style="margin-left:20px; margin-right: 96px;" onclick="generate_variance_report()"><i data-feather="file"></i> Generate Report</a>
                                <?php } ?>        
                          <!--   <form action="" name="save_textfile" id="save_textfile" enctype="multipart/form-data" method="post">                                 
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
                                                var table         = $('#Variance-table').DataTable(); 
                                                loader_();
                                                $.ajax({
                                                            type:'POST',
                                                           // url:'<?php echo base_url(); ?>Cuponing_ctrl/load_eod_ui_filter',
                                                            url:'<?php echo base_url(); ?>Cuponing_ctrl/variance_report',
                                                            data:{
                                                                    'dateFrom':$("#date_from").val(),
                                                                     'dateTo':$("#date_to").val(),
                                                                     'store':$("#store").val()     
                                                                 },
                                                            dataType:'JSON',
                                                            success: function(data)
                                                            {


                                                                   swal.close(); 
                                                                   table.clear().draw(); //gi clear una para populatetan balik ang table
                                                                    
                                                                   //console.log(data.variance_list);  
                                                                   for(var a=0;a<data.variance_list.length;a++)
                                                                   {

                                                                         
                                                                        var newRow = [
                                                                                         data.variance_list[a].date_transact,
                                                                                         data.variance_list[a].ordering_number,
                                                                                         data.variance_list[a].MUDC_disc,
                                                                                         data.variance_list[a].nav_discount,                                                                                         
                                                                                         data.variance_list[a].variance                                                                                                       
                                                                                     ];                                 
                                                                           
                                                                          // Add the new row to the DataTable
                                                                         var rowNode = table.row.add(newRow).draw().node();                            
                                                                         $(rowNode).find('td:nth-child(4), td:nth-child(5)').css(
                                                                         {
                                                                              'color': 'black',
                                                                              'font-family': 'sans-serif',
                                                                              'text-align': 'right'
                                                                         });
                                                                         
                                                                         $(rowNode).find('td').not(':nth-child(4), :nth-child(5)').css(
                                                                         {
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
                                    <table id="Variance-table" class="table  table-bordered table-hover" style="font-family:sans-serif;font-size: 16px;">
                                         <thead>
                                           <tr style="background-color:#d2eaf7;text-align:center;">
                                             <th>TRANSACTION DATE</th>
                                             <th>ORDER NO.</th>
                                             <th>Discount (MUDC)</th>
                                             <th>Discount(Nav)</th>     
                                             <th>Variance</th>                                          
                                           </tr>
                                         </thead>
                                     <tbody>
                                        <?php 

                                                $style ='font-family:sans-serif;font-size: 16px;color:black;'; 
                                                foreach($variance_list as $var)
                                                {
                                                    echo '<tr>
                                                                <td style="'.$style.'">'.date('m/d/Y', strtotime($var['date_transact'])).'</td>
                                                                <td style="'.$style.'">'.$var['ordering_number'].'</td>
                                                                <td style="'.$style.'text-align:right;">'.$var['MUDC_disc'].'</td>
                                                                <td style="'.$style.'text-align:right;">'.$var['nav_discount'].'</td>
                                                                <td style="'.$style.'text-align:right;">'.$var['variance'].'</td>                                                                
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
       $("#Variance-table").dataTable(
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