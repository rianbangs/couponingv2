<?php 

class Sr_ctrl extends CI_Controller
{
     function __construct()
     {
        parent::__construct();
        $this->load->library('session');
         $this->load->model("Cupon_mod");
         $this->load->model("Dummy_mod");
     }

      public function test()
     {
          echo "hello world";
     }

}