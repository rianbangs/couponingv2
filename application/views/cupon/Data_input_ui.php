 
                 <div class="card col-12"  >
                     <div class="card-header" >
                         <!-- <h4 class="card-title">Vertical Form with Icons</h4> -->
                            </div>
                            <div class="card-content">
                            <div class="card-body">
                                 
                                <div class="form-body" >
                                    <div class="row">
                                      <div class="col-12">
                                         <div class="col-5">  
                                             <div class="form-group has-icon-left">
                                                 <label for="password-id-icon" style=" color: black;"><strong>Ordering Number</strong></label>
                                                 <div class="position-relative">
                                                    <div class="form-control-icon">
                                                        <i data-feather="file-text"></i>
                                                    </div>
                                                     <input id="order_number" style="font-family:sans-serif;" type="text" class="form-control" placeholder="Input Ordering Number" autocomplete="off">
                                                     <div></div>
                                                 </div>
                                             </div>
                                         </div> 
                                    </div>     
                                    <div class="col-12">
                                        <div class="row">
                                          <!--   <div class="form-group col-5 has-icon-left">
                                                <label for="first-name-icon">Full Name</label>
                                                <div class="position-relative">
                                                    <input id="fname" type="text" class="form-control" placeholder="Input Full Name" autocomplete="off">
                                                    <div class="form-control-icon">
                                                        <i data-feather="user"></i>
                                                    </div>
                                                </div>
                                            </div>  -->
                                            <div class="form-group col-5 has-icon-left">
                                                <label for="first-name-icon" style=" color: black;"><strong>First Name</strong></label>
                                                <div class="position-relative">
                                                    <div class="form-control-icon">
                                                        <i data-feather="user"></i>
                                                    </div>
                                                    <input id="firstname" type="text" class="form-control" placeholder="Input First Name" autocomplete="off">
                                                    <div></div>
                                                </div>
                                            </div>
                                            <div class="form-group col-5 has-icon-left">
                                                <label for="first-name-icon" style=" color: black;"><strong>Last Name</strong></label>
                                                <div class="position-relative">
                                                    <div class="form-control-icon">
                                                        <i data-feather="user"></i>
                                                    </div>
                                                    <input id="lname" type="text" class="form-control" placeholder="Input Last Name" autocomplete="off">
                                                    <div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                             <div class="form-group col-5 has-icon-left">
                                                <label for="first-name-icon" style=" color: black;"><strong>Phone No</strong></label>
                                                <div class="position-relative">
                                                    <div class="form-control-icon">
                                                        <i data-feather="hash"></i>
                                                    </div>
                                                    <input id="phone_no" type="text" class="form-control" placeholder="Input Phone Number" autocomplete="off" style="font-family:sans-serif;">
                                                    <div></div>
                                                </div>
                                            </div>
                                            <div class="form-group col-5 has-icon-left">
                                                <label for="first-name-icon" style=" color: black;"><strong>Birth Year</strong></label>
                                                <div class="position-relative">
                                                    <!-- <input id="birth_year" type="text" class="form-control" placeholder="Input Last Name" autocomplete="off"> -->
                                                
                                                     <!-- <div class="form-control-icon">
                                                        <i data-feather="plus-square"></i>
                                                    </div> -->
                                                    <select name="year" id="year" class="choices form-select" style="font-family:sans-serif;">
                                                        <?php 
                                                                //$nearest_cutoff =  date('Y-m-d', strtotime('+1 month', strtotime(date('Y-m-'.$first_cutoff))));                   
                                                               $current_year = date('Y');
                                                               for($a=0;$a<120;$a++)
                                                               {
                                                                 echo '<option value="'.$current_year.'" style="font-family:sans-serif;">'.$current_year.'</option>';
                                                                 $current_year -= 1;
                                                               }
                                                         ?>
                                                    </select>
                                                                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>                            
                                    <div class="col-12">
                                         <div class="col-5">    
                                             <div class="form-group has-icon-left">
                                                 <label for="password-id-icon" style=" color: black;"><strong>Transaction Code</strong></label>
                                                 <div class="position-relative">
                                                     <input type="number" id="promo_code" style="font-family:sans-serif;" type="text" class="form-control" placeholder="Enter Transaction Code" autocomplete="off">
                                                     <div class="form-control-icon">
                                                        <i data-feather="edit-2"></i>
                                                    </div>
                                                 </div>
                                             </div>
                                         </div>  
                                    </div>
                                   <!--  <div class="col-12">
                                        <div class="row">
                                              <div class="col-md-6">
                                                <div class="form-group has-icon-left">
                                                  <label for="item_code">Item</label>
                                                  <div class="position-relative">
                                                    <input id="item_code" style="font-family:sans-serif;" type="text" class="form-control" placeholder="Search Item Code/Item Name" autocomplete="off">
                                                    <div class="form-control-icon">
                                                      <i data-feather="lock"></i>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                 <div class="col-md-4">
                                                    <div class="form-group has-icon-left">
                                                      <label for="quantity">Quantity</label>
                                                      <div class="position-relative">
                                                        <input id="quantity" type="text" class="form-control" placeholder="Input Quantity">
                                                        <div class="form-control-icon">
                                                          <i data-feather="lock"></i>
                                                        </div>
                                                      </div>
                                                    </div>
                                                </div>    
                                              </div>
                                         </div>
                                    </div> -->                        
                                    <style>
                                            .table-header {
                                                              text-align: center;
                                                            }
                                    </style>          
                                    <div class="col-12">
                                        <div class="col-12 table-responsive">
                                                      <table id="item-table" class="table table-striped" style="font-family:sans-serif;">
                                                        <thead style="text-align: center; background-color: #d2eaf7; color: black;">
                                                          <tr>
                                                            <th class="table-header">Item Code</th>
                                                            <th class="table-header">Item Name</th>
                                                            <th class="table-header">UOM</th>
                                                            <th class="table-header">Quantity</th>
                                                            <th class="table-header">Price</th>
                                                            <th class="table-header">Discount</th>
                                                            <th class="table-header">Discounted Amount</th> 
                                                            <th class="table-header">VAT / NON-VAT</th>    
                                                            <th></th>
                                                     
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          <!-- <tr>
                                                            <td>ITM001</td>
                                                            <td>Item 1</td>
                                                            <td>$10.00</td>
                                                            <td>ITM001</td>
                                                            <td>Item 1</td>
                                                            <td>$10.00</td>
                                                            <td>$10.00</td>
                                                          </tr>
                                                          <tr>
                                                            <td>ITM002</td>
                                                            <td>Item 2</td>
                                                            <td>$15.00</td>
                                                            <td>ITM001</td>
                                                            <td>Item 1</td>
                                                            <td>$10.00</td>
                                                            <td>$10.00</td>
                                                          </tr>
                                                          <tr>
                                                            <td>ITM003</td>
                                                            <td>Item 3</td>
                                                            <td>$20.00</td>
                                                            <td>ITM001</td>
                                                            <td>Item 1</td>
                                                            <td>$10.00</td>
                                                            <td>$10.00</td>
                                                          </tr> -->
                                                        </tbody>
                                                      </table>
                                                    </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button  class="btn btn-primary mr-1 mb-1" onclick="submit_order()">Proceed</button>
                                        <!-- <button type="reset" class="btn btn-light-secondary mr-1 mb-1">Reset</button> -->
                                    </div>
                                    </div>
                                </div>
                                
                            </div>
                            </div>
                 </div>
 