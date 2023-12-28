<?php 

class Acctg_ctrl extends CI_Controller
{
	 function __construct()
     {
        parent::__construct();
        $this->load->library('session');

     }


     function dashboard()
     {
     	 $this->load->view('Dashboard');
     }

    

}