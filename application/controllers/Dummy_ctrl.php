<?php 

class Dummy_ctrl extends CI_Controller{	
	
     function __construct(){
        parent::__construct();
        $this->load->model("Cupon_mod");
        $this->load->model("Dummy_mod");
     }

     private function checkSession(){
          if(!isset($_SESSION['username'])){
               $url = site_url('Cuponing_ctrl/login_ui');
               redirect($url);
          }
     }

     // Views
     function singleReceipt_ui(){
          $this->checkSession();
          $user_details = $this->Cupon_mod->search_user($_SESSION['username']);
          $data['fname'] = $user_details[0]['fname'];

          $promo_list = $this->Cupon_mod->get_promo_list();
          foreach($promo_list as $promo){
              if($_SESSION['promo_id'] == $promo['promo_id'])  {
                  $data['promo_name'] = $promo['promo_name'];
                  $data['image_logo_path'] = $promo['image_logo_path'];
              }
          }

          $this->load->view('single_receipt/main',$data);
          
     }

     //Report Views
     function discount_ui(){
          $this->load->view("single_receipt/discount_monitoring");
     }

     function eod_ui(){
          $this->load->view("single_receipt/eod");
     }

     function eom_ui(){
          $this->load->view("single_receipt/eom");
     }

     function variance_ui(){
          $this->load->view("single_receipt/inhouse_navision");
     }

     function billing_ui(){
          $this->load->view("single_receipt/billing");
     }

     function mpdi_eom_ui(){
          $this->load->view("single_receipt/mpdi_eom");
     }

     function mpdi_billing_ui(){
          $this->load->view("single_receipt/mpdi_billing");
     }

     // Cashier 
     function searchOrderNo(){
          $order_no = $_POST['order_no']; 
          $data   = $this->Dummy_mod->retrieveOrderNo($order_no);
          echo json_encode($data);
     }

     function searchNameOrPhone(){
          $search = $_POST['search'];
          $ind = $_POST['index']; 
          $data = $this->Dummy_mod->searchNameOrPhone($search,$ind);
          echo json_encode($data);
     }

     private function formatAltaOrderNo($order_no){
          if($_SESSION['db_id'] == 19){ // If Alta Citta
               $pattern = '/^Counter [0-9]{2}-[0-9]{9}$/'; // Regular expression pattern

               if(preg_match($pattern, $order_no)){
                    $text = "Counter 04-000110243"; // Sample Raw Format
                    $counter1 = substr($order_no, strpos($order_no, '-') - 2, 2); // Extract the digits before the dash
                    $counter2 = substr($order_no, strpos($order_no, '-') + 1); // Extract the digits after the dash
                    
                    return "0000000P".$counter1.$counter2; 
               }else
                    return $order_no;

          }else
               return $order_no;
     }

     function getSingleReceipt(){
          if(isset($_POST['order_no'])){
               $order_no = $this->formatAltaOrderNo($_POST['order_no']);
               $data = $this->Dummy_mod->retrieveTakeOrder($order_no);
               $order_count = $this->Dummy_mod->countOrderNo($order_no);
               $sr_count = $this->Dummy_mod->getTransactionCountByOrderNo($order_no,1);

               if($sr_count>0){
                    $sr_id = $this->Dummy_mod->getSRIDByOrderNo($order_no);
                    echo json_encode(array("msg" => "Ordering Number Already Transacted!", "sr_id" => $sr_id));
               }else if($order_count<1){
                    echo "Ordering Number Not Found!";
               }else if(count($data["list"])<1){
                    echo "Ordering Number Does Not Contain Any Participating Item!";
               }else{
                    echo json_encode(array("msg" => "0", "data" => $data));
               }
               
          } 

     }

     function transactSingleReceipt(){
          if(isset($_POST['order_no']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['phone']) && isset($_POST['promo_code'])){

               $order_no = $this->formatAltaOrderNo($_POST['order_no']);
               $fname = trim($_POST['fname']);
               $lname = trim($_POST['lname']);
               $phone = trim($_POST['phone']);
               $birth_year = $_POST['birth_year'];
               $promo_code = $_POST['promo_code'];

               $data = $this->Dummy_mod->retrieveTakeOrder($order_no);
               $order_count = $this->Dummy_mod->countOrderNo($order_no);
               $sr_count = $this->Dummy_mod->getTransactionCountByOrderNo($order_no,1);
               $promo_count = $this->Dummy_mod->getPromoCodeCount($promo_code);

               if($sr_count>0){ // If already transacted
                    $sr_id = $this->Dummy_mod->getSRIDByOrderNo($order_no);
                    echo json_encode(array("msg" => "Ordering Number Already Transacted!", "sr_id" => $sr_id));
               }else if($order_no=="" || ctype_space($order_no)){ // Ordering Number
                    echo "Please Input Ordering Number!";
               }else if($order_count<1){
                    echo "Ordering Number Not Found!";
               }else if(count($data["list"])<1){
                    echo "Ordering Number Does Not Contain Any Participating Item!";
               }else if($fname=="" || ctype_space($fname)){ // First 
                    echo "Please Input First Name!";
               }else if(!preg_match("/^[a-zA-Z ]+$/",$fname)){ 
                    echo "First Name Must Only Contain Letters and Spaces!";
               }else if($lname=="" || ctype_space($lname)){ // Last Name
                    echo "Please Input Last Name!";
               }else if(!preg_match("/^[a-zA-Z ]+$/",$lname)){ 
                    echo "Last Name Must Only Contain Letters and Spaces!";
               }else if($phone=="" || ctype_space($phone)){ // Phone
                    echo "Please Input Phone Number!";
               }else if(!ctype_digit($phone)){
                    echo "Phone Number Must Only Contain Numbers!";
               }else if($phone[0]!='9'){
                    echo "Phone Number Must Start With 9!";
               }else if(strlen($phone)!=10){
                    echo "Invalid Phone Number Length!";
               }else if($promo_code=="" || ctype_space($promo_code)){ // Promo Code
                    echo "Please Input Promo Code!";
               }else if(!preg_match("/^[a-zA-Z0-9]+$/",$promo_code)){ 
                    echo "Promo Code Must Be Alphanumeric!";
               }else if(strlen($promo_code)<9 || strlen($promo_code)>11){ 
                    echo "Invalid Promo Code Length(9 - 11 Characters Only)!";
               }else if($promo_count>0){
                    $cust = $this->Dummy_mod->getCustomerByPromo($promo_code);
                    echo "Promo Code Already Used by ".$cust."!";
               } else { // Success

                    $discount_type = $data["compute"]["discount_type"];
                    $minimum = $data["compute"]["minimum"];
                    $maximum = $data["compute"]["maximum"];
                    $discount = $data["compute"]["discount"];
                    $entries = $data["list"];

                    $customer_id = 0;
                    $result_id = $this->Dummy_mod->getCustomerID($fname,$lname,$phone,$birth_year);
                    if(isset($result_id))
                         $customer_id = $result_id["customer_id"];
                    else
                         $customer_id = $this->Dummy_mod->addCustomer($fname,$lname,$phone,$birth_year); 

                    $sr_id = $this->Dummy_mod->insertItemTransactionSingle($order_no,$customer_id,$promo_code,$discount_type,$minimum,$maximum,$discount,$entries);
                    if($sr_id>0)
                         echo json_encode(array("msg" => "0", "sr_id" => $sr_id));
                    else
                         echo "Error Occurred!";
               }

               
          }
     }

     function generatePDFSingle($sr_id){
          $this->ppdf = new TCPDF('P', 'mm', array(80, 297), true, 'UTF-8', false);
          $this->ppdf->SetTitle("DCMS - Receipt");    
          $this->ppdf->SetMargins(5, 15,5, true);
          $this->ppdf->setPrintHeader(false);
          $this->ppdf->SetFont('', '', 10, '', true);
          $this->ppdf->AddPage();

          $this->ppdf->SetAutoPageBreak(true);

          $result = $this->Dummy_mod->retrieveItemTransactionSingle($sr_id);
          
          $order_no = $result["discount"]["order_no"];
          $customer = $result["discount"]["full_name"];
          $promo_code = $result["discount"]["promo_code"];
          $discount = $result["discount"]["discount"];
          $date_transact = $result["discount"]["date_transact"];
          
          $total_cost = number_format($result["discount"]["total_cost"],2);
          $total_qty =  $result["discount"]["total_qty"];
          $d_cost =  number_format($result["discount"]["d_cost"],2);
          
          $tbl = '<table cellspacing="1" cellpadding="3" style="font-size:8px;">
                       <tr>
                              <td colspan="4" align="center"><b>HEALTH PLUS</b></td>
                       </tr>
                       <tr>
                              <td colspan="4"><b>Ordering Number</b>: '.$order_no.'</td>
                       </tr>
                       <tr>
                              <td colspan="4"><b>Customer</b>: '.$customer.'</td>
                       </tr>
                       <tr>
                              <td colspan="4"><b>Date</b>: '.$date_transact.'</td>
                       </tr>
                       <tr>
                              <td colspan="4"><b>Promo Code</b>: '.$promo_code.'</td>
                       </tr>
                       <tr><td colspan="4">&nbsp;</td></tr>
                       <tr>
                              <td colspan="4"><b>Description</b></td>
                       </tr>
                       <tr>
                              <td><b>Item No.</b></td>
                              <td><b>UOM</b></td>
                              <td><b>Unit Price</b></td>
                              <td align="right"><b>Amount</b></td>
                       </tr>
                       <tr><td colspan="4">&nbsp;</td></tr>';

          foreach ($result["item_list"] as $rowData) {                   
 
               $itemCode = $rowData["item_code"];
               $itemName = $rowData["item_desc"];
               $quantity = $rowData["qty"];
               $uom = $rowData["uom"];
               $price  = number_format($rowData["price"],2);
               $amt = number_format(($rowData["price"]*$rowData["qty"]),2);

               $tbl .= '<tr>
                               <td colspan="4">'.$itemName.'</td>
                         </tr>
                         <tr>    
                               <td align="center">'.$itemCode.'</td>    
                               <td>'.$quantity.' '.$uom.'</td>
                               <td>'.$price.'</td> 
                               <td align="right">'.$amt.'</td>        
                         </tr>';

          }

          $tbl .= '<tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                         <td colspan="3"><b>Total Amount</b></td>
                         <td align="right">'.$total_cost.'</td>
                    </tr>
                    <tr>
                         <td><b>Discount</b></td>
                         <td colspan="2">'.$discount.'</td>
                         <td align="right">'.$d_cost.'</td>
                    </tr>
                    </table>';

          $this->ppdf->writeHTML($tbl, true, false, false, false, '');
          $this->ppdf->write1DBarcode($discount, 'C128', '', '', '', 12, 1.3, $style = '', 'N');
          
          $tbl = '<table cellspacing="1" cellpadding="3" style="font-size:8px;">
                    <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                    </tr>
                    <tr>
                         <td><b>Total No. of Items</b></td>
                         <td colspan="3">'.$total_qty.'</td>
                    </tr>
                    </table>';

          $this->ppdf->writeHTML($tbl, true, false, false, false, '');          
          $this->ppdf->write1DBarcode($promo_code, 'C128', '', '', '', 12, 1.3, $style = '', 'N');
          ob_end_clean();
          echo $this->ppdf->Output();
     }

     // Reports - Discount
     function retrieveListOfDiscountedItems(){
          if(isset($_POST['store_select']) && isset($_POST['start_date']) && isset($_POST['end_date'])){
               $store_id = $_POST['store_select'];
               $start = $_POST['start_date'];
               $end = $_POST['end_date'];
               $start_date = new DateTime($start);
               $end_date = new DateTime($end);

               if(($start!="" && !ctype_space($start)) && ($end!="" && !ctype_space($end))){
                    
                    if($end_date->format('Y-m-d') < $start_date->format('Y-m-d')){
                         echo "Start Date must be lesser than End Date!";
                    }else{
                         $listDiscount = $this->Dummy_mod->retrieveDiscountedItemsByDate($start,$end,$store_id);
                         if(count($listDiscount)>0)
                              echo json_encode($listDiscount);
                         else
                              echo "No Record Found!";
                    }
               }else
                    echo json_encode(array());
          }
     }


     function generatePDFSingleDiscount($start,$end,$store_id){
          $this->ppdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
          $this->ppdf->SetTitle("DCMS - Discount List");    
          $this->ppdf->SetMargins(5, 15,5, true);
          $this->ppdf->setPrintHeader(false);
          $this->ppdf->SetFont('', '', 10, '', true);
          $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

          $listDiscount = $this->Dummy_mod->retrieveDiscountedItemsByDate($start,$end,$store_id);
          $pages = "Page: ".$this->ppdf->getAliasNumPage().'/'.$this->ppdf->getAliasNbPages(); 
          $counter = 0;

          foreach($listDiscount as $key => $item){
               if($counter==0){
                    
                    $this->ppdf->AddPage();
                    
                    $tbl = '  <h1 align="center">'.$this->Dummy_mod->getDisplayStore($store_id).'</h1>
                              <h1 align="center">Monitoring List for Discounted Items</h1>
                              <h2 align="center">From '.date("F j, Y", strtotime($start)).' to '.date("F j, Y", strtotime($end)).'</h2>
                              <h4 align="center">'.$pages.'</h4>
                              <table align="center" cellspacing="1" border="1" cellpadding="3" style="font-size:9px;">
                                 <tr>
                                   <th width="10%"><b>TOTAL QUANTITY</b></th>
                                   <th><b>ITEM NO.</b></th>
                                   <th width="40%"><b>BRAND NAME</b></th>
                                   <th><b>GENERIC NAME</b></th>
                                   <th width="10%"><b>UOM</b></th>
                                 </tr>';
               }

               $tbl.= '<tr>
                         <td>'.$item["qty"].'</td>
                         <td>'.$item["item_code"].'</td>
                         <td>'.$item["brand"].'</td>
                         <td>'.$item["generic"].'</td>
                         <td>'.$item["uom"].'</td>
                       </tr>';

               if($counter==30){
                    $counter = 0;
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }else{
                    $counter++; 
               }

               if($key==count($listDiscount)-1){
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }
          }

          ob_end_clean();
          echo $this->ppdf->Output();
     }

     // Reports - EOD
     function retrieveSummaryEOD(){
          if(isset($_POST['store_select']) && isset($_POST['selected_date'])){
               $store_id = $_POST['store_select'];
               $selected = $_POST['selected_date'];

               if($selected!="" && !ctype_space($selected)){

                    $summary = $this->Dummy_mod->retrieveSummaryItemsByDate($selected,$store_id);
                    if(count($summary)>0)
                         echo json_encode($summary);
                    else
                         echo "No Record Found!";

               }else
                    echo json_encode(array());
          }
     }

     function generatePDFSummaryEOD($selected,$store_id){
          $this->ppdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
          $this->ppdf->SetTitle("DCMS - EOD");    
          $this->ppdf->SetMargins(5, 15,5, true);
          $this->ppdf->setPrintHeader(false);
          $this->ppdf->SetFont('', '', 10, '', true);
          $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

          $listDiscount = $this->Dummy_mod->retrieveSummaryItemsByDate($selected,$store_id); 
          $pages = "Page: ".$this->ppdf->getAliasNumPage().'/'.$this->ppdf->getAliasNbPages(); 
          $counter = 0;

          foreach($listDiscount as $key => $item){
               if($counter==0){
                    
                    $this->ppdf->AddPage("L");
                    
                    $tbl = '  <h1 align="center">End of Day (EOD) Liquidation Report</h1>
                              <h2 align="center">'.date("F j, Y", strtotime($selected)).'</h2>
                              <h4 align="center">'.$pages.'</h4>
                              <table align="center" cellspacing="1" border="1" cellpadding="3" style="font-size:9px;">
                                 <tr>
                                   <th width="20%"><b>PRODUCT NAME</b></th>
                                   <th><b>TRANSACTION DATE</b></th>
                                   <th><b>RECEIPT</b></th>
                                   <th width="5%"><b>QTY</b></th>
                                   <th width="5%"><b>UNIT</b></th>
                                   <th><b>PRICE</b></th>
                                   <th><b>TOTAL</b></th>
                                   <th><b>BRANCH</b></th>
                                   <th><b>PROMO</b></th>
                                 </tr>';
               }

               if($item["product_name"]!="Discount"){
                    $tbl.= '<tr>
                         <td>'.$item["product_name"].'</td>
                         <td>'.$item["transaction_date"].'</td>
                         <td>'.$item["receipt"].'</td>
                         <td>'.$item["qty"].'</td>
                         <td>'.$item["unit"].'</td>
                         <td>'.number_format($item["price"],2).'</td>
                         <td>'.number_format($item["total"],2).'</td>
                         <td>'.$item["branch"].'</td>
                         <td>'.$item["promo_code"].'</td>
                       </tr>';
               }else{
                   
                    $tbl .= '<tr>    
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.$item["qty"].'</b></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.number_format($item['total'],2).'</b></td>
                                   <td></td>
                                   <td></td> 
                              </tr><tr>    
                                   <td><b>'.$item["product_name"].'</b></td>
                                   <td><b>'.$item["transaction_date"].'</b></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.number_format($item['branch'],2).'</b></td>
                                   <td></td>
                                   <td></td> 
                              </tr><tr>    
                                   <td colspan="9"></td> 
                              </tr>     ';   
               }

               if($counter==13){
                    $counter = 0;
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }else{
                    $counter++; 
               }

               if($key==count($listDiscount)-1){
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }
          }
            
          ob_end_clean();
          echo $this->ppdf->Output();
     }


     // Reports - EOM
     function retrieveSummaryEOM(){
          if(isset($_POST['store_select']) && isset($_POST['start_date']) && isset($_POST['end_date'])){
               $store_id = $_POST['store_select'];
               $start = $_POST['start_date'];
               $end = $_POST['end_date'];
               $start_date = new DateTime($start);
               $end_date = new DateTime($end);

               if(($start!="" && !ctype_space($start)) && ($end!="" && !ctype_space($end))){
                    
                    if($end_date->format('Y-m-d') < $start_date->format('Y-m-d')){
                         echo "Start Date must be lesser than End Date!";
                    }else{
                         $summary = $this->Dummy_mod->retrieveSummaryItemsByMonth($start,$end,$store_id);
                         if(count($summary[1])>0)
                              echo json_encode($summary);
                         else
                              echo "No Record Found!";
                    }
               }else
                    echo json_encode(array());
          }
     }

     function generatePDFSummaryEOM($start,$end,$store_id){
          $this->ppdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
          $this->ppdf->SetTitle("DCMS - EOM");   
          $this->ppdf->SetMargins(5, 15, 5, true);
          $this->ppdf->setPrintHeader(false);
          $this->ppdf->SetFont('', '', 10, '', true);
          $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

          $listDiscount = $this->Dummy_mod->retrieveSummaryItemsByMonth($start,$end,$store_id);
          $pages = "Page: ".$this->ppdf->getAliasNumPage().'/'.$this->ppdf->getAliasNbPages(); 
          $counter = 0;

          foreach($listDiscount[1] as $key => $item){
               if($counter==0){
                    
                    $this->ppdf->AddPage("L");
                    
                    $tbl = '  <h1 align="center">End of Month (EOM) Liquidation Report</h1>
                              <h2 align="center"> From '.date("F j, Y", strtotime($start)).' to '.date("F j, Y", strtotime($end)).'</h2>
                              <h3 align="center">Number of Transactions: '.$listDiscount[0].'</h3>
                              <h4 align="center">'.$pages.'</h4>
                              <table align="center" cellspacing="1" border="1" cellpadding="3" style="font-size:9px;">
                                 <tr>
                                   <th width="20%"><b>PRODUCT NAME</b></th>
                                   <th><b>TRANSACTION DATE</b></th>
                                   <th><b>RECEIPT</b></th>
                                   <th width="5%"><b>QTY</b></th>
                                   <th width="5%"><b>UNIT</b></th>
                                   <th><b>PRICE</b></th>
                                   <th><b>TOTAL</b></th>
                                   <th><b>BRANCH</b></th>
                                   <th><b>PROMO</b></th>
                                 </tr>';
               }

               if($item["product_name"]!="Discount"){
                    $tbl.= '<tr>
                         <td>'.$item["product_name"].'</td>
                         <td>'.$item["transaction_date"].'</td>
                         <td>'.$item["receipt"].'</td>
                         <td>'.$item["qty"].'</td>
                         <td>'.$item["unit"].'</td>
                         <td>'.number_format($item["price"],2).'</td>
                         <td>'.number_format($item["total"],2).'</td>
                         <td>'.$item["branch"].'</td>
                         <td>'.$item["promo_code"].'</td>
                       </tr>';
               }else{
                   
                    $tbl .= '<tr>    
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.$item["qty"].'</b></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.number_format($item['total'],2).'</b></td>
                                   <td></td>
                                   <td></td> 
                              </tr><tr>    
                                   <td><b>'.$item["product_name"].'</b></td>
                                   <td><b>'.$item["transaction_date"].'</b></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.number_format($item['branch'],2).'</b></td>
                                   <td></td>
                                   <td></td> 
                              </tr><tr>    
                                   <td colspan="9"></td> 
                              </tr>     ';   
               }

               if($counter==10){
                    $counter = 0;
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }else{
                    $counter++; 
               }

               if($key==count($listDiscount[1])-1){
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }
          }
                      
          ob_end_clean();
          echo $this->ppdf->Output();
     }

     function extract_file(){
          $memory_limit = ini_get('memory_limit');
          ini_set('memory_limit',-1);
          ini_set('max_execution_time', 0);

          if(isset($_FILES['files'])){
               if(count($_FILES['files']['name']) == 2)
               {
                    $transaction_arr   = array();
                    $trans_payment_arr = array();

                    for($i=0; $i<count($_FILES['files']['name']); $i++)
                    {
                         $RESS2 = ''; 
                         if(!empty($_FILES["files"]["name"]))
                         {
                              $PDFfileName = basename($_FILES["files"]["name"][$i]); 
                              $PDFfileType = pathinfo($PDFfileName, PATHINFO_EXTENSION); 
                              $allowTypes  = array('txt'); 

                              if(in_array($PDFfileType, $allowTypes))
                              {
                                   if(!empty($_FILES['files']['tmp_name'])):
                              
                                        $fileName = $_FILES['files']['tmp_name'][$i];                
                                        $file     = fopen($fileName,"r") or exit("Unable to open file!");
                                        while(!feof($file)) 
                                        {
                                             @$RESS2 .= fgets($file). "";
                                        }
                                   endif;

                                   $ress_sanitize = explode(PHP_EOL, $RESS2); // Break newline   

                                   for($a=0;$a<count($ress_sanitize);$a++)
                                   {
                                        $row_explode = explode('","',$ress_sanitize[$a]);                                 
                                        // kuhaon ang "  ug , sa ani nga array $row_explode -----------------------
                                        $pattern = '/["\,]/';  //<-------pampakuha sa double quote(") ug comma (,)
                                        $cleaned_row_arr  = array_map(function($value) use ($pattern) 
                                        {
                                             return preg_replace($pattern, '', $value);
                                        }, $row_explode);
                                        // -----------------------------------------------------------------------
                                        //var_dump($cleaned_row_arr); 
                                          
                                        if(count($cleaned_row_arr) == 74) 
                                        {
                                             $trans_line_arr =  array(
                                                                      "transaction_no" => $cleaned_row_arr[0], 
                                                                      "receipt_no"     => $cleaned_row_arr[2], 
                                                                      "store"          => $cleaned_row_arr[4],
                                                                      "ordering_no"    => $cleaned_row_arr[53],
                                                                      "discount_amount"=> $cleaned_row_arr[21] 
                                                                      );

                                             array_push($transaction_arr,$trans_line_arr);

                                        }
                                        else 
                                        if(count($cleaned_row_arr) == 32) 
                                        {
                                             $payment_line_arr = array(
                                                                      "transaction_no"  => $cleaned_row_arr[0], 
                                                                      "receipt_no"      => $cleaned_row_arr[2], 
                                                                      "tender_type"     => $cleaned_row_arr[6], 
                                                                      "amount_tendered" => $cleaned_row_arr[7]
                                                                      );
                                                    
                                             array_push($trans_payment_arr,$payment_line_arr);
                                                    
                                        }
                                   }
                                     
                              }
                              else
                              { 
                                   $response = 'only textfile is allowed to upload.'; 
                              }     
                         }  
                    }

                    if(count($transaction_arr) == 0)
                    {
                         $response = 'Transaction textfile is missing, please upload it together with Transaction Payment Entry textfile';
                    }
                    else 
                    if(count($trans_payment_arr) == 0)
                    {
                         $response = 'Transaction Payment Entry textfile is missing, please upload it together with Transaction textfile';

                    }
                    else 
                    {
                         $valid = true;
                         $update_array = array();
                         $store_sess = $this->Dummy_mod->nav_data['store']; // Get Store of the User
                         $tender_type = $this->Dummy_mod->getTenderTypeSR(); // Get Tender Type of Promo
                         
                         for($a=0;$a<count($transaction_arr);$a++)
                         {
                              $transaction_no = $transaction_arr[$a]['transaction_no'];
                              $receipt_no     = $transaction_arr[$a]['receipt_no'];
                              $store          = $transaction_arr[$a]["store"];
                              $order_no       = $transaction_arr[$a]["ordering_no"];
                              $s_receipt      = $this->Dummy_mod->getSRByOrderNo($order_no);
                                             
                              if(isset($s_receipt)){
                                   $status        = $s_receipt["status"];
                                   $discount_dcms = $s_receipt["discount"];
                              }else{
                                   continue;
                              }

                              foreach ($trans_payment_arr as $key => $value) 
                              {
                                   if($value['transaction_no'] == $transaction_no && $value['receipt_no'] == $receipt_no && !strstr($value['amount_tendered'],'-') && $tender_type == $value['tender_type']) 
                                   {
                                        
                                        $discount_nav = $value["amount_tendered"];

                                        if(strstr($store,$store_sess)){ // Check if user is uploading the correct files. Ex ICM 
                                             
                                             $variance = $discount_dcms-$discount_nav;
                                             if($variance==0 && $status=="unsettled")
                                                  $status = "unbilled";

                                             $update_array[] = array("transaction_no" => $transaction_no, "receipt_no" => $receipt_no, "discount_nav" => $discount_nav, "status" => $status, "order_no" => $order_no);
                                             
                                        }else{
                                             $response = 'Ordering Number "'.$order_no.'" has a store not accessible by User!';
                                             $valid = false;
                                             break 2; // Breaks the inner and outer loop.

                                        }

                                   }
                              }                         
                         }

                         if(empty($update_array)){
                              $response = "No Matching Data.";
                              $valid = false;
                         }

                         if($valid){
                              for($c=0; $c<count($update_array); $c++){
                                   $this->Dummy_mod->updateSRReceiptNo($update_array[$c]["receipt_no"],$update_array[$c]["transaction_no"],$update_array[$c]["discount_nav"],$update_array[$c]["status"],$update_array[$c]["order_no"]);
                              }
                              
                              
                              $response = "Success";
                         }
                     }
                     
                }
                else 
                if(count($_FILES['files']['name']) > 2)
                {
                     $response = 'Two files only allowed to upload simultaneously. Transaction and Transaction Payment Entry';
                }   
                else  
                {
                     $response = 'Transaction and Transaction Payment Entry should be uploaded simultaneously';
                }
           }else
               $response = 'No Files Selected.';
           


           ini_set('memory_limit',$memory_limit );
           $data['response'] = $response;
           echo json_encode($data);             
     }


     function generateTextFile($selected){
          header("Content-Type: text/plain");
          header("Content-Disposition: attachment; filename=".$selected."_dataport.txt");
     
          $dataport   = '';
          $trans_line = $this->Dummy_mod->getListOfOrderingNo($selected);

          foreach($trans_line as $data){
               $order_number = $data['order_no'];
               $dataport .= $order_number."|";
          }

          echo substr($dataport,0,-1);           
          
     }

     // Reports - Variance
     function retrieveVarianceReports(){
          if(isset($_POST['store_select']) && isset($_POST['start_date']) && isset($_POST['end_date'])){
               $store_id = $_POST['store_select'];     
               $start = $_POST['start_date'];
               $end = $_POST['end_date'];
               $start_date = new DateTime($start);
               $end_date = new DateTime($end);

               if(($start!="" && !ctype_space($start)) && ($end!="" && !ctype_space($end))){
                    
                    if($end_date->format('Y-m-d') < $start_date->format('Y-m-d')){
                         echo "Start Date must be lesser than End Date!";
                    }else{
                         $listVariance = $this->Dummy_mod->retrieveVariance($start,$end,$store_id);
                         if(count($listVariance)>0)
                              echo json_encode($listVariance);
                         else
                              echo "No Record Found!";
                    }

               }else
                    echo json_encode(array());
          }
     }

     function generatePDFVariance($start,$end,$store_id){
          $this->ppdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
          $this->ppdf->SetTitle("DCMS - Inhouse vs. Navision");    
          $this->ppdf->SetMargins(5, 15,5, true);
          $this->ppdf->setPrintHeader(false);
          $this->ppdf->SetFont('', '', 10, '', true);
          $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

          $listDiscount = $this->Dummy_mod->retrieveVariance($start,$end,$store_id);
          $pages = "Page: ".$this->ppdf->getAliasNumPage().'/'.$this->ppdf->getAliasNbPages(); 
          $counter = 0;

          foreach($listDiscount as $key => $item){
               if($counter==0){

                    $this->ppdf->AddPage();

                    $tbl = '  <h1 align="center">Inhouse vs. Navision</h1>
                              <h2 align="center">From '.date("F j, Y", strtotime($start)).' to '.date("F j, Y", strtotime($end)).'</h2>
                              <h4 align="center">'.$pages.'</h4>
                              <table align="center" cellspacing="1" border="1" cellpadding="3" style="font-size:9px;">
                                 <tr>
                                   <th><b>TRANSACTION DATE</b></th>
                                   <th><b>ORDER NO.</b></th>
                                   <th><b>DISCOUNT (DCMS)</b></th>
                                   <th><b>DISCOUNT (NAV)</b></th>
                                   <th><b>VARIANCE</b></th>
                                 </tr>';

               }

               $tbl.= '<tr>
                         <td>'.$item["transaction_date"].'</td>
                         <td>'.$item["order_no"].'</td>
                         <td>'.$item["discount_dcms"].'</td>
                         <td>'.$item["discount_nav"].'</td>
                         <td>'.$item["variance"].'</td>
                       </tr>';

               if($counter==30){
                    $counter = 0;
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }else{
                    $counter++; 
               }

               if($key==count($listDiscount)-1){
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }
          }

          ob_end_clean();
          echo $this->ppdf->Output();
     }


     // Billing
     function retrieveBilling(){
          if(isset($_POST['store_select']) && isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['status_select'])){
               $store_id = $_POST['store_select'];
               $start = $_POST['start_date'];
               $end = $_POST['end_date'];
               $status = $_POST['status_select'];
               $start_date = new DateTime($start);
               $end_date = new DateTime($end);

               if(($start!="" && !ctype_space($start)) && ($end!="" && !ctype_space($end))){
                    
                    if($end_date->format('Y-m-d') < $start_date->format('Y-m-d')){
                         echo "Start Date must be lesser than End Date!";
                    }else{
                         $listBilling = $this->Dummy_mod->retrieveSingleReceiptForBilling($start,$end,$status,$store_id);
                         if(count($listBilling)>0)
                              echo json_encode($listBilling);
                         else
                              echo "No Record Found!";
                    }

               }else
                    echo json_encode(array());
          }
     }


     function processBilling(){
          if(isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['checkedElements'])){
               
               $start = $_POST['start_date'];
               $end = $_POST['end_date'];
               $checkedElements = $_POST['checkedElements']; //  Array of SR ID
               $start_date = new DateTime($start);
               $end_date = new DateTime($end);
               $results = array();

               if(count($checkedElements)<1)
                    echo "No Selected CheckBoxes!";
               else if($end_date->format('Y-m-d') < $start_date->format('Y-m-d')){
                    echo "Start Date must be lesser than End Date!";
               }else{
                    $new_batch_id = $this->Dummy_mod->addBatchEntry($start,$end);
                    
                    for($c=0; $c<count($checkedElements); $c++){
                         $sr_id = $checkedElements[$c];
                         $sr_details = $this->Dummy_mod->getSRByID($sr_id);
                         $order_no = $sr_details["order_no"];
                         $batch_id = $sr_details["batch_id"];

                         if($batch_id==0){
                              $this->Dummy_mod->updateSRBatchID($new_batch_id,$sr_id);
                              $results[] = $order_no; 
                         }
                         
                    }

                    if(count($results)<1)
                         echo "No Ordering Number Billed!";
                    else
                         echo json_encode(array("The Following Ordering Numbers has been Billed!", $results));
               }
          }
     }

     function retrieveBillingBatch(){
          $listBatch = $this->Dummy_mod->getBatches();
          if(count($listBatch)>0)
               echo json_encode($listBatch);
          else
               echo json_encode(array());
     }

     function saveBatchInvoice(){
          if(isset($_POST["invoice"])){
               $invoice = $_POST["invoice"];
               $batch_id = $_POST['batch_id'];

               if($invoice=="" || ctype_space($invoice))
                    echo "Pls Input Invoice No.!";
               else{
                    $this->Dummy_mod->updateBatchInvoice($invoice,$batch_id); 
                    $this->Dummy_mod->updateSRPaid($batch_id); // Updates status to paid
                    echo json_encode(array("Invoice No. for Batch No. ".$batch_id." Successfully Posted!"));
               }     

          }
     }


     function generatePDFBilling($batch_id){
          $this->ppdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
          $this->ppdf->SetTitle("DCMS - Billing");    
          $this->ppdf->SetMargins(20, 15, 20, true);
          $this->ppdf->setPrintHeader(false);
          $this->ppdf->SetFont('', '', 10, '', true);

          $Billing = $this->Dummy_mod->retrieveBillingBatchBySR($batch_id);
          $headBilling = $Billing["head"];
          
          $this->ppdf->AddPage();
          $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
          
          $this->ppdf->Image(base_url().$headBilling["logo_path1"], 75, 5, 50, 50, 'PNG');

          $locale = 'en_US';
          $formatter = new NumberFormatter($locale, NumberFormatter::SPELLOUT);
          // Convert the number to its text representation
          $textNum = $formatter->format($headBilling["total_discount"]);

          $cover = '<br><br><br><br><br><br><br>
                    <table align="center">
                    <tr><td>'.$headBilling["store_address"].'</td></tr>
                    <tr><td>'.$headBilling["contact"].'</td></tr>
                    </tr></table>
                    <br><br><br>
                    <table>
                    <tr><td>Date: '.date("F j, Y", strtotime($headBilling["date_generated"])).'</td>
                    <td align="center">Bill No. '.date("Ymd", strtotime($headBilling["date_generated"])).'-'.$batch_id.'</td></tr>
                    </table>
                    <br><br><br>
                    <table>
                    <tr><td width="30px">To: </td><td><b>Marcela Pharma Distribution Inc.</b></td></tr>
                    <tr><td> </td><td></td></tr>
                    </table>
                    <br><br><br><br>
                    <table>
                    <tr><td width="50px"><b>Subject:</b> </td><td width="400px"><b>Billing Statement for Health+ Discount 
                    to '.$headBilling["company_name"].' transacted under '.$headBilling["promo_count"].' 
                    '.$headBilling["dept"].' codes  from '.date("F j, Y", strtotime($headBilling["from_date"])).' 
                    up to '.date("F j, Y", strtotime($headBilling["to_date"])).'
                    </b></td></tr>
                    </table>
                    <br><br>
                    <table>
                    <tr><td>
                    <p>To Whom It May Concern,</p>
                    <p>Good Day!</p>
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Please be billed the total amount of '.$textNum.' Pesos 
                    (Php.'.number_format($headBilling["total_discount"],2).') only, representing payment for Health+ 
                    Discount to '.$headBilling["company_name"].' transacted under '.$headBilling["promo_count"].' 
                    '.$headBilling["dept"].' codes from '.date("F j, Y", strtotime($headBilling["from_date"])).' 
                    up to '.date("F j, Y", strtotime($headBilling["to_date"])).'</p>
                    <p>Your prompt attention and early settlement of this account would be highly appreciated.</p>
                    <p>Thank You!</p>
                    </td></tr>
                    </table>
                    <br><br><br><br><br>
                    <table>
                    <tr>
                    <td colspan="2">Very Truly Yours,</td>
                    </tr>
                    <tr>
                    <td colspan="2">&nbsp;</td>
                    </tr><tr>
                    <td><b>'.$headBilling["supervisor"].'</b></td><td align="center"><b>'.$headBilling["doctor"].'</b></td>
                    </tr><tr>
                    <td>'.$headBilling["supervisor_position"].'</td><td align="center">'.$headBilling["doctor_position"].'</td>
                    </tr>
                    <table>
                    ';

          // Unilab consumer health products transacted under Unilab 100 EC Codes          
          $this->ppdf->writeHTML($cover, true, false, false, false, '');

          // Next Page
          $this->ppdf->SetMargins(5, 15, 5, true);
          $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

          $pages = "Page: ".$this->ppdf->getAliasNumPage().'/'.$this->ppdf->getAliasNbPages(); 
          $counter = 0;

          foreach($Billing["list"] as $key => $item){
               if($counter==0){
                    
                    $this->ppdf->AddPage("L");
                    
                    $tbl = '  <h4 align="center">'.$pages.'</h4>
                              <table align="center" cellspacing="1" border="1" cellpadding="3" style="font-size:9px;">
                                 <tr>
                                   <th width="20%"><b>PRODUCT NAME</b></th>
                                   <th><b>TRANSACTION DATE</b></th>
                                   <th><b>RECEIPT</b></th>
                                   <th width="5%"><b>QTY</b></th>
                                   <th width="5%"><b>UNIT</b></th>
                                   <th><b>PRICE</b></th>
                                   <th><b>TOTAL</b></th>
                                   <th><b>BRANCH</b></th>
                                   <th><b>PROMO</b></th>
                                 </tr>';
               }

               if($item["product_name"]!="Discount"){
                    $tbl.= '<tr>
                         <td>'.$item["product_name"].'</td>
                         <td>'.$item["transaction_date"].'</td>
                         <td>'.$item["receipt"].'</td>
                         <td>'.$item["qty"].'</td>
                         <td>'.$item["unit"].'</td>
                         <td>'.number_format($item["price"],2).'</td>
                         <td>'.number_format($item["total"],2).'</td>
                         <td>'.$item["branch"].'</td>
                         <td>'.$item["promo_code"].'</td>
                       </tr>';
               }else{
                   
                    $tbl .= '<tr>    
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.$item["qty"].'</b></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.number_format($item['total'],2).'</b></td>
                                   <td></td>
                                   <td></td> 
                              </tr><tr>    
                                   <td><b>'.$item["product_name"].'</b></td>
                                   <td><b>'.$item["transaction_date"].'</b></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.number_format($item['branch'],2).'</b></td>
                                   <td></td>
                                   <td></td> 
                              </tr><tr>    
                                   <td colspan="9"></td> 
                              </tr>     ';   
               }

               if($counter==12){
                    $counter = 0;
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }else{
                    $counter++; 
               }

               if($key==count($Billing["list"])-1){
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }
          }
          
          ob_end_clean();
          echo $this->ppdf->Output();
     }

     
     // MPDI - EOM
     function retrieveSummaryEOM_mpdi(){
          if(isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['store'])){
               $start = $_POST['start_date'];
               $end = $_POST['end_date'];
               $db_id = $_POST['store'];
               $start_date = new DateTime($start);
               $end_date = new DateTime($end);

               if(($start!="" && !ctype_space($start)) && ($end!="" && !ctype_space($end))){
                    
                    if($end_date->format('Y-m-d') < $start_date->format('Y-m-d')){
                         echo "Start Date must be lesser than End Date!";
                    }else{
                         $summary = $this->Dummy_mod->retrieveSummaryItemsByMpdi($start,$end,$db_id);
                         if(count($summary)>0)
                              echo json_encode($summary);
                         else
                              echo "No Record Found!";
                    }
               }else
                    echo json_encode(array());
          }
     }

     function generatePDFSummaryEOM_mpdi($start,$end,$db_id){
          $this->ppdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
          $this->ppdf->SetTitle("DCMS - EOM");   
          $this->ppdf->SetMargins(5, 15, 5, true);
          $this->ppdf->setPrintHeader(false);
          $this->ppdf->SetFont('', '', 10, '', true);
          $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

          $listDiscount = $this->Dummy_mod->retrieveSummaryItemsByMpdi($start,$end,$db_id);
          $pages = "Page: ".$this->ppdf->getAliasNumPage().'/'.$this->ppdf->getAliasNbPages(); 
          $counter = 0;

          foreach($listDiscount as $key => $item){
               if($counter==0){
                    
                    $this->ppdf->AddPage("L");
                    
                    $tbl = '  <h1 align="center">End of Month (EOM) Liquidation Report</h1>
                              <h2 align="center"> From '.date("F j, Y", strtotime($start)).' to '.date("F j, Y", strtotime($end)).'</h2>
                              <h4 align="center">'.$pages.'</h4>
                              <table align="center" cellspacing="1" border="1" cellpadding="3" style="font-size:9px;">
                                 <tr>
                                   <th width="20%"><b>PRODUCT NAME</b></th>
                                   <th><b>TRANSACTION DATE</b></th>
                                   <th><b>RECEIPT</b></th>
                                   <th width="5%"><b>QTY</b></th>
                                   <th width="5%"><b>UNIT</b></th>
                                   <th><b>PRICE</b></th>
                                   <th><b>TOTAL</b></th>
                                   <th><b>BRANCH</b></th>
                                   <th><b>PROMO</b></th>
                                 </tr>';
               }

               if($item["product_name"]!="Discount"){
                    $tbl.= '<tr>
                         <td>'.$item["product_name"].'</td>
                         <td>'.$item["transaction_date"].'</td>
                         <td>'.$item["receipt"].'</td>
                         <td>'.$item["qty"].'</td>
                         <td>'.$item["unit"].'</td>
                         <td>'.number_format($item["price"],2).'</td>
                         <td>'.number_format($item["total"],2).'</td>
                         <td>'.$item["branch"].'</td>
                         <td>'.$item["promo_code"].'</td>
                       </tr>';
               }else{
                   
                    $tbl .= '<tr>    
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.$item["qty"].'</b></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.number_format($item['total'],2).'</b></td>
                                   <td></td>
                                   <td></td> 
                              </tr><tr>    
                                   <td><b>'.$item["product_name"].'</b></td>
                                   <td><b>'.$item["transaction_date"].'</b></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td><b>'.number_format($item['branch'],2).'</b></td>
                                   <td></td>
                                   <td></td> 
                              </tr><tr>    
                                   <td colspan="9"></td> 
                              </tr>     ';   
               }

               if($counter==13){
                    $counter = 0;
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }else{
                    $counter++; 
               }

               if($key==count($listDiscount)-1){
                    $tbl.= '</table>';
                    $this->ppdf->writeHTML($tbl, true, false, false, false, '');
               }
          }
                      
          ob_end_clean();
          echo $this->ppdf->Output();
     }

     // MPDI - EOM
     function retrieveBilling_mpdi(){
          if(isset($_POST['start_date']) && isset($_POST['end_date'])){
               $start = $_POST['start_date'];
               $end = $_POST['end_date'];
               $start_date = new DateTime($start);
               $end_date = new DateTime($end);

               if(($start!="" && !ctype_space($start)) && ($end!="" && !ctype_space($end))){
                    
                    if($end_date->format('Y-m-d') < $start_date->format('Y-m-d')){
                         echo "Start Date must be lesser than End Date!";
                    }else{
                         $summary = $this->Dummy_mod->retrieveBillingByMpdi($start,$end);
                         if(count($summary)>0)
                              echo json_encode($summary);
                         else
                              echo "No Record Found!";
                    }
               }else
                    echo json_encode(array());
          }
     }

     function retrieveBillingBatch_mpdi(){
          $batch_id = $_POST['batch_id'];
          $listBatch = $this->Dummy_mod->retrieveBillingBatchByMpdi($batch_id);
          if(count($listBatch)>0)
               echo json_encode($listBatch);
          else
               echo json_encode(array());
     }

     function generateExcelFile(){
          if(isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['checkedElements'])){
               
               $start = $_POST['start_date'];
               $end = $_POST['end_date'];
               $checkedElements = $_POST['checkedElements']; //  Array of Batch ID
               $start_date = new DateTime($start);
               $end_date = new DateTime($end);

               if(count($checkedElements)<1)
                    echo "No Selected CheckBoxes!";
               else if($end_date->format('Y-m-d') < $start_date->format('Y-m-d')){
                    echo "Start Date must be lesser than End Date!";
               }else{

                    header("content-type: application/vnd.ms-excel");
                    header("Content-Disposition: attachment; filename=MPDI BILLING_".$_POST['start_date']." to ".$_POST['end_date'].".xls");
       
                    $dateFrom = date("F j, Y", strtotime($_POST['start_date']));
                    $dateTo = date("F j, Y", strtotime($_POST['end_date']));

                    $tbl = '<table>
                                   <tr><td><strong>MPDI BILLING REPORT</strong></td></tr> 
                                   <tr>
                                        <td><strong>From:</strong>'.$dateFrom."</td>
                                        <td></td>
                                        <td><strong>To:</strong>".$dateTo."</td>      
                                   </tr><tr><td></td></tr>
                             </table>" ;

                    for($a=0; $a<count($checkedElements); $a++){
                         
                         $batch_id = $checkedElements[$a];
                         $batch_no = $this->Dummy_mod->getBatchNumber($batch_id);
                         $batch_details = $this->Dummy_mod->retrieveBillingBatchByMpdi($batch_id);
                         $this->Dummy_mod->updateBatchExtract($batch_id);

                         $tbl .= '<table><tr><td>
                                             <strong>Billing Number:</strong>'.$batch_no.'
                                        </td></tr>
                                   </table>
                                   <table border="1">
                                        <th>PRODUCT NAME</th>
                                        <th>TRANSACTION DATE</th>
                                        <th>RECEIPT</th>
                                        <th>QTY</th>
                                        <th>UNIT</th>
                                        <th>PRICE</th>
                                        <th>TOTAL</th>
                                        <th>BRANCH</th>
                                        <th>PROMO</th>';


                         foreach($batch_details as $batch){
                               
                              if($batch["product_name"]!="Discount"){
                                   $tbl .= '<tr>    
                                             <td>'.$batch["product_name"].'</td>
                                             <td>'.$batch["transaction_date"].'</td>
                                             <td>'.$batch["receipt"].'</td>
                                             <td>'.$batch["qty"].'</td>
                                             <td>'.$batch["unit"].'</td>
                                             <td>'.number_format($batch['price'],2).'</td>
                                             <td>'.number_format($batch['total'],2).'</td>
                                             <td>'.$batch['branch'].'</td>
                                             <td>'.$batch['promo_code'].'</td> 
                                        </tr>    '; 
                              
                              }else{
                                   $tbl .= '<tr>    
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td><b>'.$batch["qty"].'</b></td>
                                             <td></td>
                                             <td></td>
                                             <td><b>'.number_format($batch['total'],2).'</b></td>
                                             <td></td>
                                             <td></td> 
                                        </tr><tr>    
                                             <td><b>'.$batch["product_name"].'</b></td>
                                             <td><b>'.$batch["transaction_date"].'</b></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td><b>'.number_format($batch['branch'],2).'</b></td>
                                             <td></td>
                                             <td></td> 
                                        </tr><tr>    
                                             <td colspan="9"></td> 
                                        </tr>     ';
                              } 
                                          

                         } // Foreach
                          
                         $tbl .= "</table><br><br>";
                    
                    } // For


                     echo $tbl;
               
               } // Else 
          }
     }
}