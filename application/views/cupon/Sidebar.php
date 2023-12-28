<body>
    <div id="app">
        <div id="sidebar" class='active'>
            <div class="sidebar-wrapper active">
    <div class="sidebar-header">
        <!-- <img src="<?php echo base_url(); ?>assets/cuponing/images/logo.svg" alt="" srcset=""> -->
        <div style="width: 210px; height: 110px; overflow: hidden;  position: relative;">
            <img src="<?php echo base_url(); ?>assets/cuponing/images/logo-mp.png" style="height: 9.5rem; position: absolute; top: -20%; bottom: -20%; margin: auto;">
        </div>
        <hr>
        <?php if(isset($_SESSION['brand_id']))
              {
         ?>
                 <div style="width: 210px; height: 110px; overflow: hidden;">            
                     <img src="<?php echo base_url().$brand_details[0]['image_logo_path']; ?>" style="height: 7.5rem;">
                 </div>
        <?php } ?>         
    </div>
    <div class="sidebar-menu">
        <ul class="menu">
            
            
                <li class='sidebar-title'> <?php echo strtoupper($_SESSION['access_type']); ?>  </li>
            
                <?php
                    if($_SESSION['access_type'] == 'ordering'){  
                ?>    
                        <li class="sidebar-item active">
                            <a href="<?php echo base_url();?>Cuponing_ctrl/cupon_ui" class='sidebar-link'>
                                <i data-feather="file-plus" width="20"></i> 
                                <span>Patient Compliance</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="<?php echo base_url();?>Dummy_ctrl/singleReceipt_ui" class='sidebar-link'>
                                <i data-feather="file-text" width="20"></i> 
                                <span>Health+</span>
                            </a>
                        </li> 


                <?php } ?>
            
                           
                <?php
                      //if($_SESSION['access_type'] == 'accounting')
                      if(in_array($_SESSION['access_type'],array('accounting','liquidation','IAD') ) )
                      {  
                ?>
                            <li class="sidebar-item active has-sub">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="file-plus" width="20"></i> 
                                    <span>Patient Compliance</span>
                                </a>                    
                                <ul class="submenu ">    

                                    <li class="li-item">
                                        <a onclick="monit_disc_items()"  style="cursor: pointer">                               
                                            Monitoring List for Discounted Items
                                        </a>                            
                                    </li>  
                                    <li class="li-item">
                                        <a onclick="EOD_report()"  style="cursor: pointer">                               
                                            End of Day (EOD)  Report
                                        </a>                            
                                    </li>  
                                      <?php
                                            if(in_array($_SESSION['access_type'],array('accounting','IAD') ) )
                                            {  
                                       ?>
                                                <li class="li-item">
                                                    <a onclick="EOM_report()"  style="cursor: pointer">                               
                                                        End of Month (EOM)  Report
                                                    </a>                            
                                                </li>  
                                      <?php } ?>          

                                    <li class="li-item">
                                        <a onclick="variance_report()"  style="cursor: pointer">                               
                                            Variance Report (Inhouse vs. Navision)
                                        </a>                            
                                    </li>   
                                    <?php
                                            if(in_array($_SESSION['access_type'],array('accounting') ) )
                                            {  
                                       ?>
                                                    <li class="li-item">
                                                        <a onclick="billing_report()"  style="cursor: pointer">                               
                                                            Billing
                                                        </a>                            
                                                    </li>  
                                    <?php   } ?>                        
                                </ul>                     
                            </li>

                        <li class="sidebar-item">
                            <a href="<?php echo base_url();?>Dummy_ctrl/singleReceipt_ui" class='sidebar-link'>
                                <i data-feather="file-text" width="20"></i> 
                                <span>Health+</span>
                            </a>
                        </li>  
                <?php }
                      else 
                      if(in_array($_SESSION['access_type'],array('mpdi') ) )
                      { ?>  

                              <li class="sidebar-item active has-sub">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="file-plus" width="20"></i> 
                                    <span>Patient Compliance</span>
                                </a>                    
                                <ul class="submenu ">                                                                           
                                  
                                    <li class="li-item">
                                        <a onclick="EOM_report()"  style="cursor: pointer">                               
                                            End of Month (EOM) MPDI Report
                                        </a>                            
                                    </li>                                                       
                                    <li class="li-item">
                                        <a onclick="mpdi_billing_report()"  style="cursor: pointer">                               
                                            Billing
                                        </a>                            
                                    </li>                        
                                </ul>                     
                             </li>

                             <li class="sidebar-item">
                                     <a href="<?php echo base_url();?>Dummy_ctrl/singleReceipt_ui" class='sidebar-link'>
                                        <i data-feather="file-text" width="20"></i> 
                                        <span>Health+</span>
                                     </a>
                             </li>  
                <?php }
                      else 
                      if(in_array($_SESSION['access_type'],array('pharma-admin') ) )
                      {
                ?>     
                         <li class="sidebar-item active has-sub">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="file-plus" width="20"></i> 
                                    <span>Patient Compliance</span>
                                </a>                    
                                <ul class="submenu ">                                                                                                             
                                    <li class="li-item">
                                        <a onclick="brand_masterfile()"  style="cursor: pointer">                               
                                             Brand Masterfile
                                        </a>                            
                                    </li>                                                                                                                
                                    <li class="li-item">
                                        <a onclick="promo_masterfile()"  style="cursor: pointer">                               
                                             Promo Masterfile
                                        </a>                            
                                    </li>   
                                </ul>                     
                        </li>  
                        <li class="sidebar-item <?php echo (!isset($_GET["user"]))? "active":""; ?>">
                            <a href="<?php echo base_url();?>Cuponing_ctrl/cupon_ui" class='sidebar-link'>
                                <i data-feather="folder-plus" width="20"></i> 
                                <span>Promo List</span>
                            </a>
                        </li>

                        <li class="sidebar-item <?php echo (isset($_GET["user"]))? "active":""; ?>">
                            <a href="<?php echo base_url();?>Cuponing_ctrl/cupon_ui?user=0" class='sidebar-link'>
                                <i data-feather="users" width="20"></i> 
                                <span>User List</span>
                            </a>
                        </li> 
                        
                <?php }?>
            
            
               <!--  <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i data-feather="triangle" width="20"></i> 
                        <span>Components</span>
                    </a>
                    
                    <ul class="submenu ">
                        
                        <li>
                            <a href="component-alert.html">Alert</a>
                        </li>
                        
                        <li>
                            <a href="component-badge.html">Badge</a>
                        </li>
                        
                        <li>
                            <a href="component-breadcrumb.html">Breadcrumb</a>
                        </li>
                        
                        <li>
                            <a href="component-buttons.html">Buttons</a>
                        </li>
                        
                        <li>
                            <a href="component-card.html">Card</a>
                        </li>
                        
                        <li>
                            <a href="component-carousel.html">Carousel</a>
                        </li>
                        
                        <li>
                            <a href="component-dropdowns.html">Dropdowns</a>
                        </li>
                        
                        <li>
                            <a href="component-list-group.html">List Group</a>
                        </li>
                        
                        <li>
                            <a href="component-modal.html">Modal</a>
                        </li>
                        
                        <li>
                            <a href="component-navs.html">Navs</a>
                        </li>
                        
                        <li>
                            <a href="component-pagination.html">Pagination</a>
                        </li>
                        
                        <li>
                            <a href="component-progress.html">Progress</a>
                        </li>
                        
                        <li>
                            <a href="component-spinners.html">Spinners</a>
                        </li>
                        
                        <li>
                            <a href="component-tooltips.html">Tooltips</a>
                        </li>
                        
                    </ul>
                    
                </li> -->

            
            
            
              <!--   <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i data-feather="briefcase" width="20"></i> 
                        <span>Extra Components</span>
                    </a>
                    
                    <ul class="submenu ">
                        
                        <li>
                            <a href="component-extra-avatar.html">Avatar</a>
                        </li>
                        
                        <li>
                            <a href="component-extra-divider.html">Divider</a>
                        </li>
                        
                    </ul>
                    
                </li> -->

            
            
            
               <!--  <li class='sidebar-title'>Forms &amp; Tables</li>
            
            
            
                <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i data-feather="file-text" width="20"></i> 
                        <span>Form Elements</span>
                    </a>
                    
                    <ul class="submenu ">
                        
                        <li>
                            <a href="form-element-input.html">Input</a>
                        </li>
                        
                        <li>
                            <a href="form-element-input-group.html">Input Group</a>
                        </li>
                        
                        <li>
                            <a href="form-element-select.html">Select</a>
                        </li>
                        
                        <li>
                            <a href="form-element-radio.html">Radio</a>
                        </li>
                        
                        <li>
                            <a href="form-element-checkbox.html">Checkbox</a>
                        </li>
                        
                        <li>
                            <a href="form-element-textarea.html">Textarea</a>
                        </li>
                        
                    </ul>
                    
                </li> -->

            
            
            
                <!-- <li class="sidebar-item  ">
                    <a href="form-layout.html" class='sidebar-link'>
                        <i data-feather="layout" width="20"></i> 
                        <span>Form Layout</span>
                    </a>
                    
                </li> -->

            
            
            <!-- 
                <li class="sidebar-item  ">
                    <a href="form-editor.html" class='sidebar-link'>
                        <i data-feather="layers" width="20"></i> 
                        <span>Form Editor</span>
                    </a>
                    
                </li> -->

            
            
            
               <!--  <li class="sidebar-item  ">
                    <a href="table.html" class='sidebar-link'>
                        <i data-feather="grid" width="20"></i> 
                        <span>Table</span>
                    </a>
                    
                </li> -->

            
            
            
               <!--  <li class="sidebar-item  ">
                    <a href="table-datatable.html" class='sidebar-link'>
                        <i data-feather="file-plus" width="20"></i> 
                        <span>Datatable</span>
                    </a>
                    
                </li> -->

            
            
            
                <!-- <li class='sidebar-title'>Extra UI</li>
            
            
            
                <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i data-feather="user" width="20"></i> 
                        <span>Widgets</span>
                    </a>
                    
                    <ul class="submenu ">
                        
                        <li>
                            <a href="ui-chatbox.html">Chatbox</a>
                        </li>
                        
                        <li>
                            <a href="ui-pricing.html">Pricing</a>
                        </li>
                        
                        <li>
                            <a href="ui-todolist.html">To-do List</a>
                        </li>
                        
                    </ul>
                    
                </li> -->

            
            
            
             <!--    <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i data-feather="trending-up" width="20"></i> 
                        <span>Charts</span>
                    </a>
                    
                    <ul class="submenu ">
                        
                        <li>
                            <a href="ui-chart-chartjs.html">ChartJS</a>
                        </li>
                        
                        <li>
                            <a href="ui-chart-apexchart.html">Apexchart</a>
                        </li>
                        
                    </ul>
                    
                </li> -->

            
            
            
                <!-- <li class='sidebar-title'>Pages</li>
            
            
            
                <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i data-feather="user" width="20"></i> 
                        <span>Authentication</span>
                    </a>
                    
                    <ul class="submenu ">
                        
                        <li>
                            <a href="auth-login.html">Login</a>
                        </li>
                        
                        <li>
                            <a href="auth-register.html">Register</a>
                        </li>
                        
                        <li>
                            <a href="auth-forgot-password.html">Forgot Password</a>
                        </li>
                        
                    </ul>
                    
                </li> -->

            
            
            
              <!--   <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i data-feather="alert-circle" width="20"></i> 
                        <span>Errors</span>
                    </a>
                    
                    <ul class="submenu ">
                        
                        <li>
                            <a href="error-403.html">403</a>
                        </li>
                        
                        <li>
                            <a href="error-404.html">404</a>
                        </li>
                        
                        <li>
                            <a href="error-500.html">500</a>
                        </li>
                        
                    </ul>
                    
                </li> -->

            
            
         
        </ul>
    </div>
    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
</div>
        </div>