<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Construction</title>
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
      <!--   <link rel="shortcut icon" type="image/png" href="http://172.16.161.100/EBS/other_deduction/assets/img/ebm_icon.png"/>
        <link rel="bookmark" href="favicon_16.ico"/>
        <link href="http://172.16.161.100/EBS/other_deduction/assets/css/site.min.css" rel="stylesheet"/> -->
        <link href="<?php echo base_url(); ?>assets/sync_plugin/css/datatables.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/sync_plugin/css/googleapis.css" rel="stylesheet" type="text/css"/>
        <link rel="<?php echo base_url();  ?>assets/sync_plugin/css/sweetalert.css">


<!--imported -->

        <!-- <link rel="shortcut icon" type="image/png" href="../assets/sync_plugin/img/latest.png"> -->
        <link href="<?php echo base_url(); ?>assets/sync_plugin/css/site.min.css" rel="stylesheet"/>
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/css/font-awesome.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/css/bootstrap-dialog.css">
        </script><link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/css/custom.css" ?v2="" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/css/bootstrap-dialog.css">
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/css/bootstrap-datetimepicker.css?ts=<?=time()?>&quot;" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/plugins/icheck-1.x/skins/square/blue.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/css/dormcss.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/plugins/icheck-1.x/skins/square/blue.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/jquery-ui/jquery-ui.css">
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/alert/css/alert.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/alert/themes/default/theme.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/css/extendedcss.css?ts=<?=time()?>&quot;" rel="stylesheet">
        <!-- <link href="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/dataTables/jquery.dataTables.min.css?ts=<?=time()?>&quot;" rel="stylesheet"> -->
        <script src="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/jquery-1.10.2.js?2"></script>
        <script src="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/bootstrap.min.js?2"></script>
        <script src="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/bootstrap-dialog.js?2"></script>

        <script src="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/jquery.metisMenu.js?2"></script>
        <script src="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/dataTables/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
        <script src="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/dataTablesDontDelete/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
        <script src="<?php echo base_url(); ?>assets/sync_plugin/progress_bar/js/ebsdeduction_function.js?<?php echo time()?>"></script>
<!-- end of imported -->
    </head>
    <body>

<style type="text/css">
    #on_icon_menu{color:#666666;height:35px;padding-top:7px;border-radius:3px 3px 1px 1px;font-size:13px;}
</style>

<div class="col-md-12" style="margin-top:0%;padding:3px;">

    <div class="col-md-12 miNS" style="min-height: 550px;border-bottom:1px solid #ddd;">
    <!-- ==================================================================================== -->

    <div class="col-md-12">
        <div class="col-md-12 pdd_1"></div>         
        <button   class="back_button btn btn-danger" onclick='back_to_posting()'  style='display:none;'>back to ebs</button> <div class="col-md-6 col-md-offset-3" style="padding: 10% 0%;">
                <div class="row" style="padding-left: 18px;">                    
                   <label class="col-md-12 pdd" style="margin:0px">
                        <img src="<?php echo base_url();?>assets/icon_index/upload_im.PNG" width="30">
                        UPLOADING FILE CONTENT
                        &nbsp;&nbsp;<img src="<?php echo base_url();?>assets/img/giphy.gif" height="20">
                    </label>
                    <span class="col-md-12 pdd fnt13 filenum">Completed file: 0 csv file(s)</span>
                    <span class="col-md-7 pdd fnt13 status">Status: 0% Complete </span>
                    <!-- <span class="col-md-4 pdd fnt13 toright">Processed Row:</span> -->
                    <span class="col-md-4 pdd fnt13 toright rowprocess"> 0</span>
                </div>
                <div class="progress row" style="height: 26px;margin:0px; padding:2px;"> 
                    <div id="percontent" class="progress-bar progress-bar-pimary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                    </div>
                </div>
                <span class="col-md-12 pdd fnt13 empname" >Employee: </span>
                <span class="col-md-12 pdd fnt13 filename"></span>
          </div>
     </div>
 <?php
    /* $this->load->library('session');
     $this->load->model('Simplify');
     $this->load->model('simplify/pdf_simplify','pdf_');
     $this->load->model('Unilab_model');
     $this->load->model('Mpdi_mod');
     $this->load->model('Gc_mod');*/
     $memory_limit = ini_get('memory_limit');
     ini_set('memory_limit',-1);
     ini_set('max_execution_time', 0);

     $get_directory    = $this->Middleware_mod->get_po_directory();
     $table_id_counter = 0;
     foreach($get_directory as $get_dir) 
     {  
        //$dir = '\\\\172.16.161.38\\cfs_txt\\Testing\\'; // specify the directory path with the correct username and password pang test ni
        //$dir = '\\\\172.16.192.57\\cdc_txt\\PO\\'; // specify the directory path with the correct username and password
        //$username = 'public'; // replace with your actual username
        //$password = 'public'; // replace with your actual password
        

        $header   = array('status','column_1','column_2','column_3','column_4','column_5','column_6','column_7','column_8','column_9','column_10','column_11','line type','smgm');
        


        $dir      = $get_dir['directory'];
        $dir      = str_replace('\\\\','\\\\',$dir);
        $dir      = str_replace('\\','\\',$dir);
        $username = $get_dir['username'];
        $password = $get_dir['password'];

        // use the 'net use' command to map the network drive with the specified credentials
        system("net use {$dir} /user:{$username} {$password}");


            $total_files = 0;
            if($handle = opendir($dir. "\\")) 
            {
               while (($entry = readdir($handle)) !== false) 
               {
                    if (is_file($dir . "\\" . $entry) && strstr($entry,'SMGM')) 
                    {
                         $total_files +=1;   
                    }
               }
            }


            // use the 'opendir' function to open the directory
            if ($handle = opendir($dir."\\")) 
            {
               
              $rowproC     = 1;

              while (($entry = readdir($handle)) !== false) 
              {
                 // process the entry         
                 if (is_file($dir . "\\" . $entry) && strstr($entry,'SMGM')) 
                 {                  


                      echo '<script language="JavaScript">';
                      echo    '$("span.filenum").text("Completed file: '.$rowproC.' out of '.$total_files.' textfiles");';
                      echo '</script>'; 

                      $table_id  = str_replace('.','_',$entry);
                      $table_id  = preg_replace('/\s+/', '', $table_id);
                      $table_id .= "_".$table_id_counter;
                      echo $this->Simplify->populate_header_table($table_id,$header);
                                              
                      echo '                                   
                                    </tbody>
                                </table>
                                <br><br>
                             <script>
                                     $("#'.$table_id.'").dataTable({
                                                                        scrollY: 500,
                                                                        scrollX: true,
                                                                        "ordering": false //disable sorting
                                                                   });
                             </script>';  



                      //$percent_row = $p++;
                      //echo $percent_row.">0 &&".$total_files.">0<br>";
                      if($rowproC >0 && $total_files >0)
                      {                                    
                         $percent = intval($rowproC/$total_files * 100)."%";                    
                      }
                      else 
                      {
                         $percent = "100%";
                      }

                      // echo $entry . "<br>"; //file name
                      $fh = fopen($dir."\\".$entry,'r');
                      //$total_row = count(file($dir.$entry));  //total number of lines sa sud sa textfile

                      while ($line = fgets($fh)) 
                      {
                         if ( !(strstr($line, '[HEADER]') || strstr($line, '[LINES]'))) 
                         {
                             //echo $line."<br>";
                            $line     =  str_replace('"','',$line);
                            $line_exp = explode("|",$line); 

                            if(count($line_exp) == 7)
                            {
                                array_push($line_exp,"","","","","HEADER",$entry);
                            }
                            else
                            {
                                array_push($line_exp,"LINES",$entry);                        
                            }

                            $row        = '';
                            $row_values = array();
                            for($a=0;$a<count($line_exp);$a++)
                            {
                                if(strstr($line_exp[$a], '/'))
                                {
                                     $date_arr = explode("/",$line_exp[$a]);                                                  
                                     if(count($date_arr) == 3)
                                     {
                                         if(is_numeric($date_arr[2]) &&  is_numeric($date_arr[0]) && is_numeric($date_arr[1]))
                                         {
                                             $value = date('Y-m-d',strtotime(date( $date_arr[2]."-".$date_arr[0]."-".$date_arr[1] )));                                                           
                                         }
                                     }
                                     else 
                                     {
                                        $value = preg_replace('/[^ \w-]/', '', $line_exp[$a]);                                                
                                     }                            
                                }
                                else 
                                {
                                    $value   = $line_exp[$a]; 
                                } 

                                $value = trim($value); //kuhaon ang white space ug mga line brakes
                                array_push($row_values,$value);

                                $row .= "'".$value."',";
                            }                    


                            $check_data = $this->Middleware_mod->check_nav_smgm_po_textiles($row_values);

                            $row  = substr($row, 0, -1);                                        

                            if(empty($check_data))
                            {

                                 if(strstr($entry,'-PST'))
                                 {
                                    $status     = 'POSTED';
                                    $status_tbl = "'POSTED',";
                                    $font_color = 'blue';
                                 }
                                 else 
                                 {
                                    $status     = 'PENDING';
                                    $status_tbl = "'PENDING',";
                                    $font_color = 'red';
                                 }
                               
                                 array_unshift($row_values,$status);
                                 $this->Middleware_mod->insert_update_update_nav_smgm_po_textiles($row_values,'insert');
                            }
                            else 
                            {                 
                                 if(strstr($entry,'-PST'))
                                 {
                                    $font_color = 'blue';
                                    $status     = 'POSTED';
                                    $status_tbl = "'POSTED',";
                                    //$get_po =  $this->Middleware_mod->check_nav_smgm_po_textiles($row_values);
                                    array_unshift($row_values,$status);
                                    $this->Middleware_mod->insert_update_update_nav_smgm_po_textiles($row_values,'update');
                                    //$this->Middleware_mod->update_nav_smgm_po_textiles($row_values,$check_data[0]['po_id']);

                                 }
                                 else 
                                 {
                                    $font_color = 'red';
                                    $status     = 'PENDING';
                                    $status_tbl = "'PENDING',";
                                 }                         
                                 
                            }

                            $row        = $status_tbl.$row;

                            echo "
                                                         <script>
                                                                  var table = $('#".$table_id."').DataTable();
                                             
                                                                  var rowNode  =  table.row.add( [ ".$row." ] ).draw().node();

                                                                  $( rowNode )
                                                                   .find('td:eq(0)').css('color', '".$font_color."').css('font-size', '15px'); //1 row sa column

                                                                  //$( rowNode )                                                   
                                                                    //.find('td:eq(2)').css('color', 'blue').css('font-size', '5px'); //specific nga column
                                                         </script>";
                         }                     
                      }
                      $table_id_counter += 1;//para sa table id arun unique  ang id sa table name per datatable
                      echo '<script language="JavaScript">';
                      echo '$("span.filename").text("Text FIle Name - '.$entry.'");';
                      echo '$("div#percontent").css({"width":"'.$percent.'"});';
                      echo '$("span.status").text("Status: '.$percent.' Complete");';
                      echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                      echo '$("span.empname").text("Entry: ");';
                      echo '</script>';
                      str_repeat(' ',1024*64);
                      flush();
                      ob_flush();
                      usleep(100);              
                     // echo "<br>----------------------------------------------------<br>";
                 }   

              }
              //closedir($handle); // close the directory handle
            } 
            else
            {
                // handle the error
                echo "Failed to open directory: {$dir}\n";
            }

     }
    //echo "total files:".$file_count;


     ini_set('memory_limit',$memory_limit );
 ?>