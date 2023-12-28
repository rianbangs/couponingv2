<div class="card col-12">
    <!-- <div class="card-header">
        <h4 class="card-title">Monitoring List for Discounted Items</h4>
    </div> -->
    <div class="card-content">
        <div class="card-body">    
            <div class="form-body">
                    
                <div class="row">

                    <?php
                        if(in_array($_SESSION['access_type'],array('accounting'))){  
                    ?>

                    <div class="col-2">
                        <div class="form-group has-icon-left">
                            <label for="store_select" style="color: black; font-weight: bold;">STORE</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="columns"></i>
                                </div>
                                <select id="store_select" class="form-control" onchange="list_billing()">
                                    <!-- <option value="0">ALL</option> -->
                                    <option value="13">ICM</option>
                                    <option value="15">ASC-DUM</option>
                                    <option value="16">ASC</option>
                                    <option value="17">TAL</option>
                                    <option value="18">PM</option>
                                    <option value="19">AC</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php } ?>
                    
                    <div class="col-2">
                        <div class="form-group has-icon-left">
                            <label for="s_date" style="color: black; font-weight: bold;">START DATE</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                                <input id="s_date" type="date" class="form-control" onchange="list_billing()">
                                <div></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-2">  
                        <div class="form-group has-icon-left">
                            <label for="e_date" style="color: black; font-weight: bold;">END DATE</label>
                            <div class="position-relative">
                                <input id="e_date" type="date" class="form-control" onchange="list_billing()">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="form-group has-icon-left">
                            <label for="status_select" style="color: black; font-weight: bold;">STATUS</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="columns"></i>
                                </div>
                                <select id="status_select" class="form-control" onchange="list_billing()">
                                    <option value="unsettled">Unsettled</option>
                                    <option value="unbilled" selected>Unbilled</option>
                                    <option value="billed">Billed</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-2" style="padding-top: 20px;">
                        <button id="process_btn" class="btn btn-primary mr-1 mb-1" onclick="updateCheckBoxes()" style="display: none;">PROCESS</button>    
                    </div>

                    <div class="col-2" style="padding-top: 20px;">
                        <button id="printBill_btn"class="btn btn-primary mr-1 mb-1" onclick="viewBillingModal()" style="display: block;">BILLED LIST</button>
                    </div>
   
                </div>

                    
                    <div class="col-12">
                        <div class="table-responsive" style="padding-top: 20px;">
                            <table id="report-table" class="table table-striped">
                                <thead style="text-align: center; background-color: #d2eaf7; color: black;">
                                    <th>
                                        <input type="checkbox" id="checkAll" style="display: none;" onclick="checkAll()"> 
                                    </th>
                                    <th>STATUS</th>
                                    <th>TRANSACTION DATE</th>
                                    <th>ORDER NO.</th>
                                    <th>DISCOUNT (DCMS)</th>
                                    <!-- <th>DISCOUNT (NAV)</th> -->
                                    <!-- <th>TOTAL</th>  -->
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end" style="padding-top: 20px;">
                        
                    </div>
            </div>
        </div>
    </div>
 </div>

 <!-- Billing Modal -->
<div class="modal fade" id="billingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel17">Billing Transactions</h4>
                <button type="button" class="close" onclick="closeBillingModal()">
                    <i data-feather="x"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="col-12">
                    <div class="table-responsive" style="padding-top: 20px;">
                        <table id="billing-table" class="table table-striped">
                            <thead style="text-align: center; background-color: #d2eaf7; color: black;">
                                <th>BATCH NO.</th>
                                <th>FROM</th>
                                <th>TO</th>
                                <th>INVOICE NO.</th>
                                <th>ACTION</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeBillingModal()" class="btn btn-danger" data-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block ">Close</span>
                </button>       
            </div>
        
        </div>
    </div>
</div>

<script type="text/javascript">

    var reportTable;
    var billingTable;

    $(function() {
        reportTable = $('#report-table').DataTable({ "ordering": false });
        billingTable = $('#billing-table').DataTable({ "ordering": false });
        feather.replace();
    });

</script>
