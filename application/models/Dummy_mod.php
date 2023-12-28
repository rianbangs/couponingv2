<?php

class Dummy_mod extends CI_Model{
    
    public $nav_data;
    private $discount_data;

	function __construct(){
        parent::__construct();
        $this->setUpNavConnect();
        $this->setUpDiscount();
    }

    private function setUpNavConnect(){
        $this->mpdi = $this->load->database('mpdi', TRUE);
        $this->mpdi->select('*');
        $this->mpdi->from('database');
        $this->mpdi->where('db_id',$_SESSION['db_id']);
        $query = $this->mpdi->get();
        $row = $query->row_array();
        if(isset($row)){
            $this->nav_data['db_name'] = $row["db_name"];
            $this->nav_data['username'] = $row["username"];  
            $this->nav_data['password'] = $row["password"];
            $this->nav_data['sub_db_name'] = $row["sub_db_name"];
            $this->nav_data['store'] = $row["store"];
            $this->nav_data['address_id'] = $row["address_id"];
        }
    }

    private function setUpNavConnectAlta(){
        $this->mpdi = $this->load->database('mpdi', TRUE);
        $this->mpdi->select('*');
        $this->mpdi->from('database');
        $this->mpdi->where('db_id',23);
        $query = $this->mpdi->get();
        $row = $query->row_array();
        $result = array();
        if(isset($row)){
            $result['db_name'] = $row["db_name"];
            $result['username'] = $row["username"];  
            $result['password'] = $row["password"];
            $result['sub_db_name'] = $row["sub_db_name"];
            $result['store'] = $row["store"];
            $result['address_id'] = $row["address_id"];
        }

        return $result;
    }

    private function setUpDiscount(){
        $this->db->select("*");
        $this->db->from('promo_setup_sr');
        $this->db->where('promo_id',$_SESSION['promo_id']);
        $query = $this->db->get();
        $row = $query->row_array();
        if(isset($row)){
            $this->discount_data['discount_type'] = $row['discount_type'];
            $this->discount_data['minimum'] = $row['minimum'];
            $this->discount_data['maximum'] = $row['maximum'];
            $this->discount_data['discount'] = $row['discount'];
        }else{
            $this->discount_data['discount_type'] = "";
            $this->discount_data['minimum'] = 0;
            $this->discount_data['maximum'] = 0;
            $this->discount_data['discount'] = 0;
        }
    }

    function getDisplayStore($db_id){
        $display = 'ALL STORES';
        $this->mpdi = $this->load->database('mpdi', TRUE);
        $this->mpdi->select('display_name');
        $this->mpdi->from('database');
        $this->mpdi->where('db_id',$db_id);
        $query = $this->mpdi->get();
        $row = $query->row_array();
        if(isset($row)){
            $display = $row["display_name"];
        }

        return $display;
    }

    function getUserDetails(){
        $this->db->select("*");
        $this->db->from('cupon_users');
        $this->db->where('username',$_SESSION['username']);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getStoreDetails($db_id){
        $cmd = 'SELECT address_location, image_logo_path, contact_no, supervisor, supervisor_position, doctor, doctor_position 
                FROM store_info a INNER JOIN `database` b ON a.address_id=b.address_id WHERE db_id=?';
        $this->mpdi = $this->load->database('mpdi', TRUE);
        $query = $this->mpdi->query($cmd,array($db_id));
        return $query->row_array();
    }

    function getCompanyDetails(){
        $this->db->select("*");
        $this->db->from('promo_list');
        $this->db->where('promo_id',$_SESSION['promo_id']);
        $query = $this->db->get();
        return $query->row_array();
    }

    function searchNameOrPhone($search,$ind){
        $columns = array("fname","lname","phone_no");
        $this->db->select($columns[$ind])->distinct();
        $this->db->from('customer_data');
        $this->db->like($columns[$ind],$search,'both');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }

    function addCustomer($fname,$lname,$phone,$year){
        $this->db->insert("customer_data",array("fname"=>$fname, "lname"=>$lname, "phone_no"=>$phone, "birth_year"=>$year));
        return $this->db->insert_id();
    }

    function getCustomerID($fname,$lname,$phone,$year){
        $this->db->select('customer_id');
        $this->db->from("customer_data");
        $this->db->where("fname",$fname);
        $this->db->where("lname",$lname);
        $this->db->where("phone_no",$phone);
        $this->db->where("birth_year",$year); 
        $query = $this->db->get();
        return $query->row_array();
    }

    function getTenderTypeSR(){
        $type = "";
        $this->db->select('tender_type_sr');
        $this->db->from("promo_list");
        $this->db->where('promo_id',$_SESSION['promo_id']);
        $query = $this->db->get();
        $row = $query->row_array();
        if(isset($row))
            $type = $row["tender_type_sr"];

        return $type;
    }

    function getTransactionCountByOrderNo($order_no,$ind){
        $tables = array('cupon_data','single_receipt');
        $column = array('ordering_number','order_no');

        $this->db->select('COUNT(*) as counted');
        $this->db->from($tables[$ind]);
        $this->db->where($column[$ind],$order_no);   
        $query = $this->db->get();
        return $query->row_array()["counted"];
    }

    function getPromoCodeCount($promo_code){
        $this->db->select('COUNT(*) as counted');
        $this->db->from('single_receipt');
        $this->db->where('promo_code',$promo_code);   
        $query = $this->db->get();
        return $query->row_array()["counted"];
    }

    function getCustomerByPromo($promo_code){
        $customer = "";
        $cmd = "SELECT CONCAT(fname,' ',lname) as cust FROM customer_data INNER JOIN single_receipt ON 
        customer_data.customer_id=single_receipt.customer_id WHERE promo_code=?";
        $query = $this->db->query($cmd,array($promo_code));
        $row = $query->row_array();
        if(isset($row)){
            $customer = $row["cust"];
        }

        return $customer;

    }

    function getPromoCodeCountByBatch($batch_id){
        $this->db->select('COUNT(*) as counted');
        $this->db->from('single_receipt');
        $this->db->where('batch_id',$batch_id);   
        $query = $this->db->get();
        return $query->row_array()["counted"];
    }

    function getSRIDByOrderNo($order_no){
        $this->db->select('sr_id');
        $this->db->from('single_receipt');
        $this->db->where('order_no',$order_no);
        $this->db->where('db_id',$_SESSION['db_id']);   
        $query = $this->db->get();
        return $query->row_array()["sr_id"];
    }

    private function searchParticipatingItem($item_code,$uom){
         $this->db->select('COUNT(*) as counted');
         $this->db->from('promo_item_list_sr');
         $this->db->where('item_code',$item_code);
         $this->db->where('uom',$uom);
         $this->db->where('promo_id',$_SESSION['promo_id']);   
         $query = $this->db->get();
         return $query->row_array()["counted"];
    }

    function retrieveOrderNo($order_no){ // Navision
        $table = '['.$this->nav_data['sub_db_name'].'$Take Order Line]';
        $connect = odbc_connect($this->nav_data['db_name'], $this->nav_data['username'], $this->nav_data['password']);

        $table_query = "SELECT DISTINCT TOP 10 [No_] FROM ".$table." WHERE [No_] LIKE '%".$order_no."%'";
        
        if($_SESSION['db_id'] == 19){ // If Alta Citta
            $table = '['.$this->nav_data['sub_db_name'].'$POS Trans_ Line]';
            $table_query = "SELECT DISTINCT TOP 10 [Receipt No_] FROM ".$table." WHERE [Receipt No_] LIKE '%".$order_no."%'";
        }

        $table_row = odbc_exec($connect, $table_query);  
        $result = array();
        while(odbc_fetch_row($table_row)){
            if($_SESSION['db_id'] == 19){ // If Alta Citta
                $raw = odbc_result($table_row, 1);
                $order_num_exp = explode('P',$raw);
                $order_num     = "Counter ".substr($order_num_exp[1], 0, 2).'-'.substr($order_num_exp[1], 2);
                $result[] = array("order_no" => $order_num);
            }else{
                $result[] = array("order_no" => odbc_result($table_row, 1));    
            }
            
        }

        odbc_free_result($table_row);
        odbc_close($connect);
        return $result;
    }

    function countOrderNo($order_no){ // Navision
        $table = '['.$this->nav_data['sub_db_name'].'$Take Order Line]';
        $connect = odbc_connect($this->nav_data['db_name'], $this->nav_data['username'], $this->nav_data['password']);

        $table_query = "SELECT COUNT(*) AS count_ FROM ".$table." WHERE [No_]=?";
        
        if($_SESSION['db_id'] == 19){ // If Alta Citta
            $table = '['.$this->nav_data['sub_db_name'].'$POS Trans_ Line]';
            $table_query = "SELECT COUNT(*) AS count_ FROM ".$table." WHERE [Receipt No_]=?";
        }

        $result = odbc_prepare($connect, $table_query);
        odbc_execute($result, array($order_no));
        
        $count = 0;
        if($row = odbc_fetch_array($result)) {
            $count = $row["count_"];
        }

        odbc_free_result($result);
        odbc_close($connect);
        return $count;
    }

    function retrieveTakeOrder($order_no){ // Navision
        $table = '['.$this->nav_data['sub_db_name'].'$Take Order Line]';
        $connect = odbc_connect($this->nav_data['db_name'], $this->nav_data['username'], $this->nav_data['password']);

        $table_query = "SELECT  [Item No_], 
                                [Unit of Measure], 
                                [Item Description], 
                                [Quantity], 
                                [Unit Price],
                                [Item Generic Code] 
                                FROM ".$table." WHERE [No_]='".$order_no."'";
        
        if($_SESSION['db_id'] == 19){ // If Alta Citta
            $table = '['.$this->nav_data['sub_db_name'].'$POS Trans_ Line]';
            $table2 = '['.$this->nav_data['sub_db_name'].'$Item]';
            $table_query = "SELECT  ".$table.".[Number],  
                                    ".$table.".[Description], 
                                    ".$table.".[Quantity], 
                                    ".$table.".[Org_ Price Inc_ VAT],
                                    ".$table2.".[Sales Unit of Measure],
                                    ".$table2.".[Generic Name] 
                                    FROM ".$table." INNER JOIN ".$table2." ON ".$table.".[Number]=".$table2.".[No_] 
                                    WHERE ".$table.".[Receipt No_]='".$order_no."'";
        }

        $table_row = odbc_exec($connect, $table_query);  
        
        $result["list"] = array();
        $result["compute"] = array();

        $discount = 0;
        $t_qty = 0;
        $t_cost = 0;

        while(odbc_fetch_row($table_row)){
            
            if($_SESSION['db_id'] == 19){ // If Alta Citta
                $item_code = odbc_result($table_row, 1);
                $uom = odbc_result($table_row, 5);
                $item_desc = odbc_result($table_row, 2);
                $qty = round(odbc_result($table_row, 3),0);
                $price = round(odbc_result($table_row, 4),2);
                $generic_name = odbc_result($table_row, 6);
            }else{
                $item_code = odbc_result($table_row, 1);
                $uom = odbc_result($table_row, 2);
                $item_desc = odbc_result($table_row, 3);
                $qty = round(odbc_result($table_row, 4),0);
                $price = round(odbc_result($table_row, 5),2);
                $generic_name = odbc_result($table_row, 6);
            }

            $t_price = $price*$qty;

            $count = $this->searchParticipatingItem($item_code,$uom); // check if participating item
            
            if($count>0){
                $result["list"][] = array(  "item_code" => $item_code, 
                                            "uom" => $uom,
                                            "item_desc" => $item_desc,
                                            "qty" => $qty,
                                            "price" => $price,
                                            "generic_name" => $generic_name,
                                            "t_price" => $t_price
                                );

                $t_qty += $qty;
                $t_cost += $t_price; 
            }
        } // While End

        if($this->discount_data["discount"]!=0 && $t_cost>=$this->discount_data["minimum"]){ 
            $temp = ($t_cost>$this->discount_data["maximum"]) ? $this->discount_data["maximum"]:$t_cost; 
            $discount = intval($temp/$this->discount_data["minimum"])*$this->discount_data["discount"];
        }
        
        $d_cost = $t_cost-$discount; // Discounted Total Cost

        $result["compute"] = array("order_no" => $order_no, "t_qty" => $t_qty, "t_cost" => $t_cost, "discount" => $discount, "d_cost" => $d_cost, "discount_type" => $this->discount_data["discount_type"], "minimum" => $this->discount_data["minimum"], "maximum" => $this->discount_data["maximum"]);
        
        odbc_free_result($table_row);
        odbc_close($connect);
        return $result;
        
    }

    function insertItemTransactionSingle($order_no,$customer_id,$promo_code,$discount_type,$minimum,$maximum,$discount,$entries){
        $insert_id = 0;
        $user_details = $this->getUserDetails();

        $insert_data["order_no"] = $order_no;
        $insert_data["customer_id"] = $customer_id;
        $insert_data["promo_code"] = $promo_code;
        $insert_data["discount_type"] = $discount_type;
        $insert_data["minimum"] = $minimum;
        $insert_data["maximum"] = $maximum;
        $insert_data["discount"] = $discount;
        $insert_data["date_transact"] = date("Y-m-d H:i:s");
        $insert_data["db_id"] = $_SESSION['db_id'];
        $insert_data["transact_by"] = $_SESSION['user_id'];

        $this->db->insert("single_receipt",$insert_data);
        $insert_id = $this->db->insert_id();

        $inserted_entries = array();
        for($c=0; $c<count($entries); $c++){
            $inserted_entries[] = array("sr_id"=>$insert_id, "item_code"=>$entries[$c]["item_code"], "uom"=>$entries[$c]["uom"], "unit_price"=>$entries[$c]["price"], "qty"=>$entries[$c]["qty"], "description"=>$entries[$c]["item_desc"], "generic_name"=>$entries[$c]["generic_name"]);
        }

        $this->db->insert_batch('item_transact_sr', $inserted_entries);
        return $insert_id;
    }

    function updateSRReceiptNo($receipt_no,$transaction_no,$discount,$status,$order_no){
        $updated_data = array("receipt_no" => $receipt_no, "transaction_no" => $transaction_no, "discount_nav" => $discount, "status" => $status);
        $where_data = array("order_no" => $order_no, "db_id" => $_SESSION['db_id']);
        $this->db->update('single_receipt', $updated_data, $where_data);
    }

    function addBatchEntry($start,$end){
        $this->db->insert("promo_billing_batch_sr",array("from_date" => $start, "to_date" => $end, "date_generated" => date("Y-m-d"), "user_id" => $_SESSION['user_id']));
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    function updateSRBatchID($batch_id,$sr_id){
        $updated_data = array("status" => "billed", "batch_id" => $batch_id);
        $where_data = array("sr_id" => $sr_id/*, "db_id" => $_SESSION['db_id']*/);
        $this->db->update('single_receipt', $updated_data, $where_data);
    }

    function updateBatchInvoice($invoice,$batch_id){
        $updated_data = array("paid_invoice_number" => $invoice);
        $where_data = array("batch_id" => $batch_id);
        $this->db->update('promo_billing_batch_sr', $updated_data, $where_data);
    }

    function updateBatchExtract($batch_id){
        $updated_data = array("date_extracted" => date("Y-m-d"));
        $where_data = array("batch_id" => $batch_id);
        $this->db->update('promo_billing_batch_sr', $updated_data, $where_data);
    }

    function updateSRPaid($batch_id){
        $updated_data = array("status" => "paid");
        $where_data = array("batch_id" => $batch_id);
        $this->db->update('single_receipt', $updated_data, $where_data);
    }

    function getSRByID($sr_id){
        $this->db->select('*');
        $this->db->from('single_receipt');
        $this->db->where("sr_id",$sr_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getSRByOrderNo($order_no){
        $this->db->select('*');
        $this->db->from('single_receipt');
        $this->db->where("order_no",$order_no);
        $query = $this->db->get();
        return $query->row_array();
    }

    private function getCustomerNameByID($cust_id){
        $this->db->select('CONCAT(fname," ",lname) AS fullname');
        $this->db->from('customer_data');
        $this->db->where("customer_id",$cust_id);
        $query = $this->db->get();
        return $query->row_array()["fullname"];
    }

    private function getItemTransactionSR($sr_id){
        $this->db->select('*');
        $this->db->from('item_transact_sr');
        $this->db->where("sr_id",$sr_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    private function getJoinedSingleReceiptCustomer($sr_id){
        $cmd =  'SELECT order_no, discount, promo_code, date_transact, CONCAT(fname," ",lname) AS full_name 
                FROM single_receipt INNER JOIN customer_data ON single_receipt.customer_id=customer_data.customer_id WHERE 
                sr_id=? AND db_id=?';

        $query = $this->db->query($cmd,array($sr_id,$_SESSION['db_id']));
        return $query->row_array();
    }

    function retrieveItemTransactionSingle($sr_id){
        $result["discount"] = array();
        $result["item_list"] = array();

        $result["discount"] = $this->getJoinedSingleReceiptCustomer($sr_id);
        $rowData = $this->getItemTransactionSR($sr_id);
        
        $t_qty = 0;
        $t_cost = 0;

        foreach($rowData as $row){
            $item_code = $row["item_code"];
            $uom = $row["uom"];
            $item_desc = $row["description"];
            $qty = $row["qty"];
            $price = $row["unit_price"];

            $result["item_list"][] = array( "item_code" => $item_code, 
                                            "uom" => $uom,
                                            "item_desc" => $item_desc,
                                            "qty" => $qty,
                                            "price" => $price
                                    );

            $t_price = $price*$qty;
            $t_qty += $qty;
            $t_cost += $t_price;
        }

        $discount = $result["discount"]["discount"];
        $d_cost = $t_cost-$discount; // Discounted Total Cost
        $result["discount"]["total_qty"] = $t_qty;
        $result["discount"]["total_cost"] = $t_cost;
        $result["discount"]["d_cost"] = $d_cost;

        return $result;

    }

    function getListOfOrderingNo($selected){
        $this->db->select('order_no');
        $this->db->from('single_receipt');
        $this->db->where("DATE(date_transact) = '$selected'");
        $this->db->where('db_id',$_SESSION['db_id']);
        $query = $this->db->get();
        return $query->result_array();
    }


    // Monitoring List for Discounted Items, items purchased within selected date range
    // Format:
    // Total Quantity, Items No, Brand Name, Generic Name, UOM
    // LO and Acctg: viewing and printing
    // IAD: viewing

    function retrieveDiscountedItemsByDate($start,$end,$db_id){
        if($_SESSION['access_type']!="accounting")
            $db_id = $_SESSION['db_id'];

        $cmd =  "SELECT order_no, item_code, uom, qty, description, generic_name FROM single_receipt INNER JOIN 
                item_transact_sr ON single_receipt.sr_id=item_transact_sr.sr_id 
                WHERE DATE(date_transact) >= ? AND DATE(date_transact) <= ? AND db_id=?";

        $query = $this->db->query($cmd,array($start,$end,$db_id));
        
        if($db_id==0){
            $cmd =  "SELECT order_no, item_code, uom, qty, description, generic_name FROM single_receipt INNER JOIN 
                item_transact_sr ON single_receipt.sr_id=item_transact_sr.sr_id 
                WHERE DATE(date_transact) >= ? AND DATE(date_transact) <= ?";

            $query = $this->db->query($cmd,array($start,$end));
        }

        $result = $query->result_array();
        
        $report = array();
        foreach($result as $row){
            $order_no = $row["order_no"];
            $item_code = $row["item_code"];
            $uom = $row["uom"];
            $qty = $row["qty"];
            $brand = $row["description"];
            $generic = $row["generic_name"];

            $found = false; // If found on array, just updates the qty of that item, otherwise adds a new entry
            for($c=0; $c<count($report); $c++){
                if($report[$c]["item_code"]==$item_code && $report[$c]["uom"]==$uom){
                    $report[$c]["qty"] += $qty;
                    $found = true; 
                    break; // Break the loop
                }
            }

            if(!$found){
                $report[] = array("qty" => $qty, "item_code" => $item_code, "brand" => $brand, "generic" => $generic, "uom" =>$uom);
            }
        }

        return $report;

    }

    // End of Day (EOD) Liquidation Report, summary purchased items on that day
    // Format:
    // Product Name, Transaction Date(current date), Receipt, Qty, Unit, Price, Total (Qty* Price), Branch, Promo Code
    // LO and Acctg: viewing and printing
    // IAD: viewing

    function retrieveSummaryItemsByDate($selected,$db_id){
        if($_SESSION['access_type']!="accounting")
            $db_id = $_SESSION['db_id'];

        if($db_id==19){ // If Alta Citta
            $cmd =  "SELECT order_no, discount, status FROM single_receipt WHERE DATE(date_transact)=? AND db_id=?";

            $query = $this->db->query($cmd,array($selected,$db_id));
            $result = $query->result_array();

            foreach($result as $row){
                $order_no = $row["order_no"];
                $discount_dcms = $row["discount"];
                $status = $row["status"];
                
                $nav_alta = $this->updateFromNavAlta($order_no);
            
                if(count($nav_alta)>0){
                    $transaction_no = $nav_alta["transaction_no"];
                    $discount_nav = $nav_alta["discount_nav"];
                    $variance = $discount_dcms-$discount_nav;

                    if($variance==0 && $status=="unsettled")
                        $status = "unbilled";

                    $this->updateSRReceiptNo($order_no,$transaction_no,$discount_nav,$status,$order_no); 
                    // Order No. and Receipt No. are the same for Alta Citta
                }   
                
            }
        }

    
        $cmd =  "SELECT sr_id, order_no, receipt_no, promo_code, discount, department, single_receipt.db_id AS dbid 
                FROM single_receipt INNER JOIN cupon_users ON single_receipt.transact_by=cupon_users.user_id
                WHERE DATE(date_transact) = ? AND single_receipt.db_id=?";

        $query = $this->db->query($cmd,array($selected,$db_id));
        
        if($db_id==0){
            $cmd =  "SELECT sr_id, order_no, receipt_no, promo_code, discount, department, single_receipt.db_id AS dbid 
                FROM single_receipt INNER JOIN cupon_users ON single_receipt.transact_by=cupon_users.user_id
                WHERE DATE(date_transact) = ?";

            $query = $this->db->query($cmd,array($selected));
        }

        $result = $query->result_array();
        $report = array();

        foreach($result as $row){
            $sr_id = $row["sr_id"];
            $order_no = $row["order_no"];
            $receipt = $row["receipt_no"];
            $promo_code = $row["promo_code"];
            $discount = $row["discount"];
            $branch = $this->getStoreByDB($row["dbid"])."-".$row["department"];

            $entries = $this->retrieveSummaryItemEntries($sr_id,$order_no,date('m/d/Y',strtotime($selected)),$receipt,$branch,$promo_code,$discount);
            $report = array_merge($report,$entries);
        }

        return $report;

    }

    private function retrieveSummaryItemEntries($sr_id,$order_no,$transaction_date,$receipt,$branch,$promo_code,$discount){
        $cmd =  "SELECT item_code, uom, description, qty, unit_price FROM item_transact_sr WHERE sr_id=?";
       
        $query = $this->db->query($cmd,array($sr_id));
        $result = $query->result_array();
        
        $report = array();
        $overall_total = 0;
        $t_qty = 0;

        foreach($result as $key => $row){
            $item_code = $row["item_code"];
            $uom = $row["uom"];
            $product_name = $row["description"];
            $qty = $row["qty"];
            $price = $row["unit_price"];
            $total = ($price*$qty);
            $t_qty += $qty;
            $overall_total += $total;

            $report[] = array("product_name" => $product_name, "transaction_date" => $transaction_date, "receipt" => $receipt, "qty" => $qty, "unit" => $uom, "price" => $price, "total" => $total, "branch" => $branch, "promo_code" => $promo_code);

            if($key==count($result)-1){
                $report[] = array("product_name" => "Discount", "transaction_date" => $discount, "receipt" => "", "qty" => $t_qty, "unit" => "", "price" => "", "total" => $overall_total, "branch" => ($overall_total-$discount), "promo_code" => $promo_code);
            }
        }

        return $report;

    }

    // Alta Citta Database 2
    private function updateFromNavAlta($order_no){ // Navision
        $alta_details = $this->setUpNavConnectAlta();
        $tender_type = $this->getTenderTypeSR();

        $table = '['.$alta_details['sub_db_name'].'$Trans_ Payment Entry]';
        $connect = odbc_connect($alta_details['db_name'], $alta_details['username'], $alta_details['password']);

        $table_query = "SELECT [Transaction No_] as t_no, [Amount Tendered] as amt FROM ".$table." WHERE 
                        [Tender Type]=? AND [Receipt No_]=?";
        
        $result = odbc_prepare($connect, $table_query);
        odbc_execute($result, array($tender_type,$order_no));
        
        $result_arr = array();
        if($row = odbc_fetch_array($result)) {
            $result_arr["transaction_no"] = $row["t_no"];
            $result_arr["discount_nav"] = $row["amt"];
        }

        odbc_free_result($result);
        odbc_close($connect);
        return $result_arr;
    }

    // End of Month (EOM) Liquidation Report
    // Format:
    // Product Name, Transaction Date, Receipt, Qty, Unit, Price, Total (Qty* Price), Branch, Promo Code
    // LO and Acctg: viewing and printing
    // IAD: viewing

    function retrieveSummaryItemsByMonth($start,$end,$db_id){
        if($_SESSION['access_type']!="accounting")
            $db_id = $_SESSION['db_id'];

        $cmd =  "SELECT sr_id, order_no, receipt_no, promo_code, discount, date_transact, department, 
                single_receipt.db_id AS dbid FROM single_receipt INNER JOIN cupon_users ON 
                single_receipt.transact_by=cupon_users.user_id 
                WHERE DATE(date_transact) >= ? AND DATE(date_transact) <= ? AND single_receipt.db_id=?";

        $query = $this->db->query($cmd,array($start,$end,$db_id));
        
        if($db_id==0){
            $cmd =  "SELECT sr_id, order_no, receipt_no, promo_code, discount, date_transact, department, 
                single_receipt.db_id AS dbid FROM single_receipt INNER JOIN cupon_users ON 
                single_receipt.transact_by=cupon_users.user_id 
                WHERE DATE(date_transact) >= ? AND DATE(date_transact) <= ?";

            $query = $this->db->query($cmd,array($start,$end));
        }

        $result = $query->result_array();
        $report = array();

        foreach($result as $row){
            $sr_id = $row["sr_id"];
            $order_no = $row["order_no"];
            $receipt = $row["receipt_no"];
            $promo_code = $row["promo_code"];
            $discount = $row["discount"];
            $date_transact = date('m/d/Y',strtotime($row["date_transact"]));
            $branch = $this->getStoreByDB($row["dbid"])."-".$row["department"];

            $entries = $this->retrieveSummaryItemEntries($sr_id,$order_no,$date_transact,$receipt,$branch,$promo_code,$discount);
            $report = array_merge($report,$entries);
        }

        return array(count($result),$report);

    }


    // Variance Report (Inhouse vs. Navision)
    // LO and Acctg: viewing and printing
    // IAD: viewing 
    // Format:
    // Transaction Date, Order No., Qty., Product, Discounted Price(DCMS), Discounted Price(Nav), Variance 
    // Transaction Date, Order No., Qty., Product, Discount (DCMS), Discount(Nav), Variance 
    // Transaction Date, Order No., Discount (DCMS), Discount(Nav), Variance 

    function retrieveVariance($start,$end,$db_id){
        if($_SESSION['access_type']!="accounting")
            $db_id = $_SESSION['db_id'];

        $cmd =  "SELECT date_transact, order_no, discount, discount_nav FROM single_receipt 
                WHERE DATE(date_transact) >= ? AND DATE(date_transact) <= ? AND db_id=?";
      
        $query = $this->db->query($cmd,array($start,$end,$db_id));
        
        if($db_id==0){
            $cmd =  "SELECT date_transact, order_no, discount, discount_nav FROM single_receipt 
                WHERE DATE(date_transact) >= ? AND DATE(date_transact) <= ?";
      
            $query = $this->db->query($cmd,array($start,$end));
        }

        $result = $query->result_array();
        $report = array();

        foreach($result as $row){
            $date_transact = date('m/d/Y',strtotime($row["date_transact"]));
            $order_no = $row["order_no"];
            $discount_dcms = $row["discount"];
            $discount_nav = $row["discount_nav"];
            $variance = $discount_dcms-$discount_nav;
            
            $report[] = array("transaction_date" => $date_transact, "order_no" => $order_no, "discount_dcms" => $discount_dcms, "discount_nav" => $discount_nav, "variance" => $variance);
        }

        return $report;

    }


    // Billing

    function retrieveSingleReceiptForBilling($start,$end,$status,$db_id){
        if($_SESSION['access_type']!="accounting")
            $db_id = $_SESSION['db_id'];    

        $cmd =  "SELECT sr_id, status, date_transact, receipt_no, order_no, discount, discount_nav FROM 
                single_receipt WHERE DATE(date_transact) >= ? AND DATE(date_transact) <= ? AND status=? 
                AND db_id=?";
      
        $query = $this->db->query($cmd,array($start,$end,$status,$db_id));
        
        if($db_id==0){
            $cmd =  "SELECT sr_id, status, date_transact, receipt_no, order_no, discount, discount_nav FROM 
                single_receipt WHERE DATE(date_transact) >= ? AND DATE(date_transact) <= ? AND status=?";
      
            $query = $this->db->query($cmd,array($start,$end,$status));
        }

        $result = $query->result_array();
        $report = array();

        foreach($result as $row){
            $sr_id = $row["sr_id"];
            $status = $row["status"];
            $date_transact = date('m/d/Y',strtotime($row["date_transact"]));
            $receipt = $row["receipt_no"];
            $order_no = $row["order_no"];
            $discount_mudc = $row["discount"];
            $discount_nav = $row["discount_nav"];
            $total = $this->getTotalAmountFromTransaction($sr_id);
            
            $report[] = array("sr_id" => $sr_id, "status" => $status, "transaction_date" => $date_transact, "receipt" => $receipt, "order_no" => $order_no, "discount_mudc" => $discount_mudc, "discount_nav" => $discount_nav, "total" => $total);
        }

        return $report;

    }

    private function getTotalAmountFromTransaction($sr_id){
        $total = 0;
        $cmd =  "SELECT SUM(unit_price*qty) as total FROM item_transact_sr WHERE sr_id=?";
      
        $query = $this->db->query($cmd,array($sr_id));
        $row = $query->row_array();
        if(isset($row))
            $total = $row["total"];

        return $total;
    }

    function getBatches(){
        $this->db->select('*');
        $this->db->from('promo_billing_batch_sr');
        $this->db->where("user_id",$_SESSION['user_id']);
        $query = $this->db->get();
        $result = $query->result_array();

        $report = array();
        foreach($result as $row){
            $batch_id = $row["batch_id"];
            $batch_no = date('Ymd',strtotime($row["date_generated"]))."-".$row["batch_id"];
            $from = date('m/d/Y',strtotime($row["from_date"]));
            $to = date('m/d/Y',strtotime($row["to_date"]));
            $invoice = $row["paid_invoice_number"];
            $date_extract = $row["date_extracted"];

            $report[] = array("batch_id" => $batch_id, "batch_no" => $batch_no, "from_date" => $from, "to_date" => $to, "paid_invoice_number" => $invoice, "date_extract" => $date_extract);

        }

        return $report;
    }

    function retrieveBillingBatchBySR($batch_id){
        $cmd =  "SELECT sr_id, order_no, receipt_no, promo_code, discount, date_transact, from_date, to_date, 
                paid_invoice_number, date_generated, a.department AS dept_acc, b.department AS dept_user, 
                single_receipt.db_id AS dbid FROM single_receipt 
                INNER JOIN promo_billing_batch_sr ON single_receipt.batch_id=promo_billing_batch_sr.batch_id 
                INNER JOIN cupon_users a ON promo_billing_batch_sr.user_id=a.user_id
                INNER JOIN cupon_users b ON single_receipt.transact_by=b.user_id
                WHERE single_receipt.batch_id=? AND promo_billing_batch_sr.user_id=?";
  
        $comp_details = $this->getCompanyDetails(); 
        $promo_code_count = $this->getPromoCodeCountByBatch($batch_id);      
        $query = $this->db->query($cmd,array($batch_id,$_SESSION['user_id']));
        $result = $query->result_array();
        
        $db_id = (count($result)>0) ? $result[0]["dbid"] : 13;
        $store_details = $this->getStoreDetails($db_id);
        $total_discount = 0;
        $dept = '';
        $report = array();
        $report["list"] = array();

        foreach($result as $row){
            $from = $row["from_date"];
            $to = $row["to_date"];
            $invoice = $row["paid_invoice_number"];
            $date_generated = $row["date_generated"];
            $dept = $row["dept_acc"]; // Accounting User's Dept
            $sr_id = $row["sr_id"];
            $order_no = $row["order_no"];
            $receipt = $row["receipt_no"];
            $promo_code = $row["promo_code"];
            $discount = $row["discount"];
            $date_transact = date('m/d/Y',strtotime($row["date_transact"]));
            $branch = $this->getStoreByDB($row["dbid"])."-".$row["dept_user"]; // Order User's Dept
            $total_discount += $discount;

            $entries = $this->retrieveSummaryItemEntries($sr_id,$order_no,$date_transact,$receipt,$branch,$promo_code,$discount);
            $report["list"] = array_merge($report["list"],$entries);
        }

        $report["head"] = array(
                                "logo_path1" => $store_details["image_logo_path"], 
                                "store_address" => $store_details["address_location"], 
                                "contact" => $store_details["contact_no"], 
                                "manager" => $store_details["manager"], 
                                "supervisor" => $store_details["supervisor"], 
                                "supervisor_position" => $store_details["supervisor_position"], 
                                "doctor" => $store_details["doctor"], 
                                "doctor_position" => $store_details["doctor_position"], 
                                "company_name" => $comp_details["company_name"], 
                                "company_address" => $comp_details["address"], 
                                "promo_count" => $promo_code_count, 
                                "logo_path2" => $comp_details["image_logo_path"], 
                                "dept" => $dept, 
                                "from_date" => $from, 
                                "to_date" => $to, 
                                "invoice" => $invoice, 
                                "date_generated" => $date_generated, 
                                "total_discount" => $total_discount
                            );

        return $report;

    }
    

    //MPDI
    function retrieveSummaryItemsByMpdi($start,$end,$db_id){
        if($db_id!=0){
            $cmd =  "SELECT sr_id, order_no, receipt_no, promo_code, discount, date_transact, department, 
                    single_receipt.db_id AS dbid FROM single_receipt INNER JOIN cupon_users ON 
                    single_receipt.transact_by=cupon_users.user_id WHERE 
                    DATE(date_transact) >= ? AND DATE(date_transact) <= ? AND single_receipt.db_id=?";
            // $params = array("billed",$start,$end,$db_id); status=? AND
            $params = array($start,$end,$db_id);
        }else{
            $cmd =  "SELECT sr_id, order_no, receipt_no, promo_code, discount, date_transact, department, 
                    single_receipt.db_id AS dbid FROM single_receipt INNER JOIN cupon_users ON 
                    single_receipt.transact_by=cupon_users.user_id WHERE 
                    DATE(date_transact) >= ? AND DATE(date_transact) <= ?";
            // $params = array("billed",$start,$end); status=? AND
            $params = array($start,$end);
        }
     
        $query = $this->db->query($cmd,$params);
        $result = $query->result_array();
        
        $report = array();
        foreach($result as $row){
            $sr_id = $row["sr_id"];
            $order_no = $row["order_no"];
            $receipt = $row["receipt_no"];
            $promo_code = $row["promo_code"];
            $discount = $row["discount"];
            $date_transact = date('m/d/Y',strtotime($row["date_transact"]));
            $branch = $this->getStoreByDB($row["dbid"])."-".$row["department"];

            $entries = $this->retrieveSummaryItemEntries($sr_id,$order_no,$date_transact,$receipt,$branch,$promo_code,$discount);
            $report = array_merge($report,$entries);
        }

        return $report;

    }

    private function getStoreByDB($db_id){
        $store = "";
        $this->mpdi = $this->load->database('mpdi', TRUE);
        $this->mpdi->select('store');
        $this->mpdi->from('database');
        $this->mpdi->where('db_id',$db_id);
        $query = $this->mpdi->get();
        $row = $query->row_array();
        if(isset($row)){
            $store = $row["store"];
            if($db_id==15){ // If Dumars
                $store = $row["store"]."-DUM";
            }    
        }
        return $store;
    }

    function retrieveBillingByMpdi($start,$end){
        $cmd =  "SELECT batch_id, from_date, to_date, paid_invoice_number, date_generated, date_extracted 
                FROM promo_billing_batch_sr WHERE DATE(date_generated) >= ? AND DATE(date_generated) <= ?";
        
        $params = array($start,$end);       
        $query = $this->db->query($cmd,$params);
        $result = $query->result_array();

        $report = array();
        foreach($result as $row){
            $batch_id = $row["batch_id"];
            $batch_no = date('Ymd',strtotime($row["date_generated"]))."-".$row["batch_id"];
            $from = date('m/d/Y',strtotime($row["from_date"]));
            $to = date('m/d/Y',strtotime($row["to_date"]));
            $invoice = $row["paid_invoice_number"];
            $status = "FOR BILLING";

            if(!empty($row["date_extracted"]) && empty($invoice)){ // If date extracted and no invoice
                $status = "BILLED";
            }else if(!empty($row["date_extracted"]) && !empty($invoice)){ // If date extracted and invoiced
                $status = "PAID";
            }
            
            $report[] = array("batch_id" => $batch_id, "batch_no" => $batch_no, "from_date" => $from, "to_date" => $to, "paid_invoice_number" => $invoice, "status" => $status);
        }
        
        return $report;
    }

    function retrieveBillingBatchByMpdi($batch_id){
        $cmd =  "SELECT sr_id, order_no, receipt_no, promo_code, discount, date_transact, from_date, to_date, 
                paid_invoice_number, date_generated, department, single_receipt.db_id AS dbid FROM single_receipt INNER JOIN 
                promo_billing_batch_sr ON single_receipt.batch_id=promo_billing_batch_sr.batch_id 
                INNER JOIN cupon_users ON single_receipt.transact_by=cupon_users.user_id WHERE single_receipt.batch_id=?";
    
        $query = $this->db->query($cmd,array($batch_id));
        $result = $query->result_array();
    
        $report = array();

        foreach($result as $row){
            $from = $row["from_date"];
            $to = $row["to_date"];
            $invoice = $row["paid_invoice_number"];
            $date_generated = $row["date_generated"];
            $sr_id = $row["sr_id"];
            $order_no = $row["order_no"];
            $receipt = $row["receipt_no"];
            $promo_code = $row["promo_code"];
            $discount = $row["discount"];
            $date_transact = date('m/d/Y',strtotime($row["date_transact"]));
            $branch = $this->getStoreByDB($row["dbid"])."-".$row["department"];

            $entries = $this->retrieveSummaryItemEntries($sr_id,$order_no,$date_transact,$receipt,$branch,$promo_code,$discount);
            $report = array_merge($report,$entries);
        }

        return $report;

    }

    function getBatchNumber($batch_id){
        $cmd =  "SELECT batch_id, date_generated FROM promo_billing_batch_sr WHERE batch_id=?";
    
        $query = $this->db->query($cmd,array($batch_id));
        $row = $query->row_array();
        $batch_no = "";
        if(isset($row)){
            $batch_no = date('Ymd',strtotime($row["date_generated"]))."-".$row["batch_id"];
        }

        return $batch_no;
    }

}