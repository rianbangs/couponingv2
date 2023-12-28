 

<!-- modal here----------------------------------------------------------------------------------------- -->
   <div class="modal fade text-left" id="promo_modal" tabindex="-1" role="dialog" aria-labelledby="modal"   aria-hidden="true" >
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg custom-width-modal" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="promo_title" style="font-family: Arial, sans-serif;"></h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <div class="modal-body" id="billing-body" style="overflow-y: scroll;height: 666px;">
                             
                            </div>
                            <div class="modal-footer">
                           
                            </div>
                        </div>
                        </div>
</div>

<!-- end of modal here----------------------------------------------------------------------------------------- -->



<div class="card col-12"  >
   <div class="card-header" >                        
   </div>                     
   <div class="card-content">   
     <div class="row">
        <div class="col-sm-2">
            <select id="brand_select" class="choices form-select" onchange="load_promo_list()" style="margin-bottom:38px;margin-left: 18px;">
           <?php foreach($brand_list as $list)
                 { 
                      echo '<option value="'.$list['brand_id'].'">'.$list['brand_name'].'</option>';
                 } ?>           
            </select>              
        </div> 
        <div class="col-sm-2">
            <button class="btn btn-success mr-1 mb-1" onclick="view_add_promo_modal()">add promo</button> 
        </div>

     </div>   
     <div class="row">
        <div class="col-sm-12" id="table_div" >
        </div>
     </div>
  </div>   
</div>

<script>
   

    $("#promo_modal").modal({
            backdrop: 'static',
            keyboard: false
        });


    load_promo_list();

    function close_promo_modal()
    {
         $("#promo_modal").modal('hide');
    }


    function view_add_promo_modal()
    { 
         


         $(".custom-width-modal").css("width", "500px");
         $(".modal-body").css("height", "459px"); 
         $("#promo_modal").modal('show');

         $.ajax({
                     type:"POST",
                     url:"<?php echo base_url();?> Cuponing_ctrl/add_promo_ui",
                     data:{
                            'brand':$("#brand_select").val()
                         },
                     dataType:'JSON',
                     success: function(data)
                     {
                         $("#billing-body").html(data.html);
                         $(".modal-footer").html(data.footer); 

                         var currentDate = new Date().toISOString().split('T')[0]; // Get current date in 'YYYY-MM-DD' format
                         document.getElementById('dateFrom').value = currentDate;
                         document.getElementById('dateTo').value = currentDate;


                     }    
                });
    }



    function validateForm() 
    {
         var dateFromInput = document.getElementById('dateFrom');
         var dateToInput = document.getElementById('dateTo');

         var dateFrom = dateFromInput.value;
         var dateTo = dateToInput.value;

         if (new Date(dateFrom) >= new Date(dateTo)) 
         {
               Swal.fire({
                                                    icon: 'error',
                                                    title: '',
                                                    text: 'Date From must be less than Date To'                                  
                         });
              
              // Set Date From back to the current date
              dateFromInput.valueAsDate = new Date();
              dateToInput.valueAsDate = new Date();
              return false;
         }

        return true;
    }


    function save_promo()
    {
          Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to save this promo?",
                                  icon: 'warning',
                                  showCancelButton: true,
                                  confirmButtonColor: '#3085d6',
                                  cancelButtonColor: '#d33',
                                  confirmButtonText: 'Yes'
                       }).then((result) => 
                       {
                             if (result.isConfirmed) 
                             {
                                    var brand_id          = $("#brand_id").val();
                                    var promoName         = $("#promoName").val();
                                    var dateFrom          = $("#dateFrom").val();
                                    var dateTo            = $("#dateTo").val();
                                    var tenderType        = $("#tenderType").val();
                                    var quantity          = $("#quantity").val();
                                    var percentage        = $("#percentage").val();
                                    var no_vat_percentage = $("#no_vat_percentage").val();
                                    // console.log(brand_id+'\n'+promoName+'\n'+dateFrom+'\n'+dateTo+'\n'+tenderType+'\n'+quantity+'\n'+percentage);
                                    $.ajax({
                                              type:'POST',
                                              url:'<?php echo base_url(); ?>Cuponing_ctrl/save_promo',
                                              data:{
                                                        'brand_id'   : brand_id,
                                                        'promoName'  : promoName, 
                                                        'dateFrom'   : dateFrom,  
                                                        'dateTo'     : dateTo,    
                                                        'tenderType' : tenderType,
                                                        'quantity'   : quantity,  
                                                        'percentage' : percentage,
                                                        'no_vat_percentage':no_vat_percentage
                                                   },
                                              dataType:'JSON',
                                              success: function(data)
                                              {
                                                 var ico = '';
                                                 var tit = ''; 

                                                 if(data.response == 'success')   
                                                 {
                                                     ico = 'success';
                                                     tit = 'Promo successfully saved!';

                                                 }
                                                 else 
                                                 {
                                                     ico = 'error';
                                                     tit = 'Promo already exists!';
                                                 }

                                                 Swal.fire({
                                                            position: 'center',
                                                            icon: ico,
                                                            title:tit,
                                                            showConfirmButton: true                                           
                                                       })  

                                                 setTimeout(function()
                                                 {
                                                      load_promo_list();
                                                 }, 3000); // 3000 milliseconds = 3 seconds
                                              }     
                                           });
                             }
                       });
    }


    function delete_item(item_id,promo_id)
    {
         Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to remove this item in this promo?",
                                  icon: 'warning',
                                  showCancelButton: true,
                                  confirmButtonColor: '#3085d6',
                                  cancelButtonColor: '#d33',
                                  confirmButtonText: 'Yes'
                       }).then((result) => 
                       {
                             if (result.isConfirmed) 
                             {

                                 $.ajax({
                                            type:'POST',
                                            url:'<?php echo base_url(); ?>Cuponing_ctrl/delete_item',
                                            data:{
                                                    'item_id':item_id
                                                 },
                                            dataType:'JSON',
                                            success: function(data)
                                            {
                                                 Swal.fire({
                                                                 position: 'center',
                                                                 icon: 'success',
                                                                 title:'Item successfully removed from this promo',
                                                                 showConfirmButton: true                                           
                                                            }) 

                                                 setTimeout(function()
                                                 {
                                                      promo_modal(promo_id);
                                                 }, 3000); // 3000 milliseconds = 3 seconds
                                                 

                                            }     
                                        });


                             }
                       });    
    }


    function save_item(promo_id)
    {
        if($("#item_code").val() == '')
        {
             Swal.fire({
                             position: 'center',
                             icon: 'error',
                             title:'Please input an item code',
                             showConfirmButton: true                                           
                        }) 
        }    
        else  
        {

         Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to add this item in this promo?",
                                  icon: 'warning',
                                  showCancelButton: true,
                                  confirmButtonColor: '#3085d6',
                                  cancelButtonColor: '#d33',
                                  confirmButtonText: 'Yes'
                       }).then((result) => 
                       {
                             if (result.isConfirmed) 
                             {
                                 $.ajax({
                                            type:'POST',
                                            url:'<?php echo base_url();?>Cuponing_ctrl/save_item',
                                            data:{
                                                        'promo_id':promo_id,
                                                        'item_code':$("#item_code").val()  
                                                     },
                                            dataType:'JSON',
                                            success: function(data)
                                            {
                                                 Swal.fire({
                                                                 position: 'center',
                                                                 icon: 'success',
                                                                 title:'Item successfully added from this promo',
                                                                 showConfirmButton: true                                           
                                                            }) 

                                                 setTimeout(function()
                                                 {
                                                      promo_modal(promo_id);
                                                 }, 3000); // 3000 milliseconds = 3 seconds
                                            }   
                                        });
                             }
                       });
        }
    }

    function download_csv_template()
    {
        var proceed = ''; 
        io.open('POST', '<?php echo base_url('Cuponing_ctrl/download_csv_template'); ?>', {                                                                                                
                                                                                         'proceed':proceed        
                                                                                      },'_blank'); 
    }



    // function upload_item(promo_id)
    // {
    //     var formData = new FormData($('#uploadForm')[0]);
    //         formData.append('promo_id',promo_id);

    //         $.ajax({
    //             url: '<?php echo base_url(); ?>Cuponing_ctrl/upload_item',
    //             type: 'POST',
    //             data: formData,
    //             contentType: false,
    //             processData: false,
    //             success: function(response) {
    //                 console.log(response);
    //                 // Handle the response from the server
    //             },
    //             error: function(error) {
    //                 console.error(error);
    //                 // Handle errors
    //             }

    //         });
    // }




    function upload_item(promo_id)
    {
          // var form_data = new FormData();
          var form_data = new FormData($('#uploadForm')[0]);
          // form_data.append("file", document.getElementById('item_txt_file').files[0]);
          form_data.append('promo_id',promo_id);

          var files = document.getElementById('item_txt_file').files;


          if (files.length === 0) 
          {
             Swal.fire({
                                      position: 'center',
                                      icon: 'error',
                                      title: 'Please Select CSV file',
                                      showConfirmButton: true                                           
                                   })      
            red_color('item_txt_file');
         }
         else 
         {
            Swal.fire({
                                          title: 'Are you sure',
                                          text: "You want to upload these items?",
                                          icon: 'warning',
                                          showCancelButton: true,
                                          confirmButtonColor: '#3085d6',
                                          cancelButtonColor: '#d33',
                                          confirmButtonText: 'Yes'
                           }).then((result) => 
                           {
                                 if (result.isConfirmed) 
                                 {
                                          loader_();
                                          $.ajax({
                                                    url: "<?php echo base_url(); ?>Cuponing_ctrl/upload_item",
                                                    method: "POST",
                                                    data: form_data,
                                                    contentType: false,
                                                    cache: false,
                                                    processData: false,
                                                    success: function(data)
                                                    {
                                                        if(data.trim() == 'success')
                                                        {
                                                             var ico = 'success';
                                                             var tit = 'Items Successfully Added'; 
                                                             $('#brand_file').css('border-color', '');
                                                             $('#brandName').css('border-color', '');

                                                             setTimeout(function()
                                                             {
                                                                promo_modal(promo_id);
                                                             }, 3000); // 3000 milliseconds = 3 seconds                             
                                                        }
                                                        else 
                                                        {
                                                             var ico = 'error';
                                                             var tit = data; 
                                                        }


                                                         Swal.fire({
                                                                      position: 'center',
                                                                      icon: ico,
                                                                      title: tit,
                                                                      showConfirmButton: true                                           
                                                                   })       
                                                    }
                                          });
                                 }
                           });
         }
    }
</script>
                              