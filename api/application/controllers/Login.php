<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
    {	
    	header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('download');
        $this->load_models();
    }

    public function load_models()
    {	
    	$this->load->database();

        $models = array(
            'GL_Accounts_Model' => 'GL_Accounts_Model',
            'Product_Category_Model' => 'Product_Category_Model',
            'Item_Model' => 'Item_Model',
            'User_Model' => 'User_Model',
            'History_Model' => 'History_Model'
        );

        $this->load->model($models);  
    }

	public function index()
	{		
		if ($this->session->userdata('logged_in') == true) 
		{
			$arr = $this->Product_Category_Model->display_active_product_menus();

			$this->jsonify(array(
				'login' => true, 
				'menu'	 => $arr,
				'data' => $this->session->userdata('logged_in'),
				'userdata' => $this->session->all_userdata(),
				'message' => 'Already logged_in! Redirecting to homepage.'
			));	
		}
		else 
		{
			$this->jsonify(array(
				'login' => false,
				'data' => $this->session->userdata('logged_in'), 
				'userdata' => $this->session->all_userdata(),
				'message' => 'Login attempt failed! Username or password wrong!', 
			));	
		}
	}

	public function auth()
	{
		$arr = $this->Product_Category_Model->display_active_product_menus();

		$this->jsonify(array(
			'login' => true, 
			'menu'	 => $arr,
			'data' => $this->session->userdata('logged_in'),
			'userdata' => $this->session->all_userdata(),
			'message' => 'Already logged_in! Redirecting to homepage.'
		));	
	}

	public function check()
	{
		$_POST = json_decode(file_get_contents('php://input'), true);
		$posts = $this->input->post();

		$username = $this->security->xss_clean($this->input->post('username'));
        $password = $this->security->xss_clean($this->input->post('password'));

		if( $login = $this->User_Model->validate($username, $password) ) {
            if( null !== $this->session->userdata('referred_from') ) {
                redirect($this->session->userdata('referred_from'), 'refresh');
            } else { 
                $arr = $this->Product_Category_Model->display_active_product_menus();

				$this->jsonify(array(
					'login' => true, 
					'menu' => $arr,
					'user_id' => $this->session->userdata('user_id'),
	                'firstname' => $this->session->userdata('firstname'),
	                'middlename' => $this->session->userdata('middlename'),
	                'lastname' => $this->session->userdata('lastname'),
	                'username' => $this->session->userdata('username'),
	                'email' => $this->session->userdata('email'),
	                'role' => $this->session->userdata('role'),
					'message' => 'Login attempt successful! Redirecting to your home.', 
				));	

				exit;
            }
        }

		$this->jsonify(array(
			'login' => false, 
			'username' => $username,
			'password' => $password,
			'datas' => $this->User_Model->validate($username, $password),
			'message' => 'Login attempt failed! Username or password wrong!', 
		));	
	}

	public function logout()
    {   
        $this->session->sess_destroy();  
        $this->jsonify(array(
			'login' => false, 
			'message' => 'Login attempt failed! Username or password wrong!', 
		));	      
    }

	public function jsonify($data)
	{
		print_r(json_encode($data));
	}
}
