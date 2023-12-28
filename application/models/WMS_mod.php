<?php 

class WMS_mod extends CI_model
{
	function __construct()
    {
        parent::__construct();
        //$this->db2 = $this->load->database('hr', TRUE);
       
    }

    function get_connection()
    {
        $query = $this->db->query("
                                        SELECT 
                                                *
                                        FROM 
                                                `database` 
                                        WHERE 
                                                db_id = '3'        
                                 ");
        return $query->result_array();
    } 
}