<div class="card col-12">
    <!-- <div class="card-header">
        <h4 class="card-title">End of Month (EOM) Liquidation Report</h4>
    </div> -->
    <div class="card-content">
        <div class="card-body">    
            <div class="form-body">
                    
                <div class="row">
                    <div class="col-2">
                        <div class="form-group has-icon-left">
                            <label for="store_select" style="color: black; font-weight: bold;">STORE</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="columns"></i>
                                </div>
                                <select id="store_select" class="form-control" onchange="mpdi_list_eom()">
                                    <option value="0">ALL</option>
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

                    <div class="col-3">
                        <div class="form-group has-icon-left">
                            <label for="s_date" style="color: black; font-weight: bold;">START DATE</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                                <input id="s_date" type="date" class="form-control" onchange="mpdi_list_eom()">
                                <div></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="e_date" style="color: black; font-weight: bold;">END DATE</label>
                            <div class="position-relative">
                                <input id="e_date" type="date" class="form-control" onchange="mpdi_list_eom()">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-2" style="padding-top: 20px;">
                         <button class="btn btn-primary mr-1 mb-1" onclick="mpdi_printPDF_eom()">PRINT</button>
                        <!-- <button class="btn btn-primary mr-1 mb-1" onclick="list_eom()">GENERATE</button> -->
                    </div>

                </div>

                      
                    <div class="col-12">
                        <div class="table-responsive" style="padding-top: 20px;">
                            <table id="report-table" class="table table-striped">
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

                    <div class="col-12 d-flex justify-content-end" style="padding-top: 20px;">
                       
                    </div>
            </div>
        </div>
    </div>
 </div>

<script type="text/javascript">

    var reportTable;

    $(function() {
        reportTable = $('#report-table').DataTable({ "ordering": false });
        feather.replace();
    });

</script>