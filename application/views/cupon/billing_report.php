<!-- modal here----------------------------------------------------------------------------------------- -->
  <div class="modal fade text-left" id="billing_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel17">Billed Transactions</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <div class="modal-body" id="billing-body">
                             
                            </div>
                            <div class="modal-footer">
                            <button type="button" onclick="close_billing_modal()" class="btn btn-danger" data-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block ">Close</span>
                            </button>
                         <!--    <button type="button" class="btn btn-primary ml-1" data-dismiss="modal">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Accept</span>
                            </button> -->
                            </div>
                        </div>
                        </div>
</div>


<!-- end of modal here----------------------------------------------------------------------------------------- -->

<div class="card col-12" style="margin-left:3px;">
   <div class="card-header">
       <!-- <h4 class="card-title">Vertical Form with Icons</h4> -->
   </div>
          <div class="card-content">
              <div class="card-body">
                   
                  <div class="form-body">                      
                       
                        <div class="row"> 
                        <div class="col-2">
                             <fieldset class="form-group">
                                        <select class="form-select"   id="status" onchange="validateDates()">
                                            <option value="unbilled">unbilled</option>
                                            <option value="billed-mpdi">billed</option>
                                            <option value="paid">paid</option>
                                            <option value="unsettled">unsettled</option>
                                        </select>
                             </fieldset>
                        </div>          
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
                                            <!-- <option value="all">all</option> -->
                                           <?php 
                                                    for($a=0;$a<count($store_arr);$a++)
                                                    { 
                                                        echo '<option value="'.$store_arr[$a]['db_id'].'">'.$store_arr[$a]['store'].'</option>';    
                                                    }
                                            ?>                                                                               
                                        </select>
                             </fieldset>
                         </div>                 
                          <div class="col-5" style="display: flex;">                             
                           <label for="date_from" style="margin-right: 10px;">From:</label>
                                <input type="date" id="date_from" name="date" style="font-family:sans-serif; margin-right: 10px;" onchange="validateDates()">                           
                                <label  for="date_to" style="margin-left:20px; margin-right: 10px;">To:</label>
                                <input  type="date" id="date_to" name="date" style="font-family:sans-serif; margin-right: 10px;" onchange="validateDates()">      
                                <a class="btn icon icon-left btn-success btn-sm" style="margin-left:20px; margin-right: 96px;" onclick="generate_billing_report()"><i data-feather="file"></i> Billed List</a>                             
                                <a href="#" id="process"  onclick="process_billing('billing-table')" class="btn btn-sm btn-warning">submit</a>                                
                                 
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
                                                var table         = $('#billing-table').DataTable(); 
                                                loader_();
                                                $.ajax({
                                                            type:'POST',
                                                           // url:'<?php echo base_url(); ?>Cuponing_ctrl/load_eod_ui_filter',
                                                            url:'<?php echo base_url(); ?>Cuponing_ctrl/billing_report',
                                                            data:{
                                                                    'dateFrom':$("#date_from").val(),
                                                                     'dateTo':$("#date_to").val(),
                                                                     'status':$("#status").val(),
                                                                     'store':$("#store").val()     
                                                                 },
                                                            dataType:'JSON',
                                                            success: function(data)
                                                            {


                                                                   swal.close(); 
                                                                   table.clear().draw(); //gi clear una para populatetan balik ang table
                                                                    
                                                                   console.log(data.billing_list);  
                                                                   for(var a=0;a<data.billing_list.length;a++)
                                                                   {

                                                                         
                                                                         var newRow = [
                                                                                         data.billing_list[a].checkbox,
                                                                                         data.billing_list[a].status,  
                                                                                         data.billing_list[a].date_transact,
                                                                                         data.billing_list[a].ordering_number,
                                                                                         data.billing_list[a].MUDC_disc                                                                                                                                                                                                                                                                              
                                                                                      ];                                 
                                                                           
                                                                          // Add the new row to the DataTable
                                                                         var rowNode = table.row.add(newRow).draw().node();      
                                                                       


                                                                        

                                                                         $(rowNode).find('td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4)').css(
                                                                         {
                                                                              'color': 'black',
                                                                              'font-family': 'sans-serif',
                                                                              'text-align': 'center'
                                                                         });


                                                                          $(rowNode).find('td:nth-child(5)').css(
                                                                         {
                                                                              'color': 'black',
                                                                              'font-family': 'sans-serif',
                                                                              'text-align': 'right'
                                                                         });
                                                                         
                                                                         // $(rowNode).find('td').not(':nth-child(), :nth-child(3)').css(
                                                                         // {
                                                                         //      'color': 'black',
                                                                         //      'font-family': 'sans-serif',
                                                                         //      'text-align': 'right'
                                                                         // });
                                                                   }

                                                            }                                                                 
                                                       });

                                                       if(['paid', 'unsettled', 'billed-mpdi','billed-acctg'].includes($("#status").val()))
                                                       {
                                                             hideFirstColumn();
                                                       }
                                                       else 
                                                       {
                                                             showFirstColumn();
                                                       }

                                            }
                                        } 

                                         
                         </script>

                      
                      <br>                 
                      <div class="row">                      
                             <div class="col-12 table-responsive">
                                    <table id="billing-table" class="table  table-bordered table-hover" style="font-family:sans-serif;font-size: 16px;">
                                         <thead>
                                           <tr style="background-color:#d2eaf7;text-align:center;">
                                             <th><input id="checkbox_main"  class="main_checkbox" type="checkbox" name="checkbox_main"></th>
                                             <th>Status</th>                                                  
                                             <th>TRANSACTION DATE</th>
                                             <th>ORDER NO.</th>
                                             <th>Discount (MUDC)</th>
                                           </tr>
                                         </thead>
                                     <tbody>
                                        <?php 

                                                $style ='font-family:sans-serif;font-size: 16px;color:black;'; 
                                                foreach($billing_list as $var)
                                                {
                                                    if(in_array($var['status'], ['settled', 'billed','unbilled']))     
                                                     {
                                                        $checkbox = '<input onclick ="check_checkboxes('."'billing-table'".')" class="checkbox" type="checkbox" name="checkbox-'.$var['status'].'" value="'.$var['cupon_id'].'">';
                                                     }
                                                     else 
                                                     {
                                                        $checkbox = '';
                                                     }
                                                     
                                                 
                                                    echo '<tr>
                                                                <td style="'.$style.'text-align:center;">'.$checkbox.'</td>
                                                                <td style="'.$style.'text-align:center;">'.$var['status'].'</td>                                                                
                                                                <td style="'.$style.'text-align:center;">'.date('m/d/Y', strtotime($var['date_transact'])).'</td>
                                                                <td style="'.$style.'text-align:center;">'.$var['ordering_number'].'</td>
                                                                <td style="'.$style.'text-align:right;">'.$var['MUDC_disc'].'</td>                                                                
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
       $("#billing-table").dataTable(
       {                            
             "order":false,
             "columnDefs": [
                             { "width": "90px", "targets": 0 },
                             { "width": "70px", "targets": 1 },
                             { "width": "70px", "targets": 2 },
                             { "width": "300px", "targets": 3 }
                           ]
       });



       $("#billing_modal").modal({
            backdrop: 'static',
            keyboard: false
        });



       <?php echo "swal.close();" ?>
       $("#uncheckall").hide();


       var checkbox = document.getElementById("checkbox_main");
        checkbox.addEventListener("click", function() 
        {
            if (checkbox.checked) 
            {
                 console.log("Checked: calling check_all('billing-table')");
                 check_all('billing-table');
            } 
            else
            {
                 console.log("Unchecked: calling uncheck_all('billing-table')");
                  uncheck_all('billing-table');
            }
        });
         

       // var checkbox = document.getElementById("checkbox_main");
       // checkbox.addEventListener("click", function()
       //  {
       //     // console.log("clicked");
       //       if (checkbox.checked) 
       //       {
       //          console.log("Checkbox is checked");
       //          check_all('billing-table');
       //       } 
       //       else
       //       {
       //           console.log("Checkbox is not checked");
       //           uncheck_all('billing-table')
       //       }
       //  });




        


</script>