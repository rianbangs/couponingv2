<?php
header("content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=chillyfacts.com.xls");

$table          = '[PHARMA WHOLESALE TEST$Sales Invoice Line]';
$get_connection = $this->Mpdi_mod->get_connection();
         foreach($get_connection  as $con)
         {
              $username   = $con['username'];
              $password   = $con['password']; 
              $connection = $con['db_name'];
         }

 ?>
    hello<br>
    world<br>
 	<table border='1'>
 		<tr>
 			<th>
 				test
 			</th>	
 		</tr>
 		<tr>
 			<td>
 				testing
 			</td>
 		</tr>
 	</table>
 