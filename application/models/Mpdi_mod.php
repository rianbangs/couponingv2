<?php

class Mpdi_mod extends CI_Model
{
	function __construct()
    {
        parent::__construct();
        //$this->db2 = $this->load->database('hr', TRUE);
       
    }



    function get_user($username)
    {
        $query = $this->db->query("
                                         SELECT 
                                                 * 
                                         FROM 
                                                 users 
                                         WHERE 
                                                 username = '".$username."'        

                                  ");
        
        return $query->result_array();
    }

    function get_connection()
    {
        $query = $this->db->query("
                                        SELECT 
                                                *
                                        FROM 
                                                `database` 
                                        WHERE 
                                                db_id = '2'        
                                 ");
        return $query->result_array();
    }

    function get_generic_name($item_code)
    {
        $query = $this->db->query("
                                        SELECT 
                                                *
                                        FROM 
                                                item_generic
                                        WHERE 
                                                item_code = '".$item_code."'       
                                  ");
        return $query->result_array();
    }

    function get_header($Document_num)
    {
        $query = $this->db->query("
                                         SELECT 
                                                 *
                                         FROM 
                                                 sales_header  
                                         WHERE 
                                                 No_ = '".$Document_num."'                       
                                 ");
        return $query->result_array();
    }


    function get_pad_id($pad_id)
    {
        $query = $this->db->query("
                                        SELECT 
                                                * 
                                        FROM 
                                                sales_header
                                        WHERE 
                                                pad_id = '".$pad_id."'                        
                                 ");
        
        return $query->result_array();
    }

    function check_si_header($Document_no)
    {
         $this->db->select("*");
         $this->db->from("sales_header");
         $this->db->where("No_",$Document_no);
         $query = $this->db->get();
         return $query->result_array();
    }




    function insert_header($Document_no,$sell_to_customer_no,$bill_to_name,$address,$salesman_code,$account_type,$posting_date,$due_date,$batch_no,$ext_doc_no,$salesman_name,$pad_id,$status)
    {
        $this->db->query("
                               INSERT INTO 
                                           sales_header
                                                        (
                                                            No_,    
                                                            sell_to_customer_no,
                                                            bill_to_name,
                                                            address, 
                                                            salesman_code,
                                                            account_type,
                                                            posting_date,
                                                            due_date, 
                                                            batch_no, 
                                                            ext_doc_no, 
                                                            salesman_name, 
                                                            pad_id, 
                                                            status    
                                                        )   
                               VALUES 
                                                        (
                                                             '".$Document_no ."',    
                                                             '".$sell_to_customer_no."',
                                                             '".$bill_to_name."',
                                                             '".$address."', 
                                                             '".$salesman_code."',
                                                             '".$account_type."',
                                                             '".$posting_date."',
                                                             '".$due_date."', 
                                                             '".$batch_no."', 
                                                             '".$ext_doc_no."', 
                                                             '".$salesman_name."', 
                                                             '".$pad_id."', 
                                                             '".$status."'            
                                                        )              
                         ");
    }


    function check_sales_invoice_line($Document_No_,$Quantity,$Item_no,$Description,$Lot_no,$Expiry_date,$Deal,$Line_discount,$unit_price,$Line_disc_amount,$Amount,$Unit_of_Measure,$Net_price,$get_pad_number)
    {
        //$this->db->select('*');
        //$this->db->from("sales_invoice_line");
        //$this->db->where("Document_No_",$Document_No);       
        //$this->db->where('Quantity',$Quantity);
        //$this->db->where('Item_no',$Item_no);
        //$this->db->where('Description',$Description);
        //$this->db->where('Lot_no',$Lot_no);
        //$this->db->where('Expiry_date',$Expiry_date);
        //$this->db->where('Deal',$Deal);
        //$this->db->where('Line_discount',$Line_discount);
        //$this->db->where('unit_price',$unit_price);
        //$this->db->where('Line_disc_amount',$Line_disc_amount);
        //$this->db->where('Amount',$Amount);
        //$this->db->where('Unit_of_Measure',$Unit_of_Measure);
        //$this->db->where('Net_price',$Net_price);
        //$this->db->where('line_pad_id',$get_pad_number);
        //$query = $this->db->get();
        //var_dump($query->result_array());

        $query = $this->db->query('Select 
                                           * 
                                   FROM 
                                         sales_invoice_line 
                                   WHERE
                                          Document_No_ = "'.$Document_No_.'"
                                      AND 
                                          Quantity     = "'.$Quantity.'"  
                                      AND 
                                          Item_no      = "'.$Item_no.'"
                                     AND                                       
                                          Lot_no       = "'.$Lot_no.'"   
                                     AND 
                                          Expiry_date  = "'.$Expiry_date.'"  
                                     AND 
                                          Deal         = "'.$Deal.'" 
                                     AND 
                                          Line_discount = "'.$Line_discount.'" 
                                     AND 
                                          unit_price    = "'.$unit_price.'"
                                     AND 
                                          Line_disc_amount = "'.$Line_disc_amount.'"          
                                     AND 
                                          Amount           = "'.$Amount.'"  
                                     AND 
                                          Unit_of_Measure = "'.$Unit_of_Measure.'"     
                                               ');

        
        return $query->result_array();
    }

   function insert_sales_invoice_line($Document_No_,$Quantity,$Item_no,$Description,$Lot_no,$Expiry_date,$Deal,$Line_discount,$unit_price,$Line_disc_amount,$Amount,$Unit_of_Measure,$Net_price,$get_pad_number)
   {
        //var_dump($Document_No_,$Quantity,$Item_no,$Description,$Lot_no,$Expiry_date,$Deal,$Line_discount,$unit_price,$Line_disc_amount,$Amount,$Unit_of_Measure,$Net_price,$get_pad_number); 
        
        $this->db->set('Document_No_',$Document_No_);
        $this->db->set('Quantity',$Quantity);
        $this->db->set('Item_no',$Item_no);
        $this->db->set('Description',$Description);
        $this->db->set('Lot_no',$Lot_no);
        $this->db->set('Expiry_date',$Expiry_date);
        $this->db->set('Deal',$Deal);
        $this->db->set('Line_discount',$Line_discount);
        $this->db->set('unit_price',$unit_price);
        $this->db->set('Line_disc_amount',$Line_disc_amount);
        $this->db->set('Amount',$Amount);
        $this->db->set('Unit_of_Measure',$Unit_of_Measure);
        $this->db->set('Net_price',$Net_price);
        $this->db->set('line_pad_id',$get_pad_number);
        $this->db->insert('sales_invoice_line');

      /*  $query = $this->db->query("
                                        INSERT INTO
                                                     sales_invoice_line
                                                                        (
                                                                            Document_No_,                                                                           
                                                                            Quantity,
                                                                            Item_no,
                                                                            Description,
                                                                            Lot_no, 
                                                                            Expiry_date,
                                                                            Deal,
                                                                            Line_discount,
                                                                            unit_price,
                                                                            Line_disc_amount,
                                                                            Amount,
                                                                            Unit_of_Measure,
                                                                            Net_price,                                                                           
                                                                            line_pad_id                                                                                
                                                                        )   
                                        VALUES  
                                                                        (
                                                                            '".$Document_No_."',                                                                            
                                                                            '".$Quantity."',
                                                                            '".$Item_no."',
                                                                            ".'"'.$Description.'"'.",
                                                                            '".$Lot_no."', 
                                                                            '".$Expiry_date."',
                                                                            '".$Deal."', 
                                                                            '".$Line_discount."',
                                                                            '".$unit_price."',
                                                                            '".$Line_disc_amount."',
                                                                            '".$Amount."',
                                                                            '".$Unit_of_Measure."',
                                                                            '".$Net_price."',                                                                            
                                                                            '".$get_pad_number."'
                                                                                                                                                       
                                 ");*/
   }



   function check_sales_footer($Document_no)
   {
        $this->db->select('*');
        $this->db->from('sales_footer');
        $this->db->where('Document_no',$Document_no);
        $query = $this->db->get();
        return $query->result_array();
   }


   function insert_sales_footer($Document_no,$vatable_sales,$vat_exempt_sales,$zero_rate_sales,$vat_amount,$total_sales,$less_vat,$net_of_vat,$pwd_disc,$amount_due,$add_vat,$total_amount_due)
   {
        $this->db->set('Document_no',$Document_no);
        $this->db->set('vatable_sales',$vatable_sales);
        $this->db->set('vat_exempt_sales',$vat_exempt_sales);
        $this->db->set('zero_rate_sales',$zero_rate_sales);
        $this->db->set('vat_amount',$vat_amount);
        $this->db->set('total_sales',$total_sales);
        $this->db->set('less_vat',$less_vat);
        $this->db->set('net_of_vat',$net_of_vat);
        $this->db->set('pwd_disc',$pwd_disc);
        $this->db->set('amount_due',$amount_due);
        $this->db->set('add_vat',$add_vat);
        $this->db->set('total_amount_due',$total_amount_due);
        $this->db->insert('sales_footer');

   }


   // Gershom 
   function addUserAccount($fn,$ln,$user,$pass){
        $query = $this->db->query("insert into users (first_name,last_name,username,password) values (?,?,?,?);", array($fn,$ln,$user,$pass));

        return $query;
   }

   function getUsernameCount($user){
        $query = $this->db->query("select * from users where username=?;", array($user));
        return $query->num_rows(); 
   }

   function getUserCountById($id){
        $query = $this->db->query("select * from users where idusers=?;", array($id));
        return $query->num_rows(); 
   }

   function retrieveAccountID($user,$pass){
        $query = $this->db->query("select password from users where username=?;", array($user));
        $row = $query->row_array();
        if(isset($row)){
                if(password_verify($pass,$row["password"])){
                        $query = $this->db->query("select idusers from users where username=?;", array($user));
                        $row = $query->row_array();
                        
                        if (isset($row)){
                                return $row["idusers"];
                        }

                        return 0;  
                }    
        }
        
        return 0;
   }

   function getUserType($id){
        $query = $this->db->query("select user_type from users where idusers=?;", array($id));
        $row = $query->row_array();
        if(isset($row)){
                return $row["user_type"];
        }

        return 0;
   }

   function retrieveUsers(){
        $user_arr = array();
        $query = $this->db->query("select first_name, last_name, user_type from users;");
        foreach ($query->result_array() as $row){
                array_push($user_arr,'{"fn": "'.$row["first_name"].'","ln": "'.$row["last_name"].'","ut": "'.$row["user_type"].'"}');
        }

        return $user_arr;
   }

   function retrieveUser($id){
        $user_arr = array();
        $query = $this->db->query("select * from users where idusers=?;", array($id));
        $row = $query->row_array();
        if(isset($row)){
                array_push($user_arr,$row["idusers"],$row["First_name"],$row["Last_name"],$row["username"],$row["password"],$row["user_type"]);
        }

        return $user_arr;
   }

   function updateUser($userpass,$id){ //userpass is an array.
        $setVal = "";
        foreach($userpass as $key => $val) {
                if($setVal!="")
                        $setVal.= ", ";

                $setVal.= $key."='".$val."'";
        }
        
        $query = $this->db->query("update users set ".$setVal." where idusers=?;", array($id));
        return $query;
        //return $setVal;
   }
}   
