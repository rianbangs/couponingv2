<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS</title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cuponing/css/bootstrap.css">
    
    <!-- <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/cuponing/images/favicon.svg" type="image/x-icon"> -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/cuponing/images/logo-mp.png" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cuponing/css/app.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/sweetalert.css">
    

</head>

<body>
    <div id="auth">
        
<div class="container">
    <div class="row">
        <div class="col-md-5 col-sm-12 mx-auto">
            <div class="card pt-4">
                <div class="card-body">
                    <div class="text-center">
                        <!-- <img src="<?php echo base_url(); ?>assets/cuponing/images/favicon.svg" height="48" class='mb-4'> -->
                        <center>
                        <div style="width: 210px; height: 110px; overflow: hidden;">
                            <img src="<?php echo base_url(); ?>assets/cuponing/images/logo-mp.png" style="height: 9.5rem;">
                        </div>
                        </center>
                        <h3>Digital Couponing Monitoring System</h3>
                        <p></p>
                    </div>
                    <!-- <form action="index.html"> -->
                        <div class="form-group position-relative has-icon-left">
                            <label for="username">Username</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="username">
                                <div class="form-control-icon">
                                    <i data-feather="user"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left">
                            <div class="clearfix">
                                <label for="password">Password</label>
                               <!--  <a href="auth-forgot-password.html" class='float-right'>
                                    <small>Forgot password?</small>
                                </a> -->
                            </div>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="password">
                                <div class="form-control-icon">
                                    <i data-feather="lock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">                                    
                                    <div class="form-group">
                                        <select id="brand_id" class="choices form-select">
                                                <option  value="" disabled selected>Select Brand</option>
                                         <?php
                                                foreach($brand_list as $brand)
                                                {                                                    
                                                     echo  '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';                                           
                                                }
                                          ?>  
                                        </select>
                                    </div>
                        </div>

                        <!-- <div class='form-check clearfix my-4'>
                            <div class="checkbox float-left">
                                <input type="checkbox" id="checkbox1" class='form-check-input' >
                                <label for="checkbox1">Remember me</label>
                            </div>
                            <div class="float-right">
                                <a href="auth-register.html">Don't have an account?</a>
                            </div>
                        </div> -->
                        <div class="clearfix">
                            <button class="btn btn-primary float-right" onclick="validate_login()">SIGN IN</button>
                        </div>
                    <!-- </form> -->
                    <!-- <div class="divider">
                        <div class="divider-text">OR</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <button class="btn btn-block mb-2 btn-primary"><i data-feather="facebook"></i> Facebook</button>
                        </div>
                        <div class="col-sm-6">
                            <button class="btn btn-block mb-2 btn-secondary"><i data-feather="github"></i> Github</button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
    <script src="<?php echo base_url(); ?>assets/cuponing/js/feather-icons/feather.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cuponing/js/app.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cuponing/js/main.js"></script>
    <script src="<?php echo base_url(); ?>assets/cuponing/js/jquery-3.6.0.min.js"></script>  
    
    <script>

        function validate_login()
        {
              var brand_id = $("#brand_id").val();  
              
              if(brand_id == null)
              {


                  $.ajax({
                                type:'POST',
                                url:'<?php echo base_url(); ?>Cuponing_ctrl/validate_login',
                                data:{
                                        'username':$("#username").val(),
                                        'password':$("#password").val(),
                                        'brand_id':brand_id
                                     },
                                dataType:'JSON',
                                success: function(data)
                                {   
                                     if(data.response == 'Password is pharma-admin')
                                     {
                                         window.location.href = data.redirect;
                                     }
                                     else 
                                     {
                                         swal_display('error','opps','Please select a promo');                                         
                                     }
                                }     
                             }); 
              }
              else 
              {                

                      $.ajax({
                                type:'POST',
                                url:'<?php echo base_url(); ?>Cuponing_ctrl/validate_login',
                                data:{
                                        'username':$("#username").val(),
                                        'password':$("#password").val(),
                                        'brand_id':brand_id
                                     },
                                dataType:'JSON',
                                success: function(data)
                                {   
                                     if(['Password is valid!', 'Password is pharma-admin'].includes(data.response)) 
                                     {
                                          window.location.href = data.redirect;
                                     }
                                     else
                                     {
                                          swal_display('error', 'opps', data.response);
                                     }

                                }     
                             });        
              }
        }


        

         window.onload = function()
         {
            document.getElementById("username").focus();
         }


         var promoCodeInput = document.getElementById("password");
         var timer;
         promoCodeInput.addEventListener("input", function() 
         {
                clearTimeout(timer); // reset the timer on input
                timer = setTimeout(function() 
                {
                       var brand_id = $("#brand_id").val(); 
                       $.ajax({
                                type:'POST',
                                url:'<?php echo base_url(); ?>Cuponing_ctrl/validate_login',
                                data:{
                                        'username':$("#username").val(),
                                        'password':$("#password").val(),
                                        'brand_id':brand_id
                                     },
                                dataType:'JSON',
                                success: function(data)
                                {   
                                     if(data.response == 'Password is pharma-admin')
                                     {
                                          $("#brand_id").hide();   
                                     }     
                                     else 
                                     {
                                          $("#brand_id").show();      
                                     }                                
                                }     
                             });        
                }, 100); // set a delay of 1 seconds  

         });  

             
        function swal_display(icon,title,text)
        {
             Swal.fire({
                             icon: icon,
                             title:title,
                             text: text                                  
                         });    
        }


        $('#password').on('keydown', function(e) 
        { 
            var keycode = e.keyCode || e.which;
            if (keycode === 13)
            {
                validate_login();
            }  
        });


        $('#brand_id').on('keydown', function(e) 
        { 
            var keycode = e.keyCode || e.which;
            if (keycode === 13)
            {
                validate_login();
            }  
        });





    </script>

</body>

</html>
