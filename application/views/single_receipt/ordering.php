<div class="card col-12">
    <div class="card-header">
        <h4 class="card-title">
            <marquee behavior="alternate" scrollamount="3"><b>40php OFF! For every P250.00 minimum purchase in single receipt. Only applicable to specific items.</b></marquee>
        </h4>
    </div>

    <div class="card-content">
        <div class="card-body"> 
            <div class="form-body">
                    
                    <div class="col-3">
                        <div class="form-group has-icon-left">
                            <label for="order_no" style="color: black; font-weight: bold;">ORDERING NUMBER</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="file-text"></i>
                                </div>
                                <input id="order_no" type="text" class="form-control" placeholder="Input Ordering Number" maxlength="20" autocomplete="off">
                                <div></div>
                            </div>
                        </div>
                    </div>

                <div class="row">
                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="fname" style="color: black; font-weight: bold;">FIRST NAME</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="user"></i>
                                </div>
                                <input id="fname" type="text" class="form-control" placeholder="Input First Name" autocomplete="off">
                                <div></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="lname" style="color: black; font-weight: bold;">LAST NAME</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="user"></i>
                                </div>
                                <input id="lname" type="text" class="form-control" placeholder="Input Last Name" autocomplete="off">
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="phone" style="color: black; font-weight: bold;">PHONE NO.</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="hash"></i>
                                </div>
                                <input id="phone" type="text" class="form-control" placeholder="Input Phone Number" maxlength="10">
                                <div></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="birth_year" style="color: black; font-weight: bold;">BIRTH YEAR</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                                <select id="birth_year" class="form-control">
                                    <?php
                                        $current_year = date('Y'); 
                                        for($c=intval($current_year)-7; $c>=1920; $c--){
                                            echo '<option value="'.$c.'">'.$c.'</option>';
                                        }
                                    ?>
                                </select>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>

                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="promo_code" style="color: black; font-weight: bold;">PROMO CODE</label>
                            <div class="position-relative">
                                <input id="promo_code" type="text" class="form-control" placeholder="Input Promo Code" maxlength="11">
                                <div class="form-control-icon">
                                    <i data-feather="edit-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                     
                    <div class="col-12">
                        <div class="table-responsive" style="padding-top: 20px;">
                            <table id="item-table" class="table table-striped">
                                <thead style="text-align: center; background-color: #d2eaf7; color: black;">
                                    <th>ITEM CODE</th>
                                    <th>ITEM NAME</th>
                                    <th>PRICE</th>
                                    <th>QUANTITY</th>
                                    <th>UOM</th> 
                                    <th>TOTAL</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end" style="padding-top: 20px;">
                        <button id="transact_btn" class="btn btn-primary mr-1 mb-1" onclick="transact()">Transact</button>
                    </div>
            </div>
        </div>
    </div>
 </div>

<!-- PDF Modal -->
<div class="modal fade" id="pdfModal_order" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background-color: #323639;">
           
            <div class="modal-body">
                <center>
                    <iframe id="pdfFrame_order" width="600px" height="600px" style="border-radius: 10px;"></iframe>
                </center>
            </div>
        
        </div>
    </div>
</div>


    