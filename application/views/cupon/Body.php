<div id="main"  style="background-image: url(<?php echo base_url(); ?>/assets/cuponing/images/background/auth.jpg)">
            <nav class="navbar navbar-header navbar-expand navbar-light">
                <a class="sidebar-toggler" href="#"><span class="navbar-toggler-icon"></span></a>
                <button class="btn navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav d-flex align-items-center navbar-light ml-auto">
                      <!--   <li class="dropdown nav-icon">
                            <a href="#" data-toggle="dropdown" class="nav-link  dropdown-toggle nav-link-lg nav-link-user">
                                <div class="d-lg-inline-block">
                                    <i data-feather="bell"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-large">
                                <h6 class='py-2 px-4'>Notifications</h6>
                                <ul class="list-group rounded-none">
                                    <li class="list-group-item border-0 align-items-start">
                                        <div class="avatar bg-success mr-3">
                                            <span class="avatar-content"><i data-feather="shopping-cart"></i></span>
                                        </div>
                                        <div>
                                            <h6 class='text-bold'>New Order</h6>
                                            <p class='text-xs'>
                                                An order made by Ahmad Saugi for product Samsung Galaxy S69
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li> -->
                      <!--   <li class="dropdown nav-icon mr-2">
                            <a href="#" data-toggle="dropdown" class="nav-link  dropdown-toggle nav-link-lg nav-link-user">
                                <div class="d-lg-inline-block">
                                    <i data-feather="mail"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"><i data-feather="user"></i> Account</a>
                                <a class="dropdown-item active" href="#"><i data-feather="mail"></i> Messages</a>
                                <a class="dropdown-item" href="#"><i data-feather="settings"></i> Settings</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i data-feather="log-out"></i> Logout</a>
                            </div>
                        </li> -->
                        <li class="dropdown">
                            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user" style="color:black;">
                                <div class="avatar mr-1">
                                    <i data-feather="user-check"></i>
                                    <!-- <img src="<?php echo base_url(); ?>assets/cuponing/images/avatar/avatar-s-1.png" alt="" srcset=""> -->
                                </div>
                                <div class="d-none d-md-block d-lg-inline-block">Hello <?php echo $fname ?> !</div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button id="pass_modal_btn" class="dropdown-item" data-toggle="modal" data-target="#accModal"><i data-feather="lock"></i> Account</button>
                                <!-- <a class="dropdown-item" href="#"><i data-feather="user"></i> Account</a> -->
                                <!-- <a class="dropdown-item active" href="#"><i data-feather="mail"></i> Messages</a>
                                <a class="dropdown-item" href="#"><i data-feather="settings"></i> Settings</a> -->
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo base_url(); ?>Cuponing_ctrl/logout"><i data-feather="log-out"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

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
                            <!-- <label for="first-name-icon"></label> -->
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
                            <!-- <label for="first-name-icon"></label> -->
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
                            <!-- <label for="first-name-icon"></label> -->
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
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <button id="save_pass_btn" type="button" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>


<div class="main-content container-fluid">
   <!--  <div class="page-title">
        <h3>Dashboard</h3>
        <p class="text-subtitle text-muted">A good dashboard to display your statistics</p>
    </div> -->
    <section class="section">
        <div class="row mb-2">
          
     
  