<script>
	// const reportTable = $("#report-table").DataTable({ "ordering": false }); 

    // const reportTable = $("#report-table").DataTable({ "ordering": false,
    //     "columnDefs": [{ "width": "90px", "targets": 0 },{ "width": "250px", "targets": 1 },{ "width": "110px", "targets": 4 }, { "bVisible": false, "aTargets": [ 0 ] }  Hides Columns ] });	

    const primary_color = '#0275d8';
    
	// Discount Monitoring
    function list_discount() {
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }

        $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveListOfDiscountedItems',
                type:'POST',
                data: { store_select: store_sel, start_date: start_date, end_date: end_date },                         
                success: function(response) {
                    reportTable.clear().draw();
                    try{
                        var res = JSON.parse(response);
                        populateTableDiscount(res);
                    }catch(err){
                        swal_display("error","Invalid!",response);  
                    }
                 
                }
        });
    
    }

   function populateTableDiscount(entries){
        var list = entries;
        console.log(list);
        for(var c=0; c<list.length; c++){
            var qty = list[c].qty;
            var item_code = list[c].item_code;
            var brand = list[c].brand;
            var generic = list[c].generic;
            var uom = list[c].uom;
            var rowNode = reportTable.row.add([qty,item_code,brand,generic,uom]).draw().node();

            $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'});  
        }
        
    }
   
    function printPDF_discount(){
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }
        window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFSingleDiscount/"+start_date+"/"+end_date+"/"+store_sel);
    }


    // EOD - EOM
    function list_eod() {
        var selected_date = $("#s_date").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }

       $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveSummaryEOD',
                type:'POST',
                data:{ store_select: store_sel, selected_date: selected_date },                                  
                success: function(response) {
                    reportTable.clear().draw();
                    try{
                        var res = JSON.parse(response);
                        console.log(res);
                        populateTableEO(res);
                    }catch(err){
                        swal_display("error","Invalid!",response);  
                    }
                    
                }
        });

         
    }

    function list_eom() {
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }

       $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveSummaryEOM',
                type:'POST',
                data:{ store_select: store_sel, start_date: start_date, end_date: end_date },                              
                success: function(response) {
                    reportTable.clear().draw();
                    $('#ct_span').text("Number of Transactions: 0");
                    
                    try{
                        var res = JSON.parse(response);
                        if(res.length>1){
                            $('#ct_span').text("Number of Transactions: "+res[0]);
                            populateTableEO(res[1]);
                        }
                        
                    }catch(err){
                        swal_display("error","Invalid!",response);  
                    }
                    
                }
        });  
    }

   
   function populateTableEO(entries){ // Used both by EOD and EOM and MPDI
        var list = entries;
        console.log(list);
        for(var c=0; c<list.length; c++){
            var product = list[c].product_name;
            var transaction = list[c].transaction_date;
            var receipt = list[c].receipt;
            var qty = list[c].qty;
            var unit = list[c].unit;
            var price = "";
            if(list[c].price!="")
                price = parseFloat(list[c].price).toFixed(2);

            var total = list[c].total.toFixed(2);
            var branch = list[c].branch;
            if(!isNaN(branch))
                branch = parseFloat(list[c].branch).toFixed(2);

            var promo = list[c].promo_code;

            var color = "blue";
            var font_weight = "normal";
            if(product=="Discount"){
                color = "black";
                font_weight = "bold";
                promo = "";
            }

            var rowNode = reportTable.row.add([product,transaction,receipt,qty,unit,price,total,branch,promo]).draw().node();

            $(rowNode).find('td').css({'color': color, 'font-family': 'sans-serif','text-align': 'center',
                                        'font-weight': font_weight});  
        }
        
    }

    // function populateTableEO(entries){ // Used both by EOD and EOM
    //     var list = entries;
    //     console.log(list);
    //     for(var c=0; c<list.length; c++){
    //         var transaction = list[c].transaction_date;
    //         var receipt = list[c].receipt;
    //         var total = list[c].total.toFixed(2);
    //         var discount = list[c].discount;
    //         var branch = list[c].branch;
    //         var promo = list[c].promo_code;
    //         var rowNode = reportTable.row.add([transaction,receipt,total,discount,branch,promo]).draw().node();

    //         $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'});  
    //     }
        
    // }
   
    function textfile(){
        var selected_date = $("#s_date").val();
        if(selected_date=="")
            selected_date = 0;

        window.open("<?php echo base_url();?>Dummy_ctrl/generateTextFile/"+selected_date);

    }

    function printPDF_eod(){
        var selected_date = $("#s_date").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }

        window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFSummaryEOD/"+selected_date+"/"+store_sel);
    }

    function printPDF_eom(){
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }
        window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFSummaryEOM/"+start_date+"/"+end_date+"/"+store_sel);
    }

    function extract(){ // Used both by EOD and EOM
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
                    reportTable.clear().draw();
                    var res = JSON.parse(response);
                    var html = res.response;
                    var indi = "error";
                    
                    if(html=="Success")
                        indi = "success";

                    swal_display_html(indi,"Message!",html);
                    console.log(html);
                    $('#text_file').val("");
                }
            });
    }


    // Variance
    function list_variance() {
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }

        $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveVarianceReports',
                type:'POST',
                data:{ store_select: store_sel, start_date: start_date, end_date: end_date },                        
                success: function(response) {
                    reportTable.clear().draw();
                    try{
                        var res = JSON.parse(response);
                        populateTableVariance(res);
                    }catch(err){
                        swal_display("error","Invalid!",response);  
                    }
                    
                }
        });
    }

   function populateTableVariance(entries){
        var list = entries;
        console.log(list);
        for(var c=0; c<list.length; c++){
            var transaction_date = list[c].transaction_date;
            var order_no = list[c].order_no;
            var discount_dcms = list[c].discount_dcms;
            var discount_nav = list[c].discount_nav;
            var variance = list[c].variance;
            var rowNode = reportTable.row.add([transaction_date,order_no,discount_dcms,discount_nav,variance]).draw().node();

            $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'});  
        }
        
    }
   
    function printPDF_variance(){
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }

        window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFVariance/"+start_date+"/"+end_date+"/"+store_sel);
    }


    // Billing
    function list_billing(){
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        var status = $("#status_select").val();
        var store_sel = 0;

        if($('#store_select').length){
            store_sel = $('#store_select').val();
        }

       $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveBilling',
                type:'POST',
                data:{ store_select: store_sel, start_date: start_date, end_date: end_date, status_select: status },
                success: function(response) {
                    reportTable.clear().draw();
                    try{
                        var res = JSON.parse(response);
                        populateTableBilling(res);
                        
                    }catch(err){
                        swal_display("error","Invalid!",response);
                        $("#process_btn").hide();  
                    }
                    
                }
        });   
    }

    function populateTableBilling(entries){
        var list = entries;
        console.log(list);
        for(var c=0; c<list.length; c++){
            var sr_id = list[c].sr_id;
            var status = list[c].status;
            var transaction_date = list[c].transaction_date;
            var order_no = list[c].order_no;
            var discount_mudc = list[c].discount_mudc;
            // var total = list[c].total.toFixed(2);

            var html = "";

            if(status=="unbilled"){
                html = '<input type="checkbox" class="status_box" value="'+sr_id+'" onclick="uncheckMain()">';
                $("#process_btn").show();
                $("#checkAll").show();
            }else{
                $("#process_btn").hide();
                $("#checkAll").hide();
            }
                    
            var rowNode = reportTable.row.add([html,status.toUpperCase(),transaction_date,order_no,discount_mudc]).draw().node();

            $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'});  
        }   
    }

    function updateCheckBoxes(){
        var status = $("#status_select").val();
        if(status=="unbilled"){

            var checkedElements = [];
            var start_date = $("#s_date").val();
            var end_date = $("#e_date").val();

            reportTable.column(0).nodes().to$().find('.status_box:checked').map(function() {
                var sr_id = $(this).val(); // Get sr_id from status_box
                checkedElements.push(sr_id); // push the value of each selected checkbox to the array

            });

            console.log(checkedElements);

            if(checkedElements.length<1){
                swal_display("error","Invalid!","No Selected CheckBoxes!"); 
            }else{
                
                Swal.fire({
                    title: "Confirmation",
                    text: 'Are you sure to process the billing?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'YES'
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                        $.ajax({
                                url:'<?php echo base_url();?>Dummy_ctrl/processBilling',
                                type:'POST',
                                data:{  start_date: start_date, 
                                        end_date: end_date, 
                                        checkedElements: checkedElements },                                  
                                success: function(response) {
                                    try{
                                        reportTable.clear().draw();
                                        var res = JSON.parse(response);
                                        var html = "";

                                        for(var c=0; c<res[1].length; c++){
                                            html+= res[1][c]+"<br>";
                                        }

                                        swal_display_html("info",res[0],html);

                                        $("#s_date").val("");
                                        $("#e_date").val("");
                                        $("#process_btn").hide();
                                        $("#checkAll").hide();
                                    }catch(err){
                                        swal_display("error","Invalid!",response);
                                    }
                                }
                        }); 

                    } // isConfirmed Close
                }); // Swal Then

            } // Else Close

        }
        

    }

    function checkAll(){
        var checked = $('#checkAll').prop('checked');
        reportTable.column(0).nodes().to$().find('.status_box').prop('checked', checked);
    }

    function uncheckMain(){
        $('#checkAll').prop('checked',false);
    }

    function viewBillingModal(){
        $("#billingModal").modal("show");
        list_batch();
    }

    function list_batch(){
        $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveBillingBatch',
                type:'POST',                                  
                success: function(response) {
                    billingTable.clear().draw();
                    var res = JSON.parse(response);
                    populateTableBatch(res);        
                }
        });
    }

    function closeBillingModal(){
        $("#billingModal").modal("hide");
    }

    function populateTableBatch(entries){
        var list = entries;
        console.log(list);
        for(var c=0; c<list.length; c++){
            var batch_id = list[c].batch_id;
            var batch_no = list[c].batch_no;
            var from = list[c].from_date;
            var to = list[c].to_date;
            var invoice = list[c].paid_invoice_number;
            var date_extract = list[c].date_extract;
            
            var action_inv = invoice;
            var action_html = "";

            if((invoice==null || invoice=="") && (date_extract!=null && date_extract!="")){
                action_inv = '<input type="text" id="batch_'+batch_id+'" placeholder="Invoice No." maxlength="15">';
                action_html+= '<button class="btn btn-danger" onclick="saveInvoice('+batch_id+')">POST</button> '
            }
            
            action_html+= '<button class="btn btn-primary" onclick="printPDF_billing('+batch_id+')">PRINT</button>';

            var rowNode = billingTable.row.add([batch_no,from,to,action_inv,action_html]).draw().node();

            $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'}); 
        } 
    } 

    function saveInvoice(batch_id){
        var invoice = $("#batch_"+batch_id).val();
        console.log(invoice+" "+batch_id);
        $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/saveBatchInvoice',
                data:{ invoice: invoice, batch_id: batch_id },
                type:'POST',                                  
                success: function(response) {
                    try{
                        var res = JSON.parse(response);
                        swal_display("success","Message!",res[0]);
                        list_batch();
                    }catch(err){
                        swal_display("error","Invalid!",response);
                    }
                    
                }
        });

    }

    function printPDF_billing(batch_id){
        window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFBilling/"+batch_id);
    }


    //MPDI - EOM
    function mpdi_list_eom() {
        var store = $("#store_select").val();
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        console.log(start_date+" "+end_date);

       $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveSummaryEOM_mpdi',
                type:'POST',
                data:{ store: store, start_date: start_date, end_date: end_date },                                  
                success: function(response) {
                    reportTable.clear().draw();
                    try{
                        var res = JSON.parse(response);
                        populateTableEO(res);
                    }catch(err){
                        swal_display("error","Invalid!",response);  
                    }
                    
                }
        });  
    } 

    function mpdi_printPDF_eom(){
        var store = $("#store_select").val();
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        window.open("<?php echo base_url();?>Dummy_ctrl/generatePDFSummaryEOM_mpdi/"+start_date+"/"+end_date+"/"+store);
    }

    //MPDI - Billing
    function mpdi_list_billing(){
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();
        console.log(start_date+" "+end_date);

       $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveBilling_mpdi',
                type:'POST',
                data:{ start_date: start_date, end_date: end_date },                                  
                success: function(response) {
                    reportTable.clear().draw();
                    try{
                        var res = JSON.parse(response);
                        populateTableBilling_Mpdi(res);
                    }catch(err){
                        swal_display("error","Invalid!",response);  
                    }
                    
                }
        }); 
    }

    function populateTableBilling_Mpdi(entries){
        // $("#checkAll").hide();
        var list = entries; 
        console.log(list);
        for(var c=0; c<list.length; c++){
            var batch_id = list[c].batch_id;
            var batch_no = list[c].batch_no;
            var from = list[c].from_date;
            var to = list[c].to_date;
            var invoice = list[c].paid_invoice_number;
            var status = list[c].status.toUpperCase();

            var check_box = '';
            // if(status=="FOR BILLING"){
                check_box = '<input type="checkbox" class="status_box" value="'+batch_id+'" onclick="uncheckMain()">';
            //     $("#checkAll").show();
            // }
            
            var view_btn = '<button class="btn btn-primary" onclick="viewItemsModal('+batch_id+')">VIEW</button>';

            var rowNode = reportTable.row.add([check_box,batch_no,from,to,invoice,status,view_btn]).draw().node();

            $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'}); 
        } 
    }

    function viewItemsModal(batch_id){
        $("#billingModal").modal("show");
        list_items(batch_id);
    }

    function list_items(batch_id){
        $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/retrieveBillingBatch_mpdi',
                type:'POST',
                data:{ batch_id: batch_id },                                   
                success: function(response) {
                    billingTable.clear().draw();
                    var res = JSON.parse(response);
                    populateTableBillingSR(res);        
                }
        });
    }

    function populateTableBillingSR(entries){
        var list = entries;
        console.log(list);
        for(var c=0; c<list.length; c++){
            var product = list[c].product_name;
            var transaction = list[c].transaction_date;
            var receipt = list[c].receipt;
            var qty = list[c].qty;
            var unit = list[c].unit;
            var price = "";
            if(list[c].price!="")
                price = parseFloat(list[c].price).toFixed(2);

            var total = list[c].total.toFixed(2);
            var branch = list[c].branch;
            if(!isNaN(branch))
                branch = parseFloat(list[c].branch).toFixed(2);

            var promo = list[c].promo_code;

            var color = "blue";
            var font_weight = "normal";
            if(product=="Discount"){
                color = "black";
                font_weight = "bold";
                promo = "";
            }

            var rowNode = billingTable.row.add([product,transaction,receipt,qty,unit,price,total,branch,promo]).draw().node();

            $(rowNode).find('td').css({'color': color, 'font-family': 'sans-serif','text-align': 'center',
                                        'font-weight': font_weight});  
        }
    }

    function extract_excel(){
        var checkedElements = [];
        var start_date = $("#s_date").val();
        var end_date = $("#e_date").val();

        reportTable.column(0).nodes().to$().find('.status_box:checked').map(function() {
            var batch_id = $(this).val(); // Get batch_id from status_box
            checkedElements.push(batch_id); // push the value of each selected checkbox to the array
        });

        console.log(checkedElements);

        if(checkedElements.length<1){
            swal_display("error","Invalid!","No Selected CheckBoxes!"); 
        }else{
        
            $.ajax({
                    url:'<?php echo base_url();?>Dummy_ctrl/generateExcelFile',
                    type:'POST',
                    data:{  checkedElements: checkedElements, start_date: start_date, end_date: end_date },          
                    success: function(response) {
                        
                        var blob = new Blob([response], { type: 'application/vnd.ms-excel' }); // Create a blob from the response data
                        var url = URL.createObjectURL(blob); // Create a URL object from the blob
                        var link = document.createElement('a'); // Create a temporary link element
                        
                        link.href = url; // Set the link's href to the URL object
                        link.download = 'MPDI BILLING_'+start_date+' to '+end_date+'.xls'; // Set the link's download attribute
                        
                        document.body.appendChild(link); // Append the link to the document body
                        link.click(); // Click the link to trigger the download
                        document.body.removeChild(link); // Remove the link from the document body
                    }
            }); 

        } // Else Close
    }

</script>