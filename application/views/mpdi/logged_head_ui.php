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
        <div class="row">
            <div>
                <div class="panel">
                    <nav class="navbar navbar-inverse">
                    <div class="container-fluid">
                        <div class="navbar-header">
                          <a class="navbar-brand" style="color: #009900;padding-left: 68px;" href="<?php echo base_url('Mpdi_log_ctrl/index');?>">
                                <img src="<?php echo base_url();?>assets/img/ebm_icon.png">
                                MARCELA PHARMA DISTRIBUTION INC</a>
                        </div>    
                        
                </div></nav>
                    
                </div>
            </div>
        </div>