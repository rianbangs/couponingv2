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



   function insert_sales_invoice_line($Document_No_,$Quantity,$Item_no,$Description,$Lot_no,$Expiry_date,$Deal,$Line_discount,$unit_price,$Line_disc_amount,$Amount,$Unit_of_Measure,$Net_price,$get_pad_number)
   {
        $query = $this->db->query("
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
                                                                                                                                                       
                                 ");
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
