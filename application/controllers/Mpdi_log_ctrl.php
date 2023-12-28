<?php
/**
 * 
 */
class Mpdi_log_ctrl extends CI_Controller
{
    
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('simplify/simplify','simplify');
        $this->load->model('simplify/pdf_simplify','pdf_');
        $this->load->model('Mpdi_mod');

        if(isset($_SESSION['user_id'])){
            if($this->Mpdi_mod->getUserCountById($_SESSION['user_id'])>0){
                if($this->Mpdi_mod->getUserType($_SESSION['user_id'])=="Admin")
                    redirect(base_url('Mpdi_ctrl/usersPage'));
                else
                    redirect(base_url('Mpdi_ctrl/mpdi_ui'));
            }else
                unset($_SESSION['user_id']);

            
        }

    }

    public function index(){
        $this->load->view("mpdi/logged_head_ui");
        $this->load->view("mpdi/login_body");
    }

    public function login(){
        $user = $_POST["user"];
        $pass = $_POST["pass"];

        $id = $this->Mpdi_mod->retrieveAccountID($user,$pass);

        if($id<1)
            echo json_encode(array("Error Credentials!","Error"));
        else{
            $_SESSION['user_id'] = $id;
            echo json_encode(array("Successfully Login!","Success"));
        }     
    }


}
?>