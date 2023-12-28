<div class="card col-12">
    <!-- <div class="card-header">
        <h4 class="card-title">Monitoring List for Discounted Items</h4>
    </div> -->
    <div class="card-content">
        <div class="card-body">    
            <div class="form-body">
                    
                <div class="row">
                    <div class="col-3">
                        <div class="form-group has-icon-left">
                            <label for="s_date" style="color: black; font-weight: bold;">START DATE</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                                <input id="s_date" type="date" class="form-control" onchange="mpdi_list_billing()">
                                <div></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="e_date" style="color: black; font-weight: bold;">END DATE</label>
                            <div class="position-relative">
                                <input id="e_date" type="date" class="form-control" onchange="mpdi_list_billing()">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-2" style="padding-top: 20px;">
                        <button id="process_btn" class="btn btn-primary mr-1 mb-1" onclick="extract_excel()">EXTRACT</button>    
                    </div>

                </div>

                    
                    <div class="col-12">
                        <div class="table-responsive" style="padding-top: 20px;">
                            <table id="report-table" class="table table-striped">
                                <thead style="text-align: center; background-color: #d2eaf7; color: black;">
                                    <th>
                                        <input type="checkbox" id="checkAll" onclick="checkAll()"> 
                                    </th>
                                    <th>BATCH NO.</th>
                                    <th>FROM</th>
                                    <th>TO</th>
                                    <th>INVOICE NO.</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
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
                <h4 class="modal-title" id="myModalLabel17">Transactions</h4>
                <button type="button" class="close" onclick="closeBillingModal()">
                    <i data-feather="x"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="col-12">
                    <div class="table-responsive" style="padding-top: 20px;">
                        <table id="billing-table" class="table table-striped">
                            <thead style="text-align: center; background-color: #d2eaf7; color: black;">
                                <th>PRODUCT NAME</th>
                                <th>TRANSACTION DATE</th>
                                <th>RECEIPT</th>
                                <th>QTY</th>
                                <th>UNIT</th>
                                <th>PRICE</th>
                                <th>TOTAL</th>
                                <th>BRANCH</th>
                                <th>PROMO</th>
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
