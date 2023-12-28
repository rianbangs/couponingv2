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
    
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datatables.min.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/sweetalert.css">

</head>
<body>
<div class="card col-12">
    <div class="card-header">
        <h4 class="card-title">40php OFF! For every P250.00 minimum purchase in single receipt. Only applicable to specific items.</h4>
    </div>
    <div class="card-content">
        <div class="card-body">    
            <div class="form-body">
                    
                    <div class="col-3">
                        <div class="form-group has-icon-left">
                            <label for="order_no">ORDERING NUMBER</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="file-text"></i>
                                </div>
                                <input id="order_no" type="text" class="form-control" placeholder="Input Ordering Number" autocomplete="off">
                                <div></div>
                            </div>
                        </div>
                    </div>

                <div class="row">
                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="fname">FIRST NAME</label>
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
                            <label for="lname">LAST NAME</label>
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
                            <label for="phone">PHONE NO.</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="hash"></i>
                                </div>
                                <input id="phone" type="text" class="form-control" placeholder="Input Phone Number" autocomplete="off">
                                <div></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">  
                        <div class="form-group has-icon-left">
                            <label for="birth_year">BIRTH YEAR</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                                <select id="birth_year" class="form-control">
                                    <?php
                                        $current_year = date('Y'); 
                                        for($c=intval($current_year); $c>=1920; $c--){
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
                            <label for="promo_code">PROMO CODE</label>
                            <div class="position-relative">
                                <input id="promo_code" type="text" class="form-control" placeholder="Input Promo Code" autocomplete="off">
                                <div class="form-control-icon">
                                    <i data-feather="edit-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                     
                    <div class="col-12">
                        <div class="col-12 table-responsive" style="padding-top: 20px;">
                            <table id="item-table" class="table table-striped">
                                <thead style="text-align: center;">
                                    <th>ITEM NO.</th>
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
                        <button class="btn btn-primary mr-1 mb-1" onclick="transact()">Transact</button>
                    </div>
            </div>
        </div>
    </div>
 </div>

<!-- Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #323639;">
            <!-- <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div> -->
            <div class="modal-body">
                <center>
                    <iframe id="pdfFrame" height="600px" style="border-radius: 10px;"></iframe>
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
<?php $this->load->view("single_receipt/javascripts"); ?>

<script>
    const itemTable = $("#item-table").DataTable({ "ordering": false,
        "columnDefs": [{ "width": "90px", "targets": 0 },{ "width": "250px", "targets": 1 },{ "width": "110px", "targets": 4 }, /*{ "bVisible": false, "aTargets": [ 0 ] }  Hides Columns */] }); 

    
    const dropdowns = [ $('<ul>').addClass('my-dropdown dropdown-menu dd_ord'),
                        $('<ul>').addClass('my-dropdown dropdown-menu dd_fn'),
                        $('<ul>').addClass('my-dropdown dropdown-menu dd_ln'),
                        $('<ul>').addClass('my-dropdown dropdown-menu dd_ph')
                    ];
    
    function dropDownEvt(e,drp){ // event, drop index
        var keycode = e.keyCode || e.which;
        var dropdown = dropdowns[drp];

        if (keycode === 40) { // arrow down key
            e.preventDefault();
            var next = dropdown.find('.dropdown-item.active').next();
            if (next.length > 0) {
                dropdown.find('.dropdown-item.active').removeClass('active');
                next.addClass('active');
            } else {
                dropdown.find('.dropdown-item.active').removeClass('active');
                dropdown.find('.dropdown-item').first().addClass('active');
            }

        } else if (keycode === 38) { // arrow up key
            e.preventDefault();
            var prev = dropdown.find('.dropdown-item.active').prev();
            if (prev.length > 0) {
                dropdown.find('.dropdown-item.active').removeClass('active');
                prev.addClass('active');
            } else {
                dropdown.find('.dropdown-item.active').removeClass('active');
                dropdown.find('.dropdown-item').last().addClass('active');
            }
        }
    }

    $(function() {
        $('#order_no').on('keydown', function(e) {
           dropDownEvt(e,0)
        });
    });

    $(function() {
        $('#fname').on('keydown', function(e) {
            dropDownEvt(e,1);
        });
    });

    $(function() {
        $('#lname').on('keydown', function(e) {
            dropDownEvt(e,2);
        });
    });

    $(function() {
        $('#phone').on('keydown', function(e) {
            dropDownEvt(e,3);
        });
    });

    $(function() {
        $('#order_no').on('keyup', function(e) {
           var keycode = e.keyCode || e.which;
           var dropdown = dropdowns[0];

           if (keycode === 13){ // enter key
                var active = dropdown.find('.dropdown-item.active');
                if (active.length > 0) {
                    $(this).val(active.text()); // set input value to the active item 
                    dropdown.hide();
                    $('#fname').focus();
                }
                retrieveOrderList();
                
            } else if (keycode !== 40 && keycode !== 38) {
                  searchOrderNo();
            }
        });
    });

    $(function() {
        $(document).on('click', '.dd_ord li', function() { // Mouse Click on Dropdown Under Order Number Field
            $('#order_no').val($(this).text());
            $('.dd_ord').hide();
            retrieveOrderList();
            $('#fname').focus();
        });
    });

    $(function() {
        $('#fname').on('keyup', function(e) {
               var keycode = e.keyCode || e.which;
               var dropdown = dropdowns[1];

               if (keycode === 13){ // enter key
                    var active = dropdown.find('.dropdown-item.active');
                    if (active.length > 0) {
                        $(this).val(active.text()); // set input value to the active item 
                        dropdown.hide();
                    }
                    $("#lname").focus();
                } else if (keycode !== 40 && keycode !== 38) {
                      searchNameOrPhone(0,1);
                }
        });
    });

    $(function() {
        $(document).on('click', '.dd_fn li', function() { // Mouse Click on Dropdown Under First Name Field
            $('#fname').val($(this).text());
            $('.dd_fn').hide();
            $('#lname').focus();
        });
    });

    $(function() {
        $('#lname').on('keyup', function(e) {
               var keycode = e.keyCode || e.which;
               var dropdown = dropdowns[2];

               if (keycode === 13){ // enter key
                    var active = dropdown.find('.dropdown-item.active');
                    if (active.length > 0) {
                        $(this).val(active.text()); // set input value to the active item 
                        dropdown.hide();
                    }
                    $("#phone").focus();
                } else if (keycode !== 40 && keycode !== 38) {
                      searchNameOrPhone(1,2);
                }
        });
    });

    $(function() {
        $(document).on('click', '.dd_ln li', function() { // Mouse Click on Dropdown Under Last Name Field
            $('#lname').val($(this).text());
            $('.dd_ln').hide();
            $('#phone').focus();
        });
    });

    $(function() {
        $('#phone').on('keyup', function(e) {
               var keycode = e.keyCode || e.which;
               var dropdown = dropdowns[3];

               if (keycode === 13){ // enter key 
                    var active = dropdown.find('.dropdown-item.active');
                    if (active.length > 0) {
                        $(this).val(active.text()); // set input value to the active item 
                        dropdown.hide();
                    }
                    $("#birth_year").focus();
                } else if (keycode !== 40 && keycode !== 38) {
                      searchNameOrPhone(2,3);
                }
        });
    });

    $(function() {
        $(document).on('click', '.dd_ph li', function() { // Mouse Click on Dropdown Under Phone Number Field
            $('#phone').val($(this).text());
            $('.dd_ph').hide();
            $('#birth_year').focus();
        });
    });

    function searchOrderNo() {
        var dropdown = dropdowns[0];
        var order_no = $('#order_no').val();
        if (order_no != '') {
           $.ajax({
                    url:'<?php echo base_url();?>Dummy_ctrl/searchOrderNo',
                    type:'POST',
                    data:{ order_no: order_no },  
                    dataType:'json',                                 
                    success: function(response) {
                        console.log(response);  
                        dropdown.empty();
                        $.each(response, function(index, item) {                                 
                              var li = $('<li>').addClass('dropdown-item').text(item.order_no);
                              dropdown.append(li);
                        });
                        $('#order_no').next().replaceWith(dropdown);
                        dropdown.show();
                        dropdown.find('.dropdown-item').first().addClass('active');
                    }
            });

        } else {
            dropdown.hide();
        }
    }

    function searchNameOrPhone(ind,drp) { // column index, drop index
        var dropdown = dropdowns[drp];
        var search = $("#fname").val();
        
        if(ind==1)
            search = $("#lname").val();
        else if(ind==2)
            search = $("#phone").val();
        
        console.log(search);

        if (search != '') {
           $.ajax({
                    url:'<?php echo base_url();?>Dummy_ctrl/searchNameOrPhone',
                    type:'POST',
                    data:{ search: search, index: ind },  
                    dataType:'json',                                 
                    success: function(response) {
                        console.log(response);  
                        dropdown.empty();
                        $.each(response, function(index, item) {
                            var entry;
                            switch(ind){
                                case 0:
                                    entry = item.fname;
                                    break;
                                case 1:
                                    entry = item.lname;
                                    break;
                                default:
                                    entry = item.phone_no;
                                    break;
                            }

                            var li = $('<li>').addClass('dropdown-item').text(entry);
                            dropdown.append(li);
                        });

                        if(ind==0)
                            $('#fname').next().replaceWith(dropdown);
                        else if(ind==1)
                            $('#lname').next().replaceWith(dropdown);
                        else
                            $('#phone').next().replaceWith(dropdown);

                        if(response.length>0){
                            dropdown.show();
                            dropdown.find('.dropdown-item').first().addClass('active');
                        }else
                            dropdown.hide();
                    }
            });

        } else {
            dropdown.hide();
        }
    }

    function retrieveOrderList() {          
        var order_no = $('#order_no').val();

        $.ajax({
                  url:'<?php echo base_url();?>Dummy_ctrl/getSingleReceipt',
                  type:'POST',
                  data:{ order_no: order_no },                                  
                  success: function(response) {
                    itemTable.clear().draw();
                    try{
                        var res = JSON.parse(response);
                        console.log(res);
                        if(res.msg=="0"){
                            populateTable(res.data);
                        }else{
                            pdf_modal(res.msg,res.sr_id);
                        }
                    }catch(err){
                        swal_display("error","Invalid!",response);
                        console.log(response);
                    }
    
                  }
                });
    }

    function populateTable(entries){
        var list = entries.list;

        for(var c=0; c<list.length; c++){
            var item_code = list[c].item_code;
            var item_desc = list[c].item_desc;
            var price = list[c].price.toFixed(2);
            var qty = list[c].qty;
            var uom = list[c].uom;
            var t_price = list[c].t_price.toFixed(2);
            var rowNode = itemTable.row.add([item_code,item_desc,price,qty,uom,t_price]).draw().node();

            $(rowNode).find('td').css({'color': 'red', 'font-family': 'sans-serif','text-align': 'center'});  
        }
        
        //For Total
        var compute = entries.compute;
        var t_qty = compute.t_qty;
        var t_cost = compute.t_cost.toFixed(2);
        var finalNode = itemTable.row.add(['','','',t_qty,'',t_cost]).draw().node();
        $(finalNode).find('td').css({'color': 'black', 'font-family': 'sans-serif','text-align': 'center'});

        //For Discount
        var discount = compute.discount;
        var d_cost = compute.d_cost.toFixed(2);
        var discountNode = itemTable.row.add(['','','','<b>Discount (Php '+discount+')</b>','',d_cost]).draw().node();
        $(discountNode).find('td').css({'color': 'black', 'font-family': 'sans-serif','text-align': 'center'});
        
    }

    function transact(){
        var order_no = $("#order_no").val();
        var firstname = $("#fname").val();
        var lastname = $("#lname").val();
        var phone = $("#phone").val();
        var birth_year = $("#birth_year").val();
        var promo_code = $("#promo_code").val();

        $.ajax({
              url:'<?php echo base_url();?>Dummy_ctrl/transactSingleReceipt',
              type:'POST',
              data:{ order_no: order_no, fname: firstname, lname: lastname, phone: phone, birth_year: birth_year, promo_code: promo_code },                                  
              success: function(response) {
                try{
                    var res = JSON.parse(response);
                    console.log(res);
                    if(res.msg=="0"){
                        itemTable.clear().draw();
                        $("#order_no").val("");
                        $("#fname").val("");
                        $("#lname").val("");
                        $("#phone").val("");
                        $('#birth_year').prop('selectedIndex', 0);
                        $("#promo_code").val("");
                        $('#pdfModal').modal('show');
                        $("#pdfFrame").attr("src","<?php echo base_url();?>Dummy_ctrl/generatePDFSingle/"+res.sr_id); 
                        // var printWindow = window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFSingle/"+res.sr_id);

                        // wait for the page to load before printing
                        // printWindow.onload = function() {
                        //     printWindow.print();
                        // };
                    }else{
                       pdf_modal(res.msg,res.sr_id);
                    }
                    
                }catch(err){
                    swal_display("error","Invalid!",response);
                }     
              }    
            });
    }

    function swal_display(icon,title,text){
        Swal.fire({icon: icon, title:title, text: text });    
    }

    function pdf_modal(msg,sr_id){
        Swal.fire({
                    title: msg,
                    text: 'Do you want to view the receipt?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'YES'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#pdfModal').modal('show');
                        $("#pdfFrame").attr("src","<?php echo base_url();?>Dummy_ctrl/generatePDFSingle/"+sr_id);   
                    }
                });
    }


</script>
</html>
                 
 