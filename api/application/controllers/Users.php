<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

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
            'Roles_Model' => 'Roles_Model',
            'User_Model' => 'User_Model',
            'Profile_Model' => 'Profile_Model',
            'History_Model' => 'History_Model',
            'Secret_Model' => 'Secret_Model',
            'User_Roles_Model' => 'User_Roles_Model'
        );

        $this->load->model($models);  
    }

	public function index($id = null)
	{	
		if( $this->input->method() ) 
        {
			$wildcard = $this->input->get("search");
			$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
			$limit   = 5 == -1 ? 0 : 5;
			$page    = $current !== null ? $current : 1;
			$start_from   = ($page-1) * $limit;

			$data['data'] = $this->User_Model->get_all_users_list($wildcard, $limit, $start_from);
			$data['total'] = $this->User_Model->get_all_users_pagination($wildcard);

			echo json_encode($data);
		}
	}

	public function archived($id = null)
	{	
		if( $this->input->method() ) 
        {
			$wildcard = $this->input->get("search");
			$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
			$limit   = 5 == -1 ? 0 : 5;
			$page    = $current !== null ? $current : 1;
			$start_from   = ($page-1) * $limit;

			$data['data'] = $this->User_Model->get_all_archived_users_list($wildcard, $limit, $start_from);
			$data['total'] = $this->User_Model->get_all_archived_users_pagination($wildcard);

			echo json_encode($data);
		}
	}
	
	public function create($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->User_Model->check_last_inserted();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$user = array(
			        'profile_id' => $this->input->post('profile_id'),
			        'username' => $this->input->post('username'),
			        'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
			        'secret_id' => $this->input->post('secret_id'),
			        'secret_password' => password_hash($this->input->post('secret_password'), PASSWORD_BCRYPT),
			        'created_by' => 1,
                    'created_at' => $timestamp
			    );

			    $user_id = $this->User_Model->insert($user);

			    $user_privilege = array(
			    	'user_id' => $user_id,
			    	'role_id' => $this->input->post('role_id')
			    );

			    $user_role_id = $this->User_Roles_Model->insert($user_privilege);

			    $history = array(
                    'history_logs' => 'has created a user',
                    'history_details' => 
					'{
						id: "' . $user_id . '",
						username: "' . $this->input->post('username') . '",
						password: "' . $this->input->post('password') . '",
						secret question: "' . $this->Secret_Model->get_secret_question_by_id($this->input->post('secret_id')) . '",
						secret password: "' . $this->input->post('secret_password') . '",
						profile: "' . $this->Profile_Model->get_profile_by_id($this->input->post('profile_id')) . '",
						privilege: "' . $this->Roles_Model->get_role_by_id($this->input->post('role_id')) . '"
					}',
                    'history_table' => 'user',
                    'history_table_id' => $user_id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('user/view/'.$user_id)
                );

                $history_id = $this->History_Model->insert($history);

			    $q = $this->db->get_where('user', array('id' => $user_id));

                $data = array(
                	'id'      => $user_id,
                	'datas'   => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The user was been successfully saved.',
	                'type'    => 'success'
	            );

	            echo json_encode( $data ); exit();
	    	}
	    }
	}

	public function update($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->User_Model->check_last_inserted();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$user = array(
			        'profile_id' => $this->input->post('profile_id'),
			        'username' => $this->input->post('username'),
			        'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
			        'secret_id' => $this->input->post('secret_id'),
			        'secret_password' => password_hash($this->input->post('secret_password'), PASSWORD_BCRYPT),
			        'created_by' => 1,
                    'created_at' => $timestamp
			    );

			    $this->User_Model->modify($user, $id);

			    $role_id = $this->User_Roles_Model->check_privilege_by_user($id);

			    if($role_id != $this->input->post('role_id'))
			    {	
			    	$removed = $this->User_Roles_Model->remove_user_current_privilege($id);

			    	if($removed) {
			    		$exist = $this->User_Roles_Model->check_if_exist($id, $this->input->post('role_id'));

			    		if($exist > 0)
			    		{
			    			$user_privilege = array(
			    				'removed' => 0
			    			);

			    			$this->User_Roles_Model->modify($user_privilege, $exist);
			    		} 
			    		else 
			    		{
			    			$user_privilege = array(
						    	'user_id' => $id,
						    	'role_id' => $this->input->post('role_id')
						    );

						    $user_role_id = $this->User_Roles_Model->insert($user_privilege);
			    		}
			    	}
			    }

			    $history = array(
                    'history_logs' => 'has updated a user',
                    'history_details' => 
					'{
						id: "' . $id . '",
						username: "' . $this->input->post('username') . '",
						password: "' . $this->input->post('password') . '",
						secret question: "' . $this->Secret_Model->get_secret_question_by_id($this->input->post('secret_id')) . '",
						secret password: "' . $this->input->post('secret_password') . '",
						profile: "' . $this->Profile_Model->get_profile_by_id($this->input->post('profile_id')) . '",
						privilege: "' . $this->Roles_Model->get_role_by_id($this->input->post('role_id')) . '"
					}',
                    'history_table' => 'user',
                    'history_table_id' => $id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('user/view/'.$id)
                );

                $history_id = $this->History_Model->insert($history);

			    $q = $this->db->get_where('user', array('id' => $id));

                $data = array(
                	'id'      => $id,
                	'datas'   => $q->row(),
                	'role_id' => $role_id,
                	'header'  => 'Sweet',
	                'message' => 'The user was been successfully updated.',
	                'type'    => 'success'
	            );

	            echo json_encode( $data ); exit();
	    	}
	    }
	}

	public function edit($id = null)
    {	
    	if( $this->input->method() ) 
        {  	
        	$arr['profiles'] = $this->Profile_Model->reload_lists($id);
			$arr['info'] = $this->User_Model->find($id);
			echo json_encode($arr);
		}
    }

    public function remove($id = null)
	{	
		if( $this->input->method() ) 
        {  
	    	$status = array(
	            'removed' => 1
	        );

	    	$this->db->where('id', $id);	
	    	$this->db->update('user', $status);

	    	$history = array(
	            'history_logs' => 'has removed a user',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'user',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('user/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('user', array('id' => $id));
			echo json_encode($q->row());
		}
	}

	public function restore($id = null)
	{	
		if( $this->input->method() ) 
        {  
	    	$status = array(
	            'removed' => 0
	        );

	    	$this->db->where('id', $id);	
	    	$this->db->update('user', $status);

	    	$history = array(
	            'history_logs' => 'has restored a user',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'user',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('user/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('user', array('id' => $id));
			echo json_encode($q->row());
		}
	}

	public function get_all_profile_for_user()
	{
		$data = $this->Profile_Model->get_all_profile_for_user();

		echo json_encode($data);
	}

	public function get_all_roles()
	{
		$data = $this->Roles_Model->get_all_roles();

		echo json_encode($data);
	}

	public function get_all_secret_question()
	{
		$data = $this->Secret_Model->get_all_secret_question();

		echo json_encode($data);
	}
}
