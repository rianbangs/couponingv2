<!-- modal here----------------------------------------------------------------------------------------- -->
   <div class="modal fade text-left" id="brand_modal" tabindex="-1" role="dialog" aria-labelledby="modal"   aria-hidden="true" >
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg custom-width-modal" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="promo_title" style="font-family: Arial, sans-serif;"> </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <div class="modal-body" id="modal-body" style="overflow-y: scroll;height: 666px;">
                             
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
            <button class="btn btn-success mr-1 mb-1" onclick="view_add_brand_modal()">add brand</button> 
        </div>

     </div>   
     <div class="row">
        <div class="col-sm-12" id="table_div" >
        </div>
     </div>
  </div>   
</div>
<script>
 display_brand_table();

 $("#brand_modal").modal({
            backdrop: 'static',
            keyboard: false
        });

function view_add_brand_modal()
{
     $(".custom-width-modal").css("width", "444px");
     $(".modal-body").css("height", "213px"); 
     $("#brand_modal").modal('show');

     $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>Cuponing_ctrl/view_add_brand_modal',
                dataType:'JSON',
                success: function(data)
                {
                    $("#modal-body").html(data.html);
                    $(".modal-footer").html(data.footer); 
                }
            });

     const brandFile = document.getElementById('brand_file');


}


function close_brand_modal()
{
    $("#brand_modal").modal('hide');
}


function update_brand(brand_id)
{
     var form_data = new FormData();
     form_data.append("file", document.getElementById('brand_file').files[0]);
     form_data.append("brandName", document.getElementById('brandName').value);
     form_data.append("brand_id",brand_id);
     var files = document.getElementById('brand_file').files;
     if($("#brandName").val() == '') 
     {
        Swal.fire({
                      position: 'center',
                      icon: 'error',
                      title: 'Please Input the brand name',
                      showConfirmButton: true                                           
                  })
        red_color('brandName');              
     }
     else 
     // if (files.length > 0)  //if naay gi select
     {
             $.ajax({
                    url: "<?php echo base_url(); ?>Cuponing_ctrl/update_brand_with_image",
                    method: "POST",
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data)
                    {                       
                         var ico = 'success';
                         var tit = 'Brand Successfully Updated'; 
                         $('#brand_file').css('border-color', '');
                         $('#brandName').css('border-color', '');

                         setTimeout(function()
                         {
                           display_brand_table();
                         }, 3000); // 3000 milliseconds = 3 seconds                
                         


                         Swal.fire({
                                      position: 'center',
                                      icon: ico,
                                      title: tit,
                                      showConfirmButton: true                                           
                                   })       
                    }
                 });
     }
     // else 
     // {
        
     // }


}



function save_brand()
{   
    var form_data = new FormData();
    form_data.append("file", document.getElementById('brand_file').files[0]);
    form_data.append("brandName", document.getElementById('brandName').value);

    var files = document.getElementById('brand_file').files;
    

    if($("#brandName").val() == '') 
    {
        Swal.fire({
                      position: 'center',
                      icon: 'error',
                      title: 'Please Input the brand name',
                      showConfirmButton: true                                           
                  })
        red_color('brandName');              
    }
    else 
    if (files.length === 0) 
    {
         Swal.fire({
                                  position: 'center',
                                  icon: 'error',
                                  title: 'Please Select an image',
                                  showConfirmButton: true                                           
                               })      
        red_color('brand_file');
    }
    else 
    {
          $.ajax({
                    url: "<?php echo base_url(); ?>Cuponing_ctrl/save_brand",
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
                             var tit = 'Brand Successfully Added'; 
                             $('#brand_file').css('border-color', '');
                             $('#brandName').css('border-color', '');

                             setTimeout(function()
                             {
                                 display_brand_table();
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
 }





 function update_brand_view(brand_id)
 {
     $(".custom-width-modal").css("width", "444px");
     $(".modal-body").css("height", "213px"); 
     $("#brand_modal").modal('show');
        $.ajax({
                    type:'POST',
                    url:'<?php echo base_url(); ?>Cuponing_ctrl/update_brand_view',
                    data:{
                            'brand_id':brand_id
                         },
                    dataType:'JSON',
                    success: function(data)
                    {
                         $("#modal-body").html(data.html);
                         $(".modal-footer").html(data.footer); 
                    }     
               });
 }
</script>
