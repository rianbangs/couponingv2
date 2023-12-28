
    <!--<div id="loader" class="row">
               
    </div>-->
    <div class="row" style="margin-top:47px;"> 
           <div class="col-sm-1">
               
           </div> 
           <div class="col-sm-10 data_here">
                data here
           </div>            
    </div>-
        <div class="modal fade" id="view_sales_inv_details" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document" style="width: 1162px;">
                <div class="modal-content">
                   <div class="modal-header" style="border-bottom: solid 1px; padding: 1px 15px;">
                         <h6><i class="glyphicon glyphicon-align-left"></i> SALES INVOICE DETAILS</h6>
                   </div>
                   <div class="modal-body" style="padding: 15px 15px;" id="remarks_body">  
                        <!-- <div class="row" style="margin-left: auto;margin-right: auto;">
                            <input type="hidden" class="amrt_id">
                            <h5 id='desired_amount'></h5>
                        </div>  
                        <div class="row" id="view_remarks_text" style="margin-left: auto;margin-right: auto;">
                            
                        </div>            -->            
                                                       
                   </div> 
                   <div class="modal-footer  modal_footer_view_sales_modal row" style="border-top: solid 1px; padding-top: 6px; padding-bottom: 6px;width: 1162px;margin-left:0px;"> 
                       
                   </div>
                </div>
            </div>
        </div> 

        <div class="modal fade" id="managers_key_details" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document" style="width: 372px;">
                <div class="modal-content">
                   <div class="modal-header" style="border-bottom: solid 1px; padding: 1px 15px;">
                         <h6><i class="glyphicon glyphicon-align-left"></i> MANAGERS KEY</h6>
                   </div>
                   <div class="modal-body" style="padding: 15px 15px;" id="">  
                         <div class="row">
                            <div class="col-sm-6">
                                    <input  class="username" placeholder="username">     
                            </div>
                            <div class="col-sm-6 ">
                                    <input  type="password" class="password" placeholder="password">                         
                            </div>                             
                         </div>
                   </div> 
                   <div class="modal-footer row" style="border-top: solid 1px; padding-top: 6px; padding-bottom: 6px;width: 372px;margin-left:0px;"> 
                          <button type="button" class="btn btn-info"  onclick ="proceed()" style="padding: 3px 10px;">Proceed</button>
                         <button type="button" class="btn btn-default" data-dismiss="modal" id="close" style="padding: 3px 10px;">Close</button>
                   </div>
                </div>
            </div>
        </div>

    <script type="text/javascript">

            load_table();
            function load_table()
            {
                var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
                $('.data_here').html(loader);
                $.ajax({
                            type:"POST",
                            url:'<?php echo base_url() ?>Mpdi_ctrl/home',
                            dataType:'json',
                            success: function(data)
                            {
                                 $('.data_here').html(data.html);
                                 console.log(data.html+"sfdsf");
                            } 
                       });
            }




            function view_details(Document_no)
            {
                console.log(Document_no);

                $.ajax({
                           type:"POST",
                           url:'<?php echo base_url() ?>Mpdi_ctrl/get_si_details',
                           data:{
                                   'Document_no':Document_no
                                },
                           dataType:'json',
                           success: function(data)
                           {
                            $("#view_sales_inv_details").modal("show");
                            $("#remarks_body").html(data.html);                            
                            $(".modal_footer_view_sales_modal").html(data.footer);
                            $(".get_pad_number").val(data.TIN_No);
                           } 
                       });
            }



            window.io = {
                open: function(verb, url, data, target){
                    var form = document.createElement("form");
                    form.action = url;
                    form.method = verb;
                    form.target = target || "_self";
                    if (data) {
                        for (var key in data) {
                            var input = document.createElement("textarea");
                            input.name = key;
                            input.value = typeof data[key] === "object"
                                ? JSON.stringify(data[key])
                                : data[key];
                            form.appendChild(input);
                        }

                    }
                    form.style.display = 'none';
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }
            };

     function gen_report()
     {

         $('.gen_report').click(function()
         {
             if($(".get_pad_number").val() == '')
             {
                  swal({
                             title: "",
                             text: "Please input TIN number",
                             type: "error"                                                    
                       });
             }
             else 
             {

                console.log($(".document_num").val()); 
                io.open('POST', '<?php echo base_url('Mpdi_ctrl/gen_report'); ?>', { document_num:$(".document_num").val(),get_pad_number:$(".get_pad_number").val() },'_blank');       
                $("#view_sales_inv_details").modal("hide");
             }
          });
     }  



     function gen_excel()
     {
        console.log( 'document_num:'+$(".document_num").val()+'get_pad_number:'+$(".get_pad_number").val()   );
        io.open('POST', '<?php echo base_url('Mpdi_ctrl/get_excel'); ?>', { document_num:$(".document_num").val(),get_pad_number:$(".get_pad_number").val() },'_blank');       
        $("#view_sales_inv_details").modal("hide");
     }



     function gen_reprint_report()
     {
        if($(".get_pad_number").val() == '')
        {
             swal({
                         title: "",
                         text: "Please input Sales Invoice number",
                         type: "error"                                                    
                  });
        }
        else 
        {
            $(".username").val('');
            $(".password").val('');
            $("#managers_key_details").modal("show");
        }
     }


     function proceed()
     {
        $.ajax({
                    type:'POST',
                     url:'<?php echo base_url('Mpdi_ctrl/validate_managers_key'); ?>',
                     data:{
                             'username': $(".username").val(),
                             'password': $(".password").val(),
                             'get_pad_number':$(".get_pad_number").val()   
                          },
                     dataType:'json',
                     success: function(data)
                     {
                        if(data.response == 'success')
                        {
                            io.open('POST', '<?php echo base_url('Mpdi_ctrl/gen_report'); ?>', { document_num:$(".document_num").val(),get_pad_number:$(".get_pad_number").val() },'_blank');       
                        }
                        else 
                        {                            
                             swal({
                                         title: "",
                                         text: data.response,
                                         type: "error"                                                    
                                  });
                        }
                     }      
               });
     }



    function search(search)
    {
        //console.log(search);
        //var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
        //$('#loader').html(loader);
        $.ajax({
                    type:"POST",
                    url:'<?php echo base_url("Mpdi_ctrl/search");  ?>',
                    data:{
                            'search':search
                         },
                    dataType:'JSON',
                    success: function(data)
                    {
                         var table = $('#sales_inv_tbl').DataTable();
                         table.clear().draw();
                        
                        for(var a=0;a<data.html.length;a++)
                        {                     
                            var button = '<button class="btn btn-danger btn-xs"  onclick="view_details('+"'"+data.html[a]+"'"+')">view</button>'  ;

                            var rowNode = table
                                              .row.add( [data.html[a] ,button ] )
                                              .draw()
                                              .node();                             
                                   $( rowNode )
                                              .css( 'color', 'red' )
                                              .animate( { color: 'black' } );
                        }   

                        $('#loader').html('');

 
                    }     

               });
    }       

    </script>
</html> 