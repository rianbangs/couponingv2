<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cuponing Dashboard</title>
    
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cuponing/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cuponing/vendors/chartjs/Chart.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cuponing/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cuponing/css/app.css">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/cuponing/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datatables.min.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/sweetalert.css">
</head>
<body>
 <div class="card col-9" style="margin-left:110px;">
    <div class="card-header">
        <h4 class="card-title">End of Month (EOM) Liquidation Report</h4>
    </div>
    <div class="card-content">
        <div class="card-body">    
            <div class="form-body">
                    
                <div class="row">
                    <div class="col-2">
                        <div class="form-group has-icon-left">
                            <label for="year_select">YEAR</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                                <select id="year_select"class="form-control">
                                    <?php 
                                        $year = date("Y");
                                        for($c=$year; $c>=2021; $c--){
                                            echo '<option value="'.$c.'">'.$c.'</option>';
                                        }
                                    ?>
                                </select>
                                <div></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="form-group has-icon-left">
                            <label for="month_select">MONTH</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                                <select id="month_select"class="form-control">
                                    <option value="01">JAN</option>
                                    <option value="02">FEB</option>
                                    <option value="03">MAR</option>
                                    <option value="04">APR</option>
                                    <option value="05">MAY</option>
                                    <option value="06">JUN</option>
                                    <option value="07">JUL</option>
                                    <option value="08">AUG</option>
                                    <option value="09">SEP</option>
                                    <option value="10">OCT</option>
                                    <option value="11">NOV</option>
                                    <option value="12">DEC</option>
                                </select>
                                <div></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-3" style="padding-top: 20px;">
                        <button class="btn btn-primary mr-1 mb-1" onclick="list()">GENERATE</button>
                    </div>

                    <div class="col-3">
                        <div class="form-group has-icon-left">
                            <label for="s_date">FILE</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="archive"></i>
                                </div>
                                <input id="text_file" type="file" class="form-control" multiple>
                                <div></div>
                            </div>
                        </div>
                    </div>


                    <div class="col-2" style="padding-top: 20px;">
                        <button class="btn btn-primary mr-1 mb-1" onclick="extract()">UPLOAD</button>
                    </div>

                </div>

                      
                    <div class="col-12">
                        <div class="col-12 table-responsive" style="padding-top: 20px;">
                            <table id="summary-table" class="table table-striped">
                                <thead style="text-align: center;">
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
                        <button class="btn btn-primary mr-1 mb-1" onclick="printPDF()">PRINT</button>
                    </div>
            </div>
        </div>
    </div>
 </div>

<!-- Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background-color: #323639;">
            <!-- <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div> -->
            <div class="modal-body">
                <center>
                    <iframe id="pdfFrame" height="700px" width="700px" style="border-radius: 10px;"></iframe>
                </center>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div> -->
        </div>
    </div>
</div>

</body>
<script src="<?php echo base_url(); ?>assets/cuponing/js/feather-icons/feather.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/cuponing/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script> -->
<script src="<?php echo base_url(); ?>assets/cuponing/js/app.js"></script>

<script src="<?php echo base_url(); ?>assets/cuponing/vendors/chartjs/Chart.min.js"></script>
<script src="<?php echo base_url(); ?>assets/cuponing/vendors/apexcharts/apexcharts.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/cuponing/js/pages/dashboard.js"></script> -->

<script src="<?php echo base_url(); ?>assets/cuponing/js/main.js"></script>
<script src="<?php echo base_url(); ?>assets/cuponing/js/jquery-3.6.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/sweetalert.js"></script>         
<script src="<?php echo base_url(); ?>assets/js/datatables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/sweetalert2.all.min.js"></script>

<script>

    const summaryTable = $("#summary-table").DataTable({ "ordering": false,
        /*"columnDefs": [{ "width": "90px", "targets": 0 },{ "width": "250px", "targets": 1 },{ "width": "110px", "targets": 4 }, { "bVisible": false, "aTargets": [ 0 ] }  Hides Columns ]*/ }); 


    function list() {
        var year = $("#year_select").val();
        var month = $("#month_select").val();
        console.log(year+" "+month);

       $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveSummaryEOM',
                type:'POST',
                data:{ year: year, month: month },                                  
                success: function(response) {
                    summaryTable.clear().draw();
                    try{
                        var res = JSON.parse(response);
                        populateTable(res);
                    }catch(err){
                        swal_display("error","Invalid!",response);  
                    }
                    
                }
        });

         
    }

   function populateTable(entries){
        var list = entries;
        console.log(list);
        for(var c=0; c<list.length; c++){
            var product = list[c].product_name;
            var transaction = list[c].transaction_date;
            var receipt = list[c].receipt;
            var qty = list[c].qty;
            var unit = list[c].unit;
            var price = list[c].price.toFixed(2);
            var total = list[c].total.toFixed(2);
            var branch = list[c].branch;
            var promo = list[c].promo_code;
            var rowNode = summaryTable.row.add([product,transaction,receipt,qty,unit,price,total,branch,promo]).draw().node();

            $(rowNode).find('td').css({'color': 'red', 'font-family': 'sans-serif','text-align': 'center'});  
        }
        
    }
   
    function printPDF(){
        $('#pdfModal').modal('show');
        var year = $("#year_select").val();
        var month = $("#month_select").val();
        $("#pdfFrame").attr("src","<?php echo base_url();?>Dummy_ctrl/generatePDFSummaryEOM/"+year+"/"+month);
    }

    function extract(){
        var formData = new FormData();
        var fileInputs = $('#text_file')[0].files;

        for (var i = 0; i < fileInputs.length; i++) {
            formData.append('files[]', fileInputs[i]);
        }

        loader_swal();
        
        $.ajax({
                url: '<?php echo base_url();?>Dummy_ctrl/extract_file',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    summaryTable.clear().draw();
                    var res = JSON.parse(response);
                    var html = "";
                    for(var c=0; c<res.response.length; c++){
                        html+= res.response[c]+"<br>";
                    }
                    swal_display_html("info","Message!",html);
                    $('#text_file').val("");
                }
            });
    }

    function swal_display(icon,title,text){
        Swal.fire({icon: icon, title:title, text: text });    
    }

    function swal_display_html(icon,title,html){
        Swal.fire({icon: icon, title:title, html: html });    
    }

    function loader_swal(){    
        Swal.fire({
                    imageUrl: '<?php echo base_url(); ?>assets/cuponing/images/Cube-1s-200px.svg',
                    imageHeight: 203,
                    imageAlt: 'loading',
                    text: 'loading, please wait',
                    allowOutsideClick:false,
                    showCancelButton: false,
                    showConfirmButton: false
                })              
    }

   


</script>
</html>
                 
 