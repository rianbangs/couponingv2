<?php $this->load->view("cupon/Plugin"); // Header ?>
<body>
<div id="app"> <!-- app -->
    <div id="sidebar" class='active'> <!-- sidebar -->
        <div class="sidebar-wrapper active"> <!-- sidebar-wrapper -->
            <div class="sidebar-header">
                <div style="width: 210px; height: 110px; overflow: hidden;  position: relative;">
                    <img src="<?php echo base_url(); ?>assets/cuponing/images/logo-mp.png" style="height: 9.5rem; position: absolute; top: -20%; bottom: -20%; margin: auto;">
                </div>
                <hr>
                <div style="width: 210px; height: 110px; overflow: hidden;">            
                    <img src="<?php echo base_url().$image_logo_path; ?>" style="height: 7.5rem;">
                </div>
            </div>

            <div class="sidebar-menu">
                <ul class="menu">
                    <li class='sidebar-title'>   
                        <?php echo strtoupper($_SESSION['access_type']); ?>    
                    </li>
                
                <?php
                    if($_SESSION['access_type'] == 'ordering'){  
                ?>    
                        <li class="sidebar-item">
                            <a href="<?php echo base_url();?>Cuponing_ctrl/cupon_ui" class='sidebar-link'>
                                <i data-feather="file-plus" width="20"></i> 
                                <span>Patient Compliance</span>
                            </a>
                        </li>

                        <li class="sidebar-item active">
                            <a href="<?php echo base_url();?>Dummy_ctrl/singleReceipt_ui" class='sidebar-link'>
                                <i data-feather="file-text" width="20"></i> 
                                <span>Health+</span>
                            </a>
                        </li> 


                <?php } ?>

                <?php
                    if(in_array($_SESSION['access_type'],array('accounting','liquidation','IAD'))){  
                ?>
                        <li class="sidebar-item">
                            <a href="<?php echo base_url();?>Cuponing_ctrl/cupon_ui" class='sidebar-link'>
                                <i data-feather="file-plus" width="20"></i> 
                                <span>Patient Compliance</span>
                            </a>
                        </li>

                        <li class="sidebar-item active has-sub">
                            <a href="#" class='sidebar-link'>
                                <i data-feather="file-text" width="20"></i> 
                                <span>Health+</span>
                            </a> 

                            <ul class="submenu ">                                          
                                <li class="li-item">
                                    <a onclick="monit_disc_items()"  style="cursor: pointer">                               
                                        Monitoring List for Discounted Items
                                    </a>                            
                                </li>  
                                <li class="li-item">
                                    <a onclick="EOD_report()"  style="cursor: pointer">                               
                                        End of Day (EOD) Liquidation Report
                                    </a>                            
                                </li>
                                <?php
                                    if(in_array($_SESSION['access_type'],array('accounting','IAD'))){  
                                ?>  
                                    <li class="li-item">
                                        <a onclick="EOM_report()"  style="cursor: pointer">                               
                                            End of Month (EOM) Liquidation Report
                                        </a>                            
                                    </li>

                                <?php } ?>

                                <li class="li-item">
                                    <a onclick="variance_report()"  style="cursor: pointer">                               
                                        Variance Report (Inhouse vs. Navision)
                                    </a>                            
                                </li>
                                <?php
                                    if($_SESSION['access_type'] == 'accounting'){  
                                ?>
                                    <li class="li-item">
                                        <a onclick="billing()"  style="cursor: pointer">                               
                                            Billing
                                        </a>                            
                                    </li> 

                                <?php } ?>

                            </ul>                     
                        </li> 

                <?php } ?>

                <?php
                    if($_SESSION['access_type'] == 'mpdi'){  
                ?>
                        <li class="sidebar-item">
                            <a href="<?php echo base_url();?>Cuponing_ctrl/cupon_ui" class='sidebar-link'>
                                <i data-feather="file-plus" width="20"></i> 
                                <span>Patient Compliance</span>
                            </a>
                        </li>

                        <li class="sidebar-item active has-sub">
                            <a href="#" class='sidebar-link'>
                                <i data-feather="file-text" width="20"></i> 
                                <span>Health+</span>
                            </a> 

                            <ul class="submenu ">                                          
                                
                                <li class="li-item">
                                    <a onclick="mpdi_EOM_report()"  style="cursor: pointer">                               
                                        End of Month (EOM) Liquidation Report
                                    </a>                            
                                </li>  
                                
                                <li class="li-item">
                                    <a onclick="mpdi_billing()"  style="cursor: pointer">                               
                                        Billing
                                    </a>                            
                                </li>                         
                            </ul>                     
                        </li> 

                <?php } ?>

                </ul>
            </div>
            <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
        </div> <!-- sidebar-wrapper -->
    </div> <!-- sidebar -->


    <div id="main" style="background-image: url(<?php echo base_url("assets/cuponing/images/background/auth.jpg");?>)">
        <nav class="navbar navbar-header navbar-expand navbar-light">
            <a class="sidebar-toggler" href="#"><span class="navbar-toggler-icon"></span></a>
            
            <button class="btn navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav d-flex align-items-center navbar-light ml-auto">
                    <li class="dropdown">
                        <a style="color: black;" href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <div class="avatar mr-1">
                                <i data-feather="user-check"></i>
                                <!-- <img src="<?php echo base_url(); ?>assets/cuponing/images/avatar/avatar-s-1.png" alt="" srcset=""> -->
                            </div>
                            <div class="d-none d-md-block d-lg-inline-block">Hello <?php echo $fname ?> !</div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <button id="pass_modal_btn" class="dropdown-item" data-toggle="modal" data-target="#accModal"><i data-feather="lock"></i> Account</button>
                                
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo base_url(); ?>Cuponing_ctrl/logout"><i data-feather="log-out"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main-content container-fluid">
            <section class="section">
                <div class="row mb-2">
                    <div id="body_div">
                        <?php
                            if($_SESSION['access_type'] == 'ordering'){
                                $this->load->view("single_receipt/ordering");
                            }  
                        ?>

                    </div>
                </div>
            </section>

            <footer style="position: absolute; bottom: 0; color: black;">
                2023 &copy; DCMS - Digital Couponing Monitoring System
                    <span style="color: red;">
                        <i data-feather="heart"></i>
                    </span> by IT SYSDEV
            </footer>
        </div>

    </div> <!-- main -->
</div> <!-- app -->

<!-- Account Modal -->
<div class="modal fade" id="accModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">ACCOUNT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <center>
                    <div class="col-5">  
                        <div class="form-group has-icon-left">
                            <div class="position-relative">
                                <input id="acc_pass" type="password" class="form-control" placeholder="Old Password" autocomplete="off">
                                <div class="form-control-icon">
                                    <i data-feather="lock"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-5">  
                        <div class="form-group has-icon-left">
                            <div class="position-relative">
                                <input id="new_acc_pass" type="password" class="form-control" placeholder="New Password" autocomplete="off">
                                <div class="form-control-icon">
                                    <i data-feather="lock"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-5">  
                        <div class="form-group has-icon-left">
                            <div class="position-relative">
                                <input id="con_acc_pass" type="password" class="form-control" placeholder="Confirm Password" autocomplete="off">
                                <div class="form-control-icon">
                                    <i data-feather="lock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </center>
            </div>

            <div class="modal-footer">
                <button id="save_pass_btn" type="button" class="btn btn-primary">Save</button>
            </div>

        </div>
    </div>
</div>

<!-- PDF Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="background-color: #323639;">
            
            <div class="modal-body">
                <center>
                    <iframe id="pdfFrame" height="700px" width="900px" style="border-radius: 10px;"></iframe>
                </center>
            </div>
        
        </div>
    </div>
</div>

</body>

<?php $this->load->view("single_receipt/javascripts"); ?>

<script>
    
    // Update and Save Password
    $(function() {
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
    });

    // Get all "li" elements with class "li-item"
    const liElements = document.querySelectorAll(".li-item");

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


    function monit_disc_items(){             
        loader_swal();
        $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/discount_ui',
                type:'POST',                                  
                success: function(response) {
                    Swal.close();
                    $('#body_div').html(response);    
              }    
            });
    }

    function EOD_report(){
        loader_swal(); 
        $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/eod_ui',
                type:'POST',                                  
                success: function(response) {
                    Swal.close();
                    $('#body_div').html(response);    
              }    
            });
    }

    function EOM_report(){
        loader_swal();           
         $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/eom_ui',
                type:'POST',                                  
                success: function(response) {
                    Swal.close();
                    $('#body_div').html(response);    
              }    
            });
    }

    function variance_report(){
        loader_swal();           
         $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/variance_ui',
                type:'POST',                                  
                success: function(response) {
                    Swal.close();
                    $('#body_div').html(response);    
              }    
            });
    }

    function billing(){
        loader_swal();           
         $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/billing_ui',
                type:'POST',                                  
                success: function(response) {
                    Swal.close();
                    $('#body_div').html(response);    
              }    
            });
    }

    function mpdi_EOM_report(){
        loader_swal();           
         $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/mpdi_eom_ui',
                type:'POST',                                  
                success: function(response) {
                    Swal.close();
                    $('#body_div').html(response);    
              }    
            });
    }

    function mpdi_billing(){
        loader_swal();           
         $.ajax({
                url:'<?php echo base_url();?>Dummy_ctrl/mpdi_billing_ui',
                type:'POST',                                  
                success: function(response) {
                    Swal.close();
                    $('#body_div').html(response);    
              }    
            });
    }

        
    // Swal
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
    <?php
        if($_SESSION['access_type'] == 'ordering'){
            $this->load->view("single_receipt/ordering_js");
        }

        if(in_array($_SESSION['access_type'],array('accounting','liquidation','IAD','mpdi'))){   
            $this->load->view("single_receipt/reports_js");
        }

        $this->load->view('cupon/Session_checker');
    ?>
</html> 
