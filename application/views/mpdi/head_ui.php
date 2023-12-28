<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Marcela Pharma Distribution Inc.</title>
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <link rel="shortcut icon" type="image/png" href="<?php echo base_url();?>assets/img/ebm_icon.png"/>
        <link rel="bookmark" href="favicon_16.ico"/>
        <link href="<?php echo base_url(); ?>assets/css/site.min.css" rel="stylesheet"/>
        <link href="<?php echo base_url(); ?>assets/css/datatables.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/css/googleapis.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/sweetalert.css">
        <script src="<?php echo base_url(); ?>assets/js/site.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/sweetalert.js"></script>         
        <script src="<?php echo base_url(); ?>assets/js/datatables.min.js"></script>
        <style type="text/css">
            .dropdown-menu {
                min-width: 0px!important; 
            }
            .dropdown-menu > li > a {
                padding: 3px 16px!important;
                background-color: #434A54!important;
            }

            .btn-custom {
                background-color: #FFF;
                color: #333;
                border-color: #adadad;
                border-radius: 25px;
            }
            .btn-custom:hover,
            .btn-custom:focus,
            .btn-custom:active    {
                background-color: #009900;
                color: #333;
                border-color: #adadad;
            }
        </style>
    </head>
    

<?php
    $controller = $this->uri->segment(2);
    $method = $this->uri->segment(1, '');
?>
    <body>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <!-- Modal -->
          <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-sm">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Update Form</h4>
                  

                </div>
                <form class="form-horizontal" id="updateUserForm">
                <div class="modal-body">
                
                  <div class="form-group">
                    <label class="control-label col-sm-5" for="user">Username:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="update_user" name="update_user" placeholder="<?php echo $active_nav[3]; ?>">
                     </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-5" for="oldpass">Old Password:</label>
                     <div class="col-sm-6">
                      <input type="password" class="form-control" id="oldpass" name="oldpass">
                     </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-5" for="newpass">New Password:</label>
                     <div class="col-sm-6">
                      <input type="password" class="form-control" id="newpass" name="newpass">
                     </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-5" for="conpass">Confirm Password:</label>
                     <div class="col-sm-6">
                      <input type="password" class="form-control" id="conpass" name="conpass">
                     </div>
                  </div>
                  
                

                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-custom">UPDATE</button>
                </div>
                </form>
              </div>
            </div>
          </div>

          <script>

            $(function() {
                $('#updateUserForm').submit(function(e){
                e.preventDefault();
                
                $.ajax({ 
                        type:'POST',
                        url:'<?php echo base_url('Mpdi_ctrl/updateAccount'); ?>',
                        data: $(this).serialize(),
                        success: function(data){
                          var res = JSON.parse(data);
                          swal("",res[0],res[1].toLowerCase()); 
                          
                          if(res[1]=='Success'){  
                            if($('#update_user').val()!="")
                              $('#update_user').attr('placeholder', $('#update_user').val());

                            $('#updateUserForm').trigger("reset");
                            
                          }
                          
                        }      
                   });   


                });
            });

            
          </script>

        <div class="row">
            <div>
                <div class="panel">
                    <nav class="navbar navbar-inverse">
                    <div class="container-fluid">
                        <div class="navbar-header">
                          <a class="navbar-brand" style="color: #009900;padding-left: 68px;" href="#">
                                <img src="<?php echo base_url();?>assets/img/ebm_icon.png">
                                MARCELA PHARMA DISTRIBUTION INC</a>
                        </div>    
                        <ul id="myTab1" class="nav navbar-nav navbar-right">
                            
                            <!--<li class="<?php echo $active_nav == 1 ? 'active' : '';?>"><a href="<?php echo base_url('Mpdi_ctrl/mpdi_ui')?>">Home</a></li>

                            <li class="<?php echo $active_nav == 2 ? 'active' : '';?>"><a href="<?php echo base_url('Mpdi_ctrl/usersPage')?>">Users</a></li>-->
                

                            <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <span class="glyphicon glyphicon-user"></span> 
                            <?php echo $active_nav[5].": ".$active_nav[1]." ".$active_nav[2]; ?></a>
                            <ul class="dropdown-menu">
                              <li><a href="#" data-toggle="modal" data-target="#myModal">Update</a></li> 
                              <li><a href="<?php echo base_url('Mpdi_ctrl/logout')?>">Logout</a></li>
                            </ul>
                          </li>

                        </ul>
                </div></nav>
                </div>
            </div>
        </div>
    