<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_method extends CI_Controller {

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
            'Shipping_Method_Model' => 'Shipping_Method_Model',
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
			$this->db->from('shipping_method as ship');
			$this->db->where('ship.removed', 0);
			$this->db->where('ship.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('ship.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("ship.id", "asc");
			$query1 = $this->db->get();

			$this->db->select('*');
			$this->db->from('shipping_method as ship');
			$this->db->where('ship.removed', 0);
			$this->db->where('ship.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('ship.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("ship.id", "asc");
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
			$this->db->from('shipping_method as ship');
			$this->db->where('ship.removed', 1);
			$this->db->where('ship.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('ship.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("ship.id", "asc");
			$query1 = $this->db->get();

			$this->db->select('*');
			$this->db->from('shipping_method as ship');
			$this->db->where('ship.removed', 1);
			$this->db->where('ship.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('ship.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('ship.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("ship.id", "asc");
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
            $check = $this->Shipping_Method_Model->check_last_inserted();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$shipping_method  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'desc' => $this->input->post('desc'),
                    'created_by' => 1,
                    'created_at' => $timestamp
                );
                
                $shipping_method_id = $this->Shipping_Method_Model->insert($shipping_method);

				$history = array(
                    'history_logs' => 'has created a shipping method',
                    'history_details' => 
					'{
						id: "' . $shipping_method_id . '",
						code: "' . $this->input->post('code') . '",
						name: "' . $this->input->post('name') . '",
						desc: "' . $this->input->post('desc') . '"
					}',
                    'history_table' => 'shipping_method',
                    'history_table_id' => $shipping_method_id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('shipment-terms/view/'.$shipping_method_id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('shipping_method', array('id' => $shipping_method_id));

                $data = array(
                	'id'      => $shipping_method_id,
                	'datas'   => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The shipping method was been successfully saved.',
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
            $check = $this->Shipping_Method_Model->check_last_modified();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$shipping_method  = array(
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'desc' => $this->input->post('desc'),
                    'updated_by' => 1,
                    'updated_at' => $timestamp
                );
                
                $this->Shipping_Method_Model->modify($shipping_method, $id);

				$history = array(
                    'history_logs' => 'has updated a shipping method',
                    'history_details' => 
					'{
						id: "' . $id . '",
						code: "' . $this->input->post('code') . '",
						name: "' . $this->input->post('name') . '",
						desc: "' . $this->input->post('desc') . '",
					}',
                    'history_table' => 'shipping_method',
                    'history_table_id' => $id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('shipment-terms/views/'.$id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('shipping_method', array('id' => $id));

                $data = array(
                	'id'      => $id,
                	'datas'   => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The shipping method was been successfully updated.',
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
			$q = $this->db->get_where('shipping_method', array('id' => $id));
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
	    	$this->db->update('shipping_method', $status);

	    	$history = array(
	            'history_logs' => 'has removed a shipping method',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'shipping_method',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('shipment-terms/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('shipping_method', array('id' => $id));
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
	    	$this->db->update('shipping_method', $status);

	    	$history = array(
	            'history_logs' => 'has restored a shipping method',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'shipping_method',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('shipment-terms/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('shipping_method', array('id' => $id));
			echo json_encode($q->row());
		}
	}
}
