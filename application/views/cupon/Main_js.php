
    </body>
</html> 



    <script src="<?php echo base_url(); ?>assets/cuponing/js/feather-icons/feather.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cuponing/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cuponing/js/app.js"></script>
    
    <!-- <script src="<?php echo base_url(); ?>assets/cuponing/vendors/chartjs/Chart.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cuponing/vendors/apexcharts/apexcharts.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cuponing/js/pages/dashboard.js"></script> -->

    <script src="<?php echo base_url(); ?>assets/cuponing/js/main.js"></script>
    <script src="<?php echo base_url(); ?>assets/cuponing/js/jquery-3.6.0.min.js"></script>    
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert.js"></script>         
    <script src="<?php echo base_url(); ?>assets/js/datatables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert2.all.min.js"></script>


    <script>


        function loader(div)
        {
             var loader  = ' <center><img src="<?php echo base_url(); ?>assets/cuponing/images/Cube-1s-200px.svg" style="padding-top:120px; padding-bottom:120px;"></center>';
             $(div).html(loader);
        }


         function loader_()
          {
              
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


        function extract_dataport_file(report)  
        {
             io.open('POST', '<?php echo base_url('Cuponing_ctrl/generate_txt_file'); ?>', {                                                                                                
                                                                                               'dateFrom':$("#date_from").val()         
                                                                                              },'_blank');  
        }



         function extract_file(report)
         {
                    var txt_data = new FormData();
                      var input = $('#txt_file')[0];
                      loader_();
                      $.each(input.files, function(i, file)
                      {
                          txt_data.append('files[]', file);
                      });   
                // console.log(input.files.length);     
                 console.log(txt_data);
                 if(input.files.length == 0)
                 {
                       Swal.fire({
                                                    icon: 'error',
                                                    title: '',
                                                    text: 'Please input file'                                  
                                 });        
                 }
                 else 
                 {
                   $.ajax({
                                      type: 'post',
                                      //url: '<?php echo site_url('Mms_ctrl/extract_file/')?>'+$("#store_upload").val(),
                                      url: '<?php echo site_url('Cuponing_ctrl/extract_file')?>',
                                      data: txt_data,
                                      contentType: false,
                                      processData: false, 
                                      dataType:'JSON',                       
                                      success: function(data)
                                      {                           
                                          console.log(data);
                                          swal.close();
                                          if(data.response == 'success')
                                          {                             
                                               Swal.fire({
                                                        position: 'center',
                                                        icon: 'success',
                                                        title: 'Data Successfully Imported',
                                                        showConfirmButton: true                                           
                                                      })       


                                                setTimeout(function()
                                                {
                                                     if(report == 'EOM')    
                                                     {
                                                         EOM_report();
                                                     }                
                                                     else 
                                                     if(report == 'EOD')    
                                                     {
                                                         EOD_report();
                                                     }                   
                                                }, 4000); // delay for 1 second              

                                          }
                                          else 
                                          {
                                                 
                                                 Swal.fire({
                                                    icon: 'error',
                                                    title: '',
                                                    text: data.response                                       
                                                  });                                   
                                          }

                                      },
                                }); 
                 }
          } 



        function monit_disc_items()
        {             
            loader_();
            // Define a variable to hold the URL of the view file to be loaded
            var url = '<?=base_url()?>Cuponing_ctrl/load_sales_report_ui';
            // Use jQuery's load() function to load the view file into the specified div
            $('#body_div').load(url);
        }

        function EOD_report()
        {
             loader_();           
              
             var url = '<?=base_url()?>Cuponing_ctrl/EOD_report';
             // Use jQuery's load() function to load the view file into the specified div
             $('#body_div').load(url);
        }


        function EOM_report()
        {
             loader_();           
              
             var url = '<?=base_url()?>Cuponing_ctrl/EOM_report';
             // Use jQuery's load() function to load the view file into the specified div
             $('#body_div').load(url);
        }



        function promo_masterfile()
        {
                         
              console.log("misud");
             var url = '<?=base_url()?>Cuponing_ctrl/promo_masterfile';
             // Use jQuery's load() function to load the view file into the specified div
             $('.section').load(url);
        }


        function brand_masterfile()
        {
                         
              console.log("misud");
             var url = '<?=base_url()?>Cuponing_ctrl/brand_masterfile';
             // Use jQuery's load() function to load the view file into the specified div
             $('.section').load(url);
        }    


        function variance_report()
        {
             loader_();           
              
             var url = '<?=base_url()?>Cuponing_ctrl/variance_report';
             // Use jQuery's load() function to load the view file into the specified div
             $('#body_div').load(url);

        }


        function billing_report()
        {
             loader_();           
              
             var url = '<?=base_url()?>Cuponing_ctrl/billing_report';
             // Use jQuery's load() function to load the view file into the specified div
             $('#body_div').load(url);

            
        }


         function mpdi_billing_report()
        {
             loader_();           
              
             var url = '<?=base_url()?>Cuponing_ctrl/mpdi_billing_report';
             // Use jQuery's load() function to load the view file into the specified div
             $('#body_div').load(url);            
        }


        function hideFirstColumn() 
        {
             // Initialize DataTable
             var table = $('#billing-table').DataTable();
             
             // Hide first column
             table.columns(0).visible(false);
             $("#process").hide();
        }


        function showFirstColumn() 
        {
             // Initialize DataTable
             var table = $('#billing-table').DataTable();
             
             // Hide first column
             table.columns(0).visible(true);
             $("#process").show();
        }



    


        $(document).on('click', '.sidebar-item', function()  //pang pa highlight sa side bar if ma click
        {
              // remove active class from all li elements
              $('.sidebar-item').removeClass('active');
              // add active class to the clicked li element
             $(this).addClass('active');
        });


        // Get all "li" elements with class "li-item"
        var liElements = document.querySelectorAll(".li-item");

        // Loop through each "li" element and add a "click" event listener
        liElements.forEach(function(li) {
          li.addEventListener("click", function() {
            // Remove the background color from all "li" elements
            liElements.forEach(function(li) {
              li.style.backgroundColor = "";
            });
            // Add the background color to the clicked "li" element
            li.style.backgroundColor = "#d2eaf7";
          });
        });




    
        $("#save_pass_btn").on('click', function() {
            var acc_pass = $("#acc_pass").val();
            var new_pass = $("#new_acc_pass").val();
            var con_pass = $("#con_acc_pass").val();
            $.ajax({
              url:'<?php echo base_url();?>Cuponing_ctrl/savePassword',
              type:'POST',
              data:{ acc_pass: acc_pass, new_pass: new_pass, con_pass: con_pass },                                  
              success: function(response) {
                     try{
                        var res = JSON.parse(response);
                        swal_display("success","Message!",res[0]);
                        $("#acc_pass").val("");
                        $("#new_acc_pass").val("");
                        $("#con_acc_pass").val("");
                     }catch(err){
                        swal_display("error","Invalid!",response);
                     }
              }    
            });
        });

        // function logout()
        // {
        //     console.log("nisud");
        //     $.ajax({
        //                 type: "POST",
        //                 url: "<?php echo base_url() ?>Cuponing_ctrl/logout",
        //                 dataType:'JSON',
        //                 success: function(data)
        //                 {
        //                      location.reload();
        //                 }
        //            });
        // }






       $("#item-table").dataTable(
       {                            
             "order":false,
             "columnDefs": [
                              { "width": "50px", "targets": 0 },  
                             { "width": "200px", "targets": 1 },
                             { "width": "90px", "targets": 6 },
                             { "width": "20px", "targets": 4 },
                             { "width": "50px", "targets": 7 }
                           ]
       });



  



       

        //  const quantityInput = document.getElementById('quantity');
        //  quantityInput.addEventListener('input', function() 
        //  {
        //       // Get the input value
        //        const inputValue = quantityInput.value;
        //       // Remove any non-digit characters from the input value
        //        const sanitizedValue = inputValue.replace(/\D/g, '');
        //       // Update the input field value with the sanitized value
        //       quantityInput.value = sanitizedValue;
        //  });


        // // Attach event listener for keydown event
        // quantityInput.addEventListener("keydown", function(event) 
        // {
        //      // Check if the pressed key is the Enter key
        //      if (event.key === "Enter") 
        //      {
        //          // Call your function here
        //          event.preventDefault(); // prevent form submission
        //          add_to_table();
        //          //quantityInput.blur(); // remove focus from quantity input            
        //          setTimeout(function()
        //          {
        //            document.getElementById("item_code").focus();
        //            $('#quantity').val('');
        //            $('#item_code').val('');
        //            $('#item_code').val('').focus();
        //          }, 200); // delay for 1 second     
        //      }
        // });

        function swal_display(icon,title,text)
        {
             Swal.fire({
                             icon: icon,
                             title:title,
                             text: text                                  
                         });    
        }

        // Define your function to be executed when Enter is pressed
        //function add_to_table(item_code,item_name,quantity,price,uom)


        
        function add_to_table(item_code,price,item_name,quantity,uom) 
        {          
             var table         = $('#item-table').DataTable();
             var allRowsData   = table.rows().data().toArray();
             var promo_code    = $('#promo_code').val();
             var full_name     = $('#fname').val(); 
             // var quantity      = $('#quantity').val();
             // var item_details  = $('#item_code').val().split("****");
             // var item_code     = item_details[0];
             // var item_name     = item_details[1];


             // if(full_name == '')
             // {
             //    swal_display('error','opps','Please input Full Name');
             // }
             // else 
             // if(promo_code == '')
             // {
             //    swal_display('error','opps','Please input promo code');                
             // }
             // else 
             // if(item_code == '')
             // {
             //    swal_display('error','opps','Please input item code');                
             // }
             // else 
             // if(quantity == '')
             // {
             //    swal_display('error','opps','Please input Quantity');                              
             // }
             // else 
             // {   
                 //Define the new row to be added
                 $.ajax({
                            type:'POST',
                            url:'<?php echo base_url();?>Cuponing_ctrl/check_item',
                            data:{
                                    'item_code':item_code,                                    
                                     'quantity':quantity,
                                     'item_name':item_name,
                                     'price':price,
                                     'uom':uom,
                                     allRowsData:allRowsData   
                                 },
                            dataType:'JSON',
                            success: function(data)     
                            {                         
                                


                                 //table.clear().draw(); //gi clear una para populatetan balik ang table

                                 setTimeout(function()
                                 {                                  
// inputed row added ------------------------------------------------------------------------------
                                          if(data.disc_quantity > 0)
                                          {

                                             var newRow = [
                                                             data.item_code,
                                                             data.item_name,
                                                             uom,
                                                             data.disc_quantity,
                                                             data.price,
                                                             data.discount,
                                                             data.discounted_price,                                                               
                                                             data.vatable,
                                                             ''//data.vatable              
                                                         ];                                 
                                               
                                              // Add the new row to the DataTable
                                             var rowNode = table.row.add(newRow).draw().node();                            
                                             $(rowNode).find('td').css({
                                                                          'color': 'black',
                                                                          'font-family': 'sans-serif',
                                                                          'text-align': 'center'
                                                                        });
                                          }



                                         if(data.cash_quantity > 0)
                                         {
                                             var cashRow = [
                                                         data.item_code,
                                                         data.item_name,
                                                         uom,
                                                         data.cash_quantity,
                                                         data.price,
                                                         '0.00',
                                                         '0.00', 
                                                         data.vatable, 
                                                         'NO DISC'             
                                                           ];
                                             // Add the new row to the DataTable
                                             var rowNode = table.row.add(cashRow).draw().node();                                 
                                             $(rowNode).find('td').css({
                                                                          'color': 'red',
                                                                          'font-family': 'sans-serif',
                                                                          'text-align': 'center'
                                                                        });   
                                         }

//existing row gi populate balik --------------------------------------------------------------------------
                                         //console.log(data.new_rows_data);
                                  //        if(data.new_rows_data.length > 0)
                                  //        {                                            
                                  //            for (let i = 0; i < data.new_rows_data.length; i++)
                                  //            {
                                  //                // for (let j = 0; j < data.new_rows_data[i].length; j++) 
                                  //                // {
                                  //                //    console.log(data.new_rows_data[i][j]);
                                  //                // }
                                  //                  var item_code = data.new_rows_data[i][0];
                                  //                  var item_name = data.new_rows_data[i][1];
                                  //                  var quantity  = data.new_rows_data[i][2];
                                  //                  var price     = data.new_rows_data[i][3];
                                  //                  var discount  = data.new_rows_data[i][4];
                                  //                  var discounted_price = data.new_rows_data[i][5];
                                  //                  var vatable   = data.new_rows_data[i][6]; 
                                  //                  if(vatable == 'CASH')
                                  //                  {
                                  //                    var color = "red";
                                  //                  }
                                  //                  else 
                                  //                  {
                                  //                    var color = "black";
                                  //                  }

                                  //                    var existRow = [
                                  //                            item_code,
                                  //                            item_name,
                                  //                            quantity,
                                  //                            price,
                                  //                            discount,
                                  //                            discounted_price,  
                                  //                            vatable             
                                  //                              ];
                                  //                    // Add the new row to the DataTable
                                  //                    var rowNode = table.row.add(existRow).draw().node();                                 
                                  //                    $(rowNode).find('td').css({
                                  //                                                 'color': color,
                                  //                                                 'font-family': 'sans-serif',
                                  //                                                 'text-align': 'center'
                                  //                                               });   

                                  //            }
                                  //        }



                                   }, 300); // delay for 1 second    

                                 //$('#quantity').val('');
                                 //$('#item_code').val('').focus();
                             }
                        });

             //}

             // console.log("Enter key pressed!");
        }


// -------------------------------------------------------dropdown fro item-----------------------------------------
        

     // var dropdown = $('<ul>').addClass('my-dropdown dropdown-menu');

     // // add keydown event listener to the input field
     // $('#item_code').on('keydown', function(e) 
     // {
     //   var keycode = e.keyCode || e.which;
  
     //   if (keycode === 40) 
     //   { // arrow down key
     //     e.preventDefault();
     //     var next = dropdown.find('.dropdown-item.active').next();
     //     if (next.length > 0) 
     //     {
     //       dropdown.find('.dropdown-item.active').removeClass('active');
     //       next.addClass('active');
     //     }
     //     else
     //     {
     //       dropdown.find('.dropdown-item.active').removeClass('active');
     //       dropdown.find('.dropdown-item').first().addClass('active');
     //     }
     //   } 
     //   else 
     //   if (keycode === 38)
     //   { // arrow up key
     //     e.preventDefault();
     //     var prev = dropdown.find('.dropdown-item.active').prev();
     //     if (prev.length > 0)
     //     {
     //       dropdown.find('.dropdown-item.active').removeClass('active');
     //       prev.addClass('active');
     //     }
     //     else
     //     {
     //       dropdown.find('.dropdown-item.active').removeClass('active');
     //       dropdown.find('.dropdown-item').last().addClass('active');
     //     }
     //   }



     // });



     // $('#item_code').on('keyup', function(e) 
     // {
     //       var keycode = e.keyCode || e.which;
     //       if (keycode === 13)
     //       {
     //            document.getElementById("quantity").focus();
     //       }
     //  });





     // add keyup event listener to the input field
     // $('#item_code').on('keyup', function(e) 
     // {
     //       var keycode = e.keyCode || e.which;
     //       if (keycode === 13)
     //       { // enter key
     //              var active = dropdown.find('.dropdown-item.active');
     //              if (active.length > 0) 
     //              {
     //                  $(this).val(active.text()); // set input value to the active item
     //              }
     //              dropdown.hide(); // hide the dropdown

     //        }
     //        else 
     //        if (keycode !== 40 && keycode !== 38) 
     //        {
     //              var inputVal = $(this).val();
     //              if (inputVal != '') 
     //              {
     //                   $.ajax({
     //                              url:'<?php echo base_url();?>Cuponing_ctrl/search_item',
     //                              type:'POST',
     //                              data:{
     //                                      inputVal: inputVal 
     //                                   },  
     //                              dataType:'json',                                 
     //                              success: function(response) 
     //                              {
     //                                  dropdown.empty();
     //                                  $.each(response, function(index, item) 
     //                                  {                                 
     //                                      var li = $('<li>').addClass('dropdown-item').text(item.item_code+'****'+item.item_name);
     //                                      dropdown.append(li);
     //                                  });
     //                                  $('#item_code').next().replaceWith(dropdown);
     //                                  dropdown.show();
     //                                  dropdown.find('.dropdown-item').first().addClass('active');
     //                             }
     //                        });
     //              }
     //              else 
     //              {
     //                 dropdown.hide();
     //              }
     //        }
     // });

     // // add click event listener to dropdown items
     // $(document).on('click', '.dropdown-item', function() 
     // {
     //       $('#item_code').val($(this).text());
     //       dropdown.hide();
     // });



// ----------------------------------------------------------------- validate uni promo code ------------------------------------------------
     

     function check_promo_code()
     {
             $.ajax({
                            type:'POST',
                            url:'<?php echo base_url(); ?>Cuponing_ctrl/search_promo_code',
                            data:{
                                    'promo_code':$("#promo_code").val()
                                 },
                            dataType:'JSON',
                            success: function(data)
                            {
                                if(data.response == 'success')    
                                {
                                     document.getElementById("item_code").focus();
                                }
                                else 
                                {
                                    var reponse_arr = data.response.split("^"); 
                                    swal_display('error','opps','this promocode is already redeemed by '+reponse_arr[0]+" on "+reponse_arr[1]);                                    
                                }
                            }
                       });
     }

     $('#promo_code').on('keydown', function(e) 
     {  
         var keycode = e.keyCode || e.which;
         if (keycode === 13 || keycode === 9)
         { // enter key
             check_promo_code();               
         }             
     });



//--------------------------------------------------------------dropdown first name -----------------------------------------------------
     var dropdown_fname = $('<ul>').addClass('my-dropdown dropdown-menu dropdown-fname');

      // add keydown event listener to the input field
     $('#firstname').on('keydown', function(e) 
     {
           var keycode = e.keyCode || e.which;
      
           if (keycode === 40) 
           { // arrow down key
             e.preventDefault();
             var next = dropdown_fname.find('.dropdown-item.active').next();
             if (next.length > 0) 
             {
               dropdown_fname.find('.dropdown-item.active').removeClass('active');
               next.addClass('active');
             }
             else
             {
               dropdown_fname.find('.dropdown-item.active').removeClass('active');
               dropdown_fname.find('.dropdown-item').first().addClass('active');
             }
           } 
           else 
           if (keycode === 38)
           { // arrow up key
             e.preventDefault();
             var prev = dropdown_fname.find('.dropdown-item.active').prev();
             if (prev.length > 0)
             {
               dropdown_fname.find('.dropdown-item.active').removeClass('active');
               prev.addClass('active');
             }
             else
             {
               dropdown_fname.find('.dropdown-item.active').removeClass('active');
               dropdown_fname.find('.dropdown-item').last().addClass('active');
             }
           }
     }); 




     $('#firstname').on('keyup', function(e) 
     {
           var keycode = e.keyCode || e.which;
           if (keycode === 13)
           { // enter key
                  var active = dropdown_fname.find('.dropdown-item.active');
                  if (active.length > 0) 
                  {
                      $(this).val(active.text()); // set input value to the active item
                      update_customer_fields(active.text());
                  }
                  dropdown_fname.hide(); // hide the dropdown

                  setTimeout(function() 
                  { 
                      if($("#lname").val() != '')
                      { 
                         document.getElementById("promo_code").focus();
                      }
                      else 
                      {
                         document.getElementById("#lname").focus();                    
                      }  
                  }, 200);
                     


            }
            else 
            if (keycode !== 40 && keycode !== 38) 
            {
                 var inputVal = $(this).val();
                 if (inputVal != '') 
                 {  
                         $.ajax({
                                  url:'<?php echo base_url();?>Cuponing_ctrl/search_name_details',
                                  type:'POST',
                                  data:{
                                          'lname':  $("#lname").val(),
                                          'fname' : inputVal  
                                       },  
                                  dataType:'json',                                 
                                  success: function(response) 
                                  {
                                      dropdown_fname.empty();
                                      $.each(response, function(index, item) 
                                      {                                 
                                          var li = $('<li>').addClass('dropdown-item').text(item.fname).css({
                                                                                                                     'font-family': 'sans-serif',
                                                                                                                     'font-size': '16px'
                                                                                                                });     
                                          dropdown_fname.append(li);
                                          
                                          if(item.fname == '')   
                                          {
                                             dropdown_fname.hide();
                                          }
                                          else 
                                          {
                                             dropdown_fname.show();
                                          }

                                          dropdown_fname.find('.dropdown-item').first().addClass('active');

                                      });
                                      $('#firstname').next().replaceWith(dropdown_fname);

                                 }
                            });
                 }
                 else 
                 {
                      dropdown_fname.hide();
                 }
            }
      });

     

    
     $(document).on('click', '.dropdown-fname li', function() 
     {
          $('#firstname').val($(this).text());
          $('.dropdown-fname').hide();
          update_customer_fields($(this).text());
     });



     function update_customer_fields(fname)
     {
             $.ajax({
                     type:'POST',
                     url:'<?php echo base_url();?>Cuponing_ctrl/search_name_details',
                     data:{
                                 'lname': $("#lname").val(),
                                 'fname' : fname
                          },
                     dataType:'JSON',
                     success: function(data)
                     {
                        
                             $.each(data, function(index, item) 
                             {
                                 $("#lname").val(item.lname); 
                                 $("#phone_no").val(item.phone_no);   

                                 $(document).ready(function() 
                                 {
                                      // set the default selected option to "option2"
                                      $("#year").val(item.birth_year);
                                 });                            

                             });
                        
                     }     
                 }); 
     }  










// --------------------------------------------------------------dropdown last name -----------------------------------------------------
      var dropdown_lname = $('<ul>').addClass('my-dropdown dropdown-menu dropdown-lname');


      // add keydown event listener to the input field
     $('#lname').on('keydown', function(e) 
     {
           var keycode = e.keyCode || e.which;
      
           if (keycode === 40) 
           { // arrow down key
             e.preventDefault();
             var next = dropdown_lname.find('.dropdown-item.active').next();
             if (next.length > 0) 
             {
               dropdown_lname.find('.dropdown-item.active').removeClass('active');
               next.addClass('active');
             }
             else
             {
               dropdown_lname.find('.dropdown-item.active').removeClass('active');
               dropdown_lname.find('.dropdown-item').first().addClass('active');
             }
           } 
           else 
           if (keycode === 38)
           { // arrow up key
             e.preventDefault();
             var prev = dropdown_lname.find('.dropdown-item.active').prev();
             if (prev.length > 0)
             {
               dropdown_lname.find('.dropdown-item.active').removeClass('active');
               prev.addClass('active');
             }
             else
             {
               dropdown_lname.find('.dropdown-item.active').removeClass('active');
               dropdown_lname.find('.dropdown-item').last().addClass('active');
             }
           }
     }); 


     $('#lname').on('keyup', function(e) 
     {
           var keycode = e.keyCode || e.which;
           if (keycode === 13)
           { // enter key
                  var active = dropdown_lname.find('.dropdown-item.active');
                  if (active.length > 0) 
                  {
                      $(this).val(active.text()); // set input value to the active item
                  }
                  dropdown_lname.hide(); // hide the dropdown
                  document.getElementById("promo_code").focus();

            }
            else 
            if (keycode !== 40 && keycode !== 38) 
            {
                 var inputVal = $(this).val();
                 if (inputVal != '') 
                 {  
                         $.ajax({
                                  url:'<?php echo base_url();?>Cuponing_ctrl/search_name_details',
                                  type:'POST',
                                  data:{
                                          'lname': inputVal,
                                          'fname' : $("#firstname").val()    
                                       },  
                                  dataType:'json',                                 
                                  success: function(response) 
                                  {
                                      dropdown_lname.empty();
                                      $.each(response, function(index, item) 
                                      {                                 
                                          var li = $('<li>').addClass('dropdown-item').text(item.lname).css({
                                                                                                                     'font-family': 'sans-serif',
                                                                                                                     'font-size': '16px'
                                                                                                                });     
                                          dropdown_lname.append(li);
                                          if(item.lname == '')
                                          {
                                             dropdown_lname.hide();
                                          }
                                          else 
                                          {
                                             dropdown_lname.show();
                                          }
                                          dropdown_lname.find('.dropdown-item').first().addClass('active');

                                      });
                                      $('#lname').next().replaceWith(dropdown_lname);

                                 }
                            });
                 }
                 else 
                 {
                      dropdown_lname.hide();
                 }
            }
      });

     

    
     $(document).on('click', '.dropdown-lname li', function() 
     {
          $('#lname').val($(this).text());
          $('.dropdown-lname').hide();
          // $.ajax({
          //            type:'POST',
          //            url:'<?php echo base_url();?>Cuponing_ctrl/search_name_details',
          //            data:'lname': $(this).text(),
          //                 'fname' : $("#firstname").val()
          //            dataType:'JSON',
          //            success: function(data)
          //            {
          //                if($("#firstname").val() != '')
          //                {
          //                    $.each(response, function(index, item) 
          //                    {
          //                        $("#firstname").val();
          //                    });
          //                }
          //            }     
          //        }); 

     });







      

// ------------------------------------------------------------------dropdown for full name--------------------------------------------
      // add keyup event listener to the input field
     var dropdown2 = $('<ul>').addClass('my-dropdown dropdown-menu');


      // add keydown event listener to the input field
     $('#fname').on('keydown', function(e) 
     {
       var keycode = e.keyCode || e.which;
  
       if (keycode === 40) 
       { // arrow down key
         e.preventDefault();
         var next = dropdown2.find('.dropdown-item.active').next();
         if (next.length > 0) 
         {
           dropdown2.find('.dropdown-item.active').removeClass('active');
           next.addClass('active');
         }
         else
         {
           dropdown2.find('.dropdown-item.active').removeClass('active');
           dropdown2.find('.dropdown-item').first().addClass('active');
         }
       } 
       else 
       if (keycode === 38)
       { // arrow up key
         e.preventDefault();
         var prev = dropdown2.find('.dropdown-item.active').prev();
         if (prev.length > 0)
         {
           dropdown2.find('.dropdown-item.active').removeClass('active');
           prev.addClass('active');
         }
         else
         {
           dropdown2.find('.dropdown-item.active').removeClass('active');
           dropdown2.find('.dropdown-item').last().addClass('active');
         }
       }



     }); 


     $('#fname').on('keyup', function(e) 
     {
           var keycode = e.keyCode || e.which;
           if (keycode === 13)
           { // enter key
                  var active = dropdown2.find('.dropdown-item.active');
                  if (active.length > 0) 
                  {
                      $(this).val(active.text()); // set input value to the active item
                  }
                  dropdown2.hide(); // hide the dropdown
                  document.getElementById("promo_code").focus();

            }
            else 
            if (keycode !== 40 && keycode !== 38) 
            {
                  var inputVal = $(this).val();
                  if (inputVal != '') 
                  {
                       $.ajax({
                                  url:'<?php echo base_url();?>Cuponing_ctrl/search_name',
                                  type:'POST',
                                  data:{
                                          inputVal: inputVal 
                                       },  
                                  dataType:'json',                                 
                                  success: function(response) 
                                  {
                                      dropdown2.empty();
                                      $.each(response, function(index, item) 
                                      {                                 
                                          var li = $('<li>').addClass('dropdown-item').text(item.full_name).css({
                                                                                                                     'font-family': 'sans-serif',
                                                                                                                     'font-size': '16px'
                                                                                                                });
                                          dropdown2.append(li);
                                      });
                                      $('#fname').next().replaceWith(dropdown2);
                                      dropdown2.show();
                                      dropdown2.find('.dropdown-item').first().addClass('active');

                                 }
                            });
                  }
                  else 
                  {
                     dropdown2.hide();
                  }
            }
     });

     // // add click event listener to dropdown items
     // $(document).on('click', '.dropdown-item', function() 
     // {
     //       $('#item_code').val($(this).text());
     //       dropdown2.hide();
     // });


 //------dropdown ordering number ----------------------------------------------------------------
  // add keyup event listener to the input field
  var dropdown3 = $('<ul>').addClass('my-dropdown dropdown-menu dropdown-ordering');

    // add keydown event listener to the input field
     $('#order_number').on('keydown', function(e) 
     {
       var keycode = e.keyCode || e.which;
  
       if (keycode === 40) 
       { // arrow down key
         e.preventDefault();
         var next = dropdown3.find('.dropdown-item.active').next();
         if (next.length > 0) 
         {
           dropdown3.find('.dropdown-item.active').removeClass('active');
           next.addClass('active');
         }
         else
         {
           dropdown3.find('.dropdown-item.active').removeClass('active');
           dropdown3.find('.dropdown-item').first().addClass('active');
         }
       } 
       else 
       if (keycode === 38)
       { // arrow up key
         e.preventDefault();
         var prev = dropdown3.find('.dropdown-item.active').prev();
         if (prev.length > 0)
         {
           dropdown3.find('.dropdown-item.active').removeClass('active');
           prev.addClass('active');
         }
         else
         {
           dropdown3.find('.dropdown-item.active').removeClass('active');
           dropdown3.find('.dropdown-item').last().addClass('active');
         }
       }



     }); 


  $('#order_number').on('keyup', function(e) 
  {
         var table         = $('#item-table').DataTable();
         var keycode = e.keyCode || e.which;
         if (keycode === 13)
         { // enter key
               var active = dropdown3.find('.dropdown-item.active');
               if (active.length > 0) 
               {
                     $(this).val(active.text()); // set input value to the active item
                     //ani populate sa table
                     $.ajax({
                                type:'POST',
                                url:'<?php echo base_url(); ?>Cuponing_ctrl/get_Take_Order_Line',
                                data:{'order_number':active.text()},
                                dataType:'JSON',
                                success: function(data)
                                {
                                     //console.log(data.item_line_arr);
                                     table.clear().draw(); //gi clear una para populatetan balik ang table
                                     for(var a=0;a<data.item_line_arr.length;a++)
                                     {                                           
                                         add_to_table(data.item_line_arr[a][0],
                                                      data.item_line_arr[a][1],
                                                      data.item_line_arr[a][2],
                                                      data.item_line_arr[a][3],
                                                      data.item_line_arr[a][4]); 
                                     } 
                                }
                            });

               }
               dropdown3.hide(); // hide the dropdown
               document.getElementById("firstname").focus();
         }
         else 
         if (keycode !== 40 && keycode !== 38) 
         {
                var inputVal = $(this).val();
                if (inputVal != '') 
                {
                    $.ajax({
                                  url:'<?php echo base_url();?>Cuponing_ctrl/search_order_number',
                                  type:'POST',
                                  data:{
                                          inputVal: inputVal 
                                       },  
                                  dataType:'json',                                 
                                  success: function(response) 
                                  {
                                      dropdown3.empty();
                                      console.log(response.ord_num_arr);


                                      $.each(response, function(index, item) 
                                      {
                                      for(var a=0;a<response.ord_num_arr.length;a++) 
                                      {                                        
                                          var li = $('<li>').addClass('dropdown-item').text(response.ord_num_arr[a]).css({
                                                                                                                              'font-family': 'sans-serif',
                                                                                                                              'font-size': '16px'
                                                                                                                         });
                                          dropdown3.append(li);
                                          if(response.ord_num_arr[a] == '')
                                          {
                                                 dropdown3.hide();                                            
                                          }
                                          else 
                                          {
                                                 dropdown3.show();                                            
                                          }
                                      }                                 
                                      });
                                      $('#order_number').next().replaceWith(dropdown3);
                                      dropdown3.find('.dropdown-item').first().addClass('active');

                                 }
                            });           
                }
                else 
                {
                  dropdown2.hide();
                }
         }
  });



    // add click event listener to dropdown items
     $(document).on('click', '.dropdown-ordering li', function() 
     {
          var table         = $('#item-table').DataTable();
          $('#order_number').val($(this).text());
          $('.dropdown-ordering').hide();
          document.getElementById("firstname").focus();


             //ani populate sa table
                     $.ajax({
                                type:'POST',
                                url:'<?php echo base_url(); ?>Cuponing_ctrl/get_Take_Order_Line',
                                data:{'order_number':$(this).text()},
                                dataType:'JSON',
                                success: function(data)
                                {
                                     //console.log(data.item_line_arr);
                                     table.clear().draw(); //gi clear una para populatetan balik ang table
                                     for(var a=0;a<data.item_line_arr.length;a++)
                                     {                                           
                                         add_to_table(data.item_line_arr[a][0],
                                                      data.item_line_arr[a][1],
                                                      data.item_line_arr[a][2],
                                                      data.item_line_arr[a][3],
                                                      data.item_line_arr[a][4]); 
                                     } 
                                }
                            });
     });




// -----submit order--------------------------------------------------------------

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



function submit_order_()
{
     var table       = $('#item-table').DataTable();
     var allRowsData = table.rows().data().toArray();
     var fname       = $("#fname").val();
     var promo_code  = $("#promo_code").val();  
      $.ajax({
                 type:'POST',
                 url:'<?php echo base_url(); ?>Cuponing_ctrl/submit_order',
                 data:{
                         allRowsData:allRowsData,
                         'fname':fname,
                         'promo_code':promo_code
                      },
                 dataType:'JSON',
                 success: function(data)
                 {

                 }
          });
}      


function generate_Monitoring_List_report()
{
     var table        = $('#sales-table').DataTable();
     var allRowsData  = table.rows().data().toArray();
     var from_date    = $('#date_from').val();
     var date_to      = $('#date_to').val();
     var db_id        = $('#store').val();

      io.open('POST', '<?php echo base_url('Cuponing_ctrl/generate_Monitoring_List_report'); ?>', {
                                                                                                      allRowsData: JSON.stringify(allRowsData),
                                                                                                      'from_date':from_date,
                                                                                                      'date_to':date_to,
                                                                                                      'db_id':db_id                                                                                                     
                                                                                                   }, '_blank'); //_blank 
}


function generate_eod_report()
{
     var table        = $('#eod-table').DataTable();
     var allRowsData  = table.rows().data().toArray();
     var from_date    = $('#date_from').val();
     io.open('POST', '<?php echo base_url('Cuponing_ctrl/generate_eod_report'); ?>', {
                                                                                                      allRowsData: JSON.stringify(allRowsData),
                                                                                                      'from_date':from_date                                                                          
                                                                                                   }, '_blank'); //_blank 
}


function generate_eom_report()
{
     var table        = $('#eod-table').DataTable();
     var allRowsData  = table.rows().data().toArray();
     var from_date    = $('#date_from').val();
     var date_to      = $('#date_to').val();
     var db_id        = $('#store').val();
     io.open('POST', '<?php echo base_url('Cuponing_ctrl/generate_eom_report'); ?>', {
                                                                                                      allRowsData: JSON.stringify(allRowsData),
                                                                                                      'from_date':from_date,
                                                                                                      'date_to':date_to,
                                                                                                      'db_id':db_id                                                                           
                                                                                                   }, '_blank'); //_blank 
}


function generate_variance_report()
{
     var table        = $('#Variance-table').DataTable();
     var allRowsData  = table.rows().data().toArray();
     var from_date    = $('#date_from').val();
     var date_to      = $('#date_to').val();
     io.open('POST', '<?php echo base_url('Cuponing_ctrl/generate_variance_report'); ?>', {
                                                                                                      allRowsData: JSON.stringify(allRowsData),
                                                                                                      'from_date':from_date,
                                                                                                      'date_to':date_to                                                                          
                                                                                                   }, '_blank'); //_blank 
}






function generate_billing_report()
{
     var table        = $('#billing-table').DataTable();
     var allRowsData  = table.rows().data().toArray();
     var from_date    = $('#date_from').val();
     var date_to      = $('#date_to').val();

     loader_();   
      

     $.ajax({
                 type:'POST',
                 url:'<?php echo base_url(); ?>Cuponing_ctrl/get_promo_billing_batch',
                 data:{
                            'from_date':from_date,
                            'date_to'  :date_to 
                      },
                 dataType:'json',
                 success: function(data)
                 {
                        //console.log(data.batch_list);
                        swal.close();
                        $("#billing-body").html(data.html);
                        $("#billing_modal").modal('show');
                 }     

            });



      
}



function print_billing(batch_id)
{
     var from_date    = $('#date_from').val();
     var date_to      = $('#date_to').val();
     io.open('POST', '<?php echo base_url('Cuponing_ctrl/generate_billing_report'); ?>', {
                                                                                             'from_date':from_date,
                                                                                             'date_to':date_to,
                                                                                             'batch_id':batch_id                                                                          
                                                                                         }, '_blank'); //_blank
}



function close_billing_modal()
{
     $("#billing_modal").modal('hide');    
}


function submit_order()
{
    
     var table        = $('#item-table').DataTable();
     var allRowsData  = table.rows().data().toArray();
     var fname        = $("#firstname").val();
     var lname        = $("#lname").val();
     var promo_code   = $("#promo_code").val();  
     var order_number = $("#order_number").val();
     var phone_no     = $("#phone_no").val();
     var year         = $("#year").val();
     var promoCodePattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/;

    // $.ajax({
    //              type:'POST',
    //              url:'<?php echo base_url(); ?>Cuponing_ctrl/submit_order',
    //              data:{
    //                      allRowsData:allRowsData,
    //                      'fname':fname,
    //                      'promo_code':promo_code
    //                   },
    //              dataType:'JSON',
    //              success: function(data)
    //              {

    //              }
    //       });

     var promoCodeValue = $("#promo_code").val();



     if (promoCodeValue.length < 8) 
     {
          swal_display('error','opps','PROMO CODE must be atleast 8 digit');
          // valid promo code
     }
     // else    
     // if (!promoCodePattern.test(promoCodeValue)) 
     // {
     //    // Promo code is not valid
     //    // Do something here, like disabling a submit button or displaying an error message
     //    swal_display('error','opps','PROMO CODE must be alpha numeric');        
     // }
     else
     if(fname == '')
     {
         swal_display('error','opps','Please input Full Name');
     }
     else 
     if(promo_code == '')
     {
          swal_display('error','opps','Please input promo code');                
     }
     else 
     if(order_number == '')
     {
          swal_display('error','opps','Please input ordering number');                        
     }
    // else 
    // if(allRowsData.length == 0)
    // {
    //     swal_display('error','opps','Please select an item');                   
    // }
    else 
    {



      $.ajax({
                            type:'POST',
                            url:'<?php echo base_url(); ?>Cuponing_ctrl/search_promo_code',
                            data:{
                                    'promo_code':$("#promo_code").val()
                                 },
                            dataType:'JSON',
                            success: function(data)
                            {
                                if(data.response == 'success')    
                                {                                    
                                     io.open('POST', '<?php echo base_url('Cuponing_ctrl/submit_order'); ?>', {
                                                                                                                    allRowsData: JSON.stringify(allRowsData),
                                                                                                                    fname: fname,
                                                                                                                    lname:lname,
                                                                                                                    promo_code: promo_code,
                                                                                                                    order_number:order_number,
                                                                                                                    phone_no:phone_no,
                                                                                                                    year:year
                                                                                                                }, '_blank'); //_blank
                                     $("#order_number").val('');
                                     $("#firstname").val('');
                                     $("#lname").val('');
                                     $("#phone_no").val('');
                                     $("#promo_code").val('');                                  

                                     // Get the select element
                                     var yearSelect = document.getElementById("year");
                                     // Set the default value to the latest year (current year)
                                     yearSelect.value = new Date().getFullYear().toString();
                                     //clear table content
                                     table.clear().draw();
                                }
                                else 
                                {
                                    var reponse_arr = data.response.split("^"); 
                                    swal_display('error','opps','this promocode is already redeemed by '+reponse_arr[0]+" on "+reponse_arr[1]);                                    
                                }
                            }
                       });


    }






  
    // // Stop the interval after 5 seconds (5000 milliseconds)
    // setTimeout(function() 
    // {
    //   clearInterval(intervalID);
    //   console.log("Interval stopped");
    // }, 5000);
     




      // // Open a new window and load the document to print
      // var printWindow = window.open('', 'Print', 'height=600,width=800');

      // // Write the document content to the new window
      // printWindow.document.write('<html><head><title>Print Document</title></head><body>');
      // printWindow.document.write('<h1>Hello, World!</h1>');
      // printWindow.document.write('</body></html>');

      // // Print the document
      // printWindow.print();
      // printWindow.close();


}


//phone number validation -----------------------------------------------------------------------
    var phoneNo = document.getElementById("phone_no");
if (phoneNo !== null) {
phoneNo.addEventListener("keyup", function(event) 
{
     // Get the input value
     var input = phoneNo.value ;

     // Remove any non-digit characters
     input = input.replace(/\D/g,'');

     // Remove the first character if it is not 9
     if (input.charAt(0) !== '9') 
     {
         input = input.slice(1);
     }

     // Limit the input to 10 digits
     if (input.length > 10) 
     {
         input = input.slice(0, 10);
     }

     // Update the input value
     phoneNo.value = input;
});






var promoCodeInput = document.getElementById("promo_code");
var promoCodePattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/;

var timer;
promoCodeInput.addEventListener("input", function() 
{   
      var promoCodeValue = promoCodeInput.value.trim();
      $("#promo_code").val(promoCodeValue.toUpperCase());

     // Limit the input to 10 digits
     if (promoCodeValue.length > 11) 
     {
         input = promoCodeValue.slice(0, 11);
         $("#promo_code").val(input);
     }


    clearTimeout(timer); // reset the timer on input
    timer = setTimeout(function() 
    {


     


      if (promoCodeValue.length != 8 ) 
      {
        swal_display('error','opps','Transaction CODE must be  8 digit');
       
        // valid promo code
      }
      // else      
      // if (!promoCodePattern.test(promoCodeValue)) 
      // {
      //   // Promo code is not valid
      //   // Do something here, like disabling a submit button or displaying an error message
      //   swal_display('error','opps','PROMO CODE must be alpha numeric');        
      // }
      
    }, 2000); // set a delay of 5 seconds  
});
} // If PhoneNo is null

 
// function toggle_checkboxes(table_id, is_checked)
// {
//   var table = $('#' + table_id).DataTable();
//   var numPages = table.page.info().pages;

//   for (var i = 0; i < numPages; i++) {
//     table.page(i).draw(false);
//     $('#' + table_id).find('input[type="checkbox"]').prop('checked', is_checked);
//   }

//   $("#checkall").toggle(!is_checked);
//   $("#uncheckall").toggle(is_checked);
// }

function toggle_checkboxes(table_id, is_checked) 
{
  var table = $('#' + table_id).DataTable();
  table.column(0).nodes().to$().find('input[type="checkbox"]').prop('checked', is_checked);
 // $("#checkall, #uncheckall").toggle();
}




function check_all(table_id) 
{
  toggle_checkboxes(table_id, true);
}

function uncheck_all(table_id) {
  toggle_checkboxes(table_id, false);
}






function process_billing(table_id)
{
     var status       = $("#status").val();     
     var from_date    = $('#date_from').val();
     var date_to      = $('#date_to').val();
     dataTable   = $("#"+table_id).DataTable();            
     var checked = []; 
     dataTable.rows().nodes().to$().find('input[class="checkbox"]:checked').each(function()
     {                   
         checked.push(this.value);
     });
    //console.log(checked);


    if(checked.length == 0) 
    {
        swal_display('error','opps','Please Select transaction');
    }
    else 
    {


             Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to submit this billing?",
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
                                             url:'<?php echo base_url(); ?>Cuponing_ctrl/process_billing',
                                             data:{
                                                      checked    :checked,
                                                      'status'   :status,
                                                      'from_date':from_date,
                                                      'date_to'  :date_to
                                                  },
                                             dataType:'JSON',
                                             success: function(data)
                                             {
                                                   Swal.fire({
                                                                  position: 'center',
                                                                  icon: 'success',
                                                                  title: 'Successfully billed',
                                                                  showConfirmButton: true                                           
                                                             })  

                                                    io.open('POST', '<?php echo base_url('Cuponing_ctrl/generate_billing_report'); ?>', {
                                                                                             'from_date':from_date,
                                                                                             'date_to':date_to,
                                                                                             'batch_id':data.batch_id                                                                          
                                                                                         }, '_blank'); //_blank 
                                                   
                                                   setTimeout(function()
                                                   {
                                                         validateDates();    
                                                   }, 1000); // delay for 1 second   
                                                 
                                             }
                                        });
                             }
                      })  
    }
}   




 // function check_all(table_id)
 // {
 //    var table = $('#'+table_id).DataTable();
 //    var numPages = table.page.info().pages;
 //    for (var i = 0; i < numPages; i++) 
 //    {
 //         table.page(i).draw(false);
 //         $('#'+table_id).find('input[class="checkbox"]').prop('checked', true);
 //    }

 //   $("#checkall").hide();
 //   $("#uncheckall").show();   
    
 // }


 // function uncheck_all(table_id)
 // {
 //     var table = $('#'+table_id).DataTable();
 //     var numPages = table.page.info().pages;
 //     for (var i = 0; i < numPages; i++) 
 //     {
 //         table.page(i).draw(false);
 //         $('#'+table_id).find('input[class="checkbox"]').prop('checked', false);
 //     }

 //     $("#checkall").show();      
 //     $("#uncheckall").hide();   
 // }









function check_checkboxes(table_id) 
{
    var table             = $('#' + table_id).DataTable();
    var totalCheckboxes   = table.$('input[type="checkbox"]').length;
    var checkedCheckboxes = table.$('input[type="checkbox"]:checked').length;
    var isAllChecked      = totalCheckboxes === checkedCheckboxes;
    $('#checkbox_main').prop('checked', isAllChecked);
}



function extract_billing(table_id)
{

     dataTable   = $("#"+table_id).DataTable();            
     var checked = []; 
     dataTable.rows().nodes().to$().find('input[class="checkbox"]:checked').each(function()
     {                   
         checked.push(this.value);
     });


     if(checked.length == 0) 
     {
          swal_display('error','opps','Please Select transaction');
     }
     else 
     {
        io.open('POST', '<?php echo base_url('Cuponing_ctrl/generate_excel_file'); ?>', { 
                                                                                             checked: JSON.stringify(checked),
                                                                                             'dateFrom':$("#date_from").val(),
                                                                                             'dateTo':$("#date_to").val()             
                                                                                        },'_blank');      
     }


     // $.ajax({
     //            type:'POST',
     //            url:'<?php echo base_url() ?>Cuponing_ctrl/generate_excel_file',
     //            data:{
     //                    checked:checked
     //                 },
     //            dataType:'JSON',
     //            success: function(data)
     //            {

     //            }
     //        }); 
}



function post_billing(batch_id)
{
     var from_date      = $('#date_from').val();
     var date_to        = $('#date_to').val(); 
     var invoice_number = $('.invoice-'+batch_id).val();

     if($(".invoice-"+batch_id).val() == '')
     {
          Swal.fire({
                         icon: 'error',
                         title: '',
                         text: 'Please input Invoice Number'                                  
                    });     
     }
     else 
     {
          Swal.fire({
                          title: 'Are you sure',
                          text: "You want to post this transaction?",
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#3085d6',
                          cancelButtonColor: '#d33',
                          confirmButtonText: 'Yes'
                    }).then((result) => 
                    {                        
                         if (result.isConfirmed) 
                         {
                                loader('#billing-body');

                                $.ajax({
                                             type:'POST',
                                             url:'<?php echo base_url(); ?>Cuponing_ctrl/set_paid',
                                             data:{
                                                     'batch_id':batch_id,
                                                     'invoice_number':invoice_number
                                                  },
                                             dataType:'json',
                                             success: function(data)
                                             {
                                                    $.ajax({
                                                                 type:'POST',
                                                                 url:'<?php echo base_url(); ?>Cuponing_ctrl/get_promo_billing_batch',
                                                                 data:{
                                                                            'from_date':from_date,
                                                                            'date_to'  :date_to 
                                                                      },
                                                                 dataType:'json',
                                                                 success: function(data)
                                                                 {                                                   
                                                                        $("#billing-body").html(data.html);   

                                                                        Swal.fire({
                                                                                         position: 'center',
                                                                                         icon: 'success',
                                                                                         title: 'Transaction successfully post',
                                                                                         showConfirmButton: true                                           
                                                                                    })                                                 
                                                                 }     

                                                           });
                                             }  
                                       });


                                



                               
                         }
                    });
     }

      
}






function view_billing_details(batch_id)
{
    $("#billing_modal").modal('show'); 

    $.ajax({
                type:'POST',
                url:'<?php echo base_url()?>Cuponing_ctrl/view_billing_details',
                data:{
                        'batch_id':batch_id
                     },
                dataType:'JSON',
                success: function(data)
                {
                    $("#billing-body").html(data.html);
                }     
           });
}

<?php
    if(in_array($_SESSION['access_type'],array('pharma-admin'))){ 
?>
    
var promoTable = $('#promo-table').DataTable({ "ordering": false });
var userTable = $('#user-table').DataTable({ "ordering": false });
feather.replace();


<?php
    if(!isset($_GET["user"])){ 
?>
    listPromoItems();

<?php }else{ ?>

    listUsers();

<?php } ?>  


function listPromoItems(){
    var program_sel = $("#program_select").val();
    
    $.ajax({
                type:'POST',
                url:'<?php echo base_url()?>Cuponing_ctrl/getPromoItems',
                data:{ program_id:program_sel},
                success: function(data)
                {
                    var jObj = JSON.parse(data);
                    promoTable.clear().draw();

                    for(var c=0; c<jObj.length; c++){
                        var id = jObj[c].item_id;
                        var code = jObj[c].item_code;
                        var uom = jObj[c].uom;
                        var desc = jObj[c].description;
                        var del_btn = '<button class="btn btn-danger" onclick="deletePromoItem('+id+')">REMOVE</button>';

                        var rowNode = promoTable.row.add([code,uom,desc,del_btn]).draw().node();
                        $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'}); 
                    }

                }     
           });
}


function savePromoItem(){
    var program_sel = $("#program_select").val();
    var item_code = $("#item_code_tf").val();
    var uom = $("#uom_sel").val();

    $.ajax({
                type:'POST',
                url:'<?php echo base_url()?>Cuponing_ctrl/savePromoItem',
                data:{ program_id:program_sel, item_code:item_code, uom:uom },
                success: function(data)
                {
                    var jObj = JSON.parse(data);
                    Swal.fire({position: 'center', icon: jObj[0], title: jObj[1], showConfirmButton: true});
                    if(jObj[0]=='success'){
                        listPromoItems();
                        $("#item_code_tf").val("");                 
                    }

                }     
           });
}

function deletePromoItem(item_id){
    Swal.fire({
              title: 'Are you sure',
              text: "You want to remove this item from Promo?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes'
        }).then((result) => {                        
            if (result.isConfirmed) {
                
                var program_sel = $("#program_select").val();

                $.ajax({
                            type:'POST',
                            url:'<?php echo base_url()?>Cuponing_ctrl/deletePromoItem',
                            data:{ program_id:program_sel, item_id:item_id },
                            success: function(data){
                                var jObj = JSON.parse(data);
                                
                                Swal.fire({position: 'center', icon: jObj[0], title: jObj[1], showConfirmButton: true});
                                listPromoItems(); 
                                            

                            }     
                       });
         }
        });   

}


function listUsers(){
    $.ajax({
                type:'POST',
                url:'<?php echo base_url()?>Cuponing_ctrl/getUserList',
                success: function(data)
                {
                    var jObj = JSON.parse(data);
                    userTable.clear().draw();

                    for(var c=0; c<jObj.length; c++){
                        var id = jObj[c].user_id;
                        var fn = jObj[c].firstname;
                        var ln = jObj[c].lastname;
                        var user = jObj[c].username;
                        var store = jObj[c].store;
                        var access = jObj[c].access;
                        var update_btn = '<button class="btn btn-warning" onclick="updateUserModal('+id+')">UPDATE</button>';

                        var rowNode = userTable.row.add([fn,ln,user,store,access,update_btn]).draw().node();
                        $(rowNode).find('td').css({'color': 'blue', 'font-family': 'sans-serif','text-align': 'center'}); 
                    }

                }     
           });
}


function closeUserModal(){
    $("#userModal").modal('hide'); 
}

function addUserModal(){
    $("#user_fname").val("");
    $("#user_lname").val("");
    $("#user_uname").val("");
    $("#userModalTitle").html("ADD USER FORM");
    $("#userModalBtn").html("ADD");
    $("#user_access_block").show();
    $("#user_store_block").show();
    $("#user_password_block").hide();
    $("#user_new_password_block").hide();
    $("#user_con_password_block").hide();
    $("#userModal").modal('show');
    setStoreSelect();

}

function setStoreSelect(){
    var access = $("#user_access").val();
    $("#user_store").prop("disabled",(access=="accounting" || access=="mpdi"));
}

function userModalBtnFunc(){
    var btn = $("#userModalBtn").html();
    if(btn=="ADD")
        addCouponUser();
    else
        updateCouponUser();
}

function addCouponUser(){
    var firstname = $("#user_fname").val();
    var lastname = $("#user_lname").val();
    var username = $("#user_uname").val();
    var access = $("#user_access").val();
    var db_id = 0;
    if(!$("#user_store").prop("disabled"))
        db_id = $("#user_store").val();

    console.log(firstname+" "+lastname+" "+username+" "+access+" "+db_id);

    $.ajax({
                type:'POST',
                url:'<?php echo base_url()?>Cuponing_ctrl/addCouponUser',
                data: { firstname:firstname, lastname:lastname, username:username, access:access, db_id:db_id },
                success: function(data)
                {
                    var jObj = JSON.parse(data);
                    Swal.fire({position: 'center', icon: jObj[0], title: jObj[1], showConfirmButton: true});
                    if(jObj[0]=='success'){
                        listUsers();
                        $("#user_fname").val("");
                        $("#user_lname").val("");
                        $("#user_uname").val("");
                    }

                }     
           });
}

function regex1(elem){ // Makes Text Field only accept Letters and Spaces
    $(elem).val($(elem).val().replace(/[^a-zA-Z\s]/g, ''));
}

function regex2(elem) { // Makes Text Field only accept Letters and Numbers
    $(elem).val($(elem).val().replace(/[^a-zA-Z0-9]/g, ''));
}

var selected_user_id = 0;

function updateUserModal(id){
    $.ajax({
                type:'POST',
                url:'<?php echo base_url()?>Cuponing_ctrl/getCouponUser',
                data: { id:id },
                success: function(data)
                {
                    var jObj = JSON.parse(data);
                    selected_user_id = id;
                    $("#user_fname").val(jObj.firstname);
                    $("#user_lname").val(jObj.lastname);
                    $("#user_uname").val(jObj.username);
                    $("#user_password").val("");
                    $("#user_new_password").val("");
                    $("#user_con_password").val("");
                    $("#userModalTitle").html("UPDATE USER FORM");
                    $("#userModalBtn").html("UPDATE");
                    $("#user_access_block").hide();
                    $("#user_store_block").hide();
                    $("#user_password_block").show();
                    $("#user_new_password_block").show();
                    $("#user_con_password_block").show();
                    $("#userModal").modal('show');
                    
                }     
           });
}

function updateCouponUser(){
    var firstname = $("#user_fname").val();
    var lastname = $("#user_lname").val();
    var username = $("#user_uname").val();
    var password = $("#user_password").val();
    var new_password = $("#user_new_password").val();
    var con_password = $("#user_con_password").val();
    
    console.log(selected_user_id+" "+firstname+" "+lastname+" "+username+" "+password+" "+new_password+" "+con_password);

    $.ajax({
                type:'POST',
                url:'<?php echo base_url()?>Cuponing_ctrl/updateCouponUser',
                data: { id:selected_user_id, firstname:firstname, lastname:lastname, username:username, password:password, new_password:new_password, con_password:con_password },
                success: function(data)
                {
                    var jObj = JSON.parse(data);
                    Swal.fire({position: 'center', icon: jObj[0], title: jObj[1], showConfirmButton: true});
                    if(jObj[0]=='success'){
                        listUsers();
                        $("#user_password").val("");
                        $("#user_new_password").val("");
                        $("#user_con_password").val("");
                    }

                }     
           });
}

<?php } ?>



function load_promo_list()
{
     loader_();
     $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>Cuponing_ctrl/load_promo_list',
                data:{
                        'brand_select':$("#brand_select").val()
                     },
                dataType:'JSON',
                success: function(data)
                {
                    $("#table_div").html(data.html);
                    swal.close();
                }     
            });
}


 function promo_modal(promo_id,promo_name)
 {
     loader_();
     $(".custom-width-modal").css("width", "1000px");
     $(".modal-body").css("height", "459px"); 
     $("#promo_modal").modal("show");
     $("#promo_title").html(promo_name);
     $.ajax({
                  type:'POST',
                  url:'<?php echo base_url(); ?>Cuponing_ctrl/promo_modal',
                  data:{
                            'promo_id':promo_id
                       },
                  dataType:'JSON',
                  success: function(data)
                  {
                     $("#billing-body").html(data.html);
                     $(".modal-footer").html(data.footer); 
                     swal.close();
                  }      
            });
 }



 function display_brand_table()
 {    
      loader_();
      $.ajax({
                 type:'POST',
                 url:'<?php echo base_url(); ?>Cuponing_ctrl/display_brand_table',
                 dataType:'JSON',
                 success: function(data)
                 {
                     $("#table_div").html(data.html);
                     swal.close();
                 }
            });
 }   

 
 function revert_color(id)
 {
    $('#'+id).css('border-color', '');
 }

 function red_color(store)
 {
      $('#'+store).css('border-color', 'red');
 }

</script>