<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends CI_Controller {

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
            'Groups_Model' => 'Groups_Model',
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

			$data['data'] = $this->Groups_Model->get_all_groups_list($wildcard, $limit, $start_from);
			$data['total'] = $this->Groups_Model->get_all_groups_pagination($wildcard);

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

			$data['data'] = $this->Groups_Model->get_all_archived_groups_list($wildcard, $limit, $start_from);
			$data['total'] = $this->Groups_Model->get_all_archived_groups_pagination($wildcard);

			echo json_encode($data);
		}
	}

	public function create($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->Groups_Model->check_last_inserted();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$Groups  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'desc' => $this->input->post('desc'),
                    'created_by' => 1,
                    'created_at' => $timestamp
                );
                
                $Groups_id = $this->Groups_Model->insert($Groups);

				$history = array(
                    'history_logs' => 'has created a groups',
                    'history_details' => 
					'{
						id: "' . $Groups_id . '",
						code: "' . $this->input->post('code') . '",
						name: "' . $this->input->post('name') . '",
						desc: "' . $this->input->post('desc') . '"
					}',
                    'history_table' => 'groups',
                    'history_table_id' => $Groups_id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('groups/view/'.$Groups_id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('groups', array('id' => $Groups_id));

                $data = array(
                	'id'      => $Groups_id,
                	'datas'   => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The groups was been successfully saved.',
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
            $check = $this->Groups_Model->check_last_modified();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$Groups  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'desc' => $this->input->post('desc'),
                    'updated_by' => 1,
                    'updated_at' => $timestamp
                );
                
                $this->Groups_Model->modify($Groups, $id);

				$history = array(
                    'history_logs' => 'has updated a groups',
                    'history_details' => 
					'{
						id: "' . $id . '",
						code: "' . $this->input->post('code') . '",
						name: "' . $this->input->post('name') . '",
						desc: "' . $this->input->post('desc') . '",
					}',
                    'history_table' => 'groups',
                    'history_table_id' => $id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('groups/views/'.$id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('groups', array('id' => $id));

                $data = array(
                	'id'      => $id,
                	'datas'   => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The groups was been successfully updated.',
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
			$q = $this->db->get_where('groups', array('id' => $id));
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
	    	$this->db->update('groups', $status);

	    	$history = array(
	            'history_logs' => 'has removed a groups',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'groups',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('groups/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('groups', array('id' => $id));
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
	    	$this->db->update('groups', $status);

	    	$history = array(
	            'history_logs' => 'has restored a groups',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'groups',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('groups/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('groups', array('id' => $id));
			echo json_encode($q->row());
		}
	}
}
