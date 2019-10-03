<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Privileges extends CI_Controller {

	public function __construct()
    {	
    	header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        $this->load_models();
    }

    public function load_models()
    {	
    	$this->load->database();

        $models = array(
            'Privilege_Model' => 'Privilege_Model',
            'History_Model' => 'History_Model'
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

			$this->db->select('*');
			$this->db->from('privilege as priv');
			$this->db->where('priv.removed', 0);
			$this->db->where('priv.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('priv.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("priv.id", "asc");
			$query1 = $this->db->get();

			$this->db->select('*');
			$this->db->from('privilege as priv');
			$this->db->where('priv.removed', 0);
			$this->db->where('priv.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('priv.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("priv.id", "asc");
			$query2 = $this->db->limit( $limit, $start_from )->get();

			$data['data'] = $query2->result();
			$data['total'] = $query1->num_rows();

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

			$this->db->select('*');
			$this->db->from('privilege as priv');
			$this->db->where('priv.removed', 1);
			$this->db->where('priv.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('priv.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("priv.id", "asc");
			$query1 = $this->db->get();

			$this->db->select('*');
			$this->db->from('privilege as priv');
			$this->db->where('priv.removed', 1);
			$this->db->where('priv.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('priv.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('priv.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("priv.id", "asc");
			$query2 = $this->db->limit( $limit, $start_from )->get();

			$data['data'] = $query2->result();
			$data['total'] = $query1->num_rows();

			echo json_encode($data);
		}
	}

	public function create($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->Privilege_Model->check_last_inserted();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$privilege  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'desc' => $this->input->post('desc'),
                    'created_by' => 1,
                    'created_at' => $timestamp
                );
                
                $privilege_id = $this->Privilege_Model->insert($privilege);

				$history = array(
                    'history_logs' => 'has created a privping method',
                    'history_details' => 
					'{
						id: "' . $privilege_id . '",
						code: "' . $this->input->post('code') . '",
						name: "' . $this->input->post('name') . '",
						desc: "' . $this->input->post('desc') . '"
					}',
                    'history_table' => 'privilege',
                    'history_table_id' => $privilege_id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('privment-terms/view/'.$privilege_id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('privilege', array('id' => $privilege_id));

                $data = array(
                	'id'      => $privilege_id,
                	'datas'   => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The privping method was been successfully saved.',
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
            $check = $this->Privilege_Model->check_last_modified();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$privilege  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'desc' => $this->input->post('desc'),
                    'updated_by' => 1,
                    'updated_at' => $timestamp
                );
                
                $this->Privilege_Model->modify($privilege, $id);

				$history = array(
                    'history_logs' => 'has updated a privping method',
                    'history_details' => 
					'{
						id: "' . $id . '",
						code: "' . $this->input->post('code') . '",
						name: "' . $this->input->post('name') . '",
						desc: "' . $this->input->post('desc') . '",
					}',
                    'history_table' => 'privilege',
                    'history_table_id' => $id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('privment-terms/views/'.$id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('privilege', array('id' => $id));

                $data = array(
                	'id'      => $id,
                	'datas'   => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The privping method was been successfully updated.',
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
			$q = $this->db->get_where('privilege', array('id' => $id));
			echo json_encode($q->row());
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
	    	$this->db->update('privilege', $status);

	    	$history = array(
	            'history_logs' => 'has removed a privping method',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'privilege',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('privment-terms/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('privilege', array('id' => $id));
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
	    	$this->db->update('privilege', $status);

	    	$history = array(
	            'history_logs' => 'has restored a privping method',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'privilege',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('privment-terms/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('privilege', array('id' => $id));
			echo json_encode($q->row());
		}
	}
}
