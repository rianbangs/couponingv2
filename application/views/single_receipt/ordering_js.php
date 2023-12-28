<script>
    var itemTable;

    $(function() {
        itemTable = $("#item-table").DataTable({ "ordering": false }); 
    });

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
        $(document).on('click', 'body', function() { // Mouse Click on Body
            for(var c=0; c<dropdowns.length; c++){
                dropdowns[c].hide();
            }
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

    $(function() { 
        $('#order_no').on('input', function() {
            //$(this).val($(this).val().replace(/[^0-9]/g, '')); // Makes Text Field only accept Numbers
            $(this).val($(this).val().replace(/[^a-zA-Z0-9\s\-]/g, '')); // Makes Text Field only accept Letters, Hyphen, Numbers, Space
        });
    });

    $(function() { // Makes Text Field only accept Letters and Spaces
        $('#fname').on('input', function() {
            $(this).val($(this).val().replace(/[^a-zA-Z\s]/g, ''));
        });
    });

    $(function() { // Makes Text Field only accept Letters and Spaces
        $('#lname').on('input', function() {
            $(this).val($(this).val().replace(/[^a-zA-Z\s]/g, ''));
        });
    });

    $(function() { // Makes Text Field only accept Numbers
        $('#phone').on('input', function() {
            var inputVal = $(this).val().slice(0,1);
            var regex = /^[9]*$/; 
            if (!regex.test(inputVal)) {
                $(this).val(""); 
            } // Only Accepts 9 as the first digit

            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });
    });


    $(function() {
        $('#promo_code').on('input', function() {

            var inputVal = $(this).val();
            var regex = /^[a-zA-Z0-9]*$/; 
            $(this).val(inputVal.toUpperCase());
            if (!regex.test(inputVal)) {
                $(this).val(inputVal.replace(/[^a-zA-Z0-9]/g, '')); 
            }
            
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

    function loadingRow(){
        itemTable.clear().draw();
        var rowNode = itemTable.row.add(["","","LOADING DATA","","",""]).draw().node();
        $(rowNode).find('td').css({'color': 'red', 'font-family': 'sans-serif','text-align': 'center'}); 
    }

    function retrieveOrderList() {          
        var order_no = $('#order_no').val();
        loadingRow();
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

            $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'});  
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
        
        $("#transact_btn").prop('disabled', true);

        $.ajax({
              url:'<?php echo base_url();?>Dummy_ctrl/transactSingleReceipt',
              type:'POST',
              data:{ order_no: order_no, fname: firstname, lname: lastname, phone: phone, birth_year: birth_year, promo_code: promo_code },                                  
              success: function(response) {
                $("#transact_btn").prop('disabled', false);
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
                        window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFSingle/"+res.sr_id);

                        //$('#pdfModal_order').modal('show');
                        //$("#pdfFrame_order").attr("src","<?php echo base_url();?>Dummy_ctrl/generatePDFSingle/"+res.sr_id);                         
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
                        //$('#pdfModal_order').modal('show');
                        //$("#pdfFrame_order").attr("src","<?php echo base_url();?>Dummy_ctrl/generatePDFSingle/"+sr_id);
                        window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFSingle/"+sr_id);   
                    }
                });
    }

</script>    