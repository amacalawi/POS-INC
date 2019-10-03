<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item extends CI_Controller {

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
            'Unit_Of_Measurement_Model' => 'Unit_Of_Measurement_Model',
            'Item_Model' => 'Item_Model',
            'History_Model' => 'History_Model',
            'Groups_Model' => 'Groups_Model'
        );

        $this->load->model($models);  
    }

	public function index($id = null)
	{	
		$wildcard = $this->input->get("search");
		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 5 == -1 ? 0 : 5;
		$page    = $current !== null ? $current : 1;
		$start_from   = ($page-1) * $limit;

		$data['data'] = $this->Item_Model->get_all_item_list($wildcard, $limit, $start_from);
		$data['total'] = $this->Item_Model->get_all_item_pagination($wildcard);

		echo json_encode($data);
	}

	public function archived($id = null)
	{	
		$wildcard = $this->input->get("search");
		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 5 == -1 ? 0 : 5;
		$page    = $current !== null ? $current : 1;
		$start_from   = ($page-1) * $limit;

		$data['data'] = $this->Item_Model->get_all_archived_item_list($wildcard, $limit, $start_from);
		$data['total'] = $this->Item_Model->get_all_archived_item_pagination($wildcard);

		echo json_encode($data);
	}

	public function upload()
	{
		if(!isset($_FILES['file_upload']))
		{
			$response=array("status"=>0,"message"=>"File not choosen!");
			print json_encode($response);
			exit;
		}
		
		$f_name = uniqid("img_").str_replace(" ","-",$_FILES['file_upload']['name']);
		$target_file = "./uploads/item/" . $f_name;
		$response = array("status" => 0, "message" => "File Upload Success!", "filename" => $f_name);
		print json_encode($response);

		if(!move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file))
		{
			
			$response=array("status"=>0,"message"=>"File Upload Failed!");
			print json_encode($response);
			exit;
		}		
	}

	public function create($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->Item_Model->check_last_inserted();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$gl_accounts = explode(' ', trim($this->input->post('gl_accounts_id')));
				$gl_accounts_id = $this->GL_Accounts_Model->get_gl_accounts_id_by_code($gl_accounts[0]);	
				$item = array(
                    'item_code' => $this->input->post('item_code'),
                    'item_name' => $this->input->post('item_name'),
                    'item_desc' => $this->input->post('item_desc'),
                    'unit_of_measurement_id' => $this->input->post('unit_of_measurement_id'),
                    'gl_accounts_id' => $gl_accounts_id,
                    'item_img'  => $this->input->get('files'), 
                    'created_by' => 1,
                    'created_at' => $timestamp
                );
                
                $item_id = $this->Item_Model->insert($item);

				$history = array(
                    'history_logs' => 'has created an item',
                    'history_details' => 
					'{
						id:"' . $item_id . '",
						code: "' . $this->input->post('item_code') . '",
						name: "' . $this->input->post('item_name') . '",
						item_desc: "' . $this->input->post('item_desc') . '",
						uom: "' . $this->input->post('unit_of_measurement_id') . '",
						gl accounts: "' . trim($this->input->post('gl_accounts_id')) . '",
						files: "' . $this->input->get('files') . '"
					}',
                    'history_table' => 'item',
                    'history_table_id' => $item_id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('item/view/'.$item_id)
                );

                $history_id = $this->History_Model->insert($history);

                $data = array(
                	'id'      => $item_id,
                	'header'  => 'Sweet',
	                'message' => 'The item has been successfully saved.',
	                'type'    => 'success'
	            );

	            echo json_encode( $data ); exit();		
			}
		}
	}

    public function edit($id)
    {	
    	$data = $this->Item_Model->find($id);
		echo json_encode($data);
    }

    public function update($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->Item_Model->check_last_modified();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$gl_accounts = explode(' ', trim($this->input->post('gl_accounts_id')));
				$gl_accounts_id = $this->GL_Accounts_Model->get_gl_accounts_id_by_code($gl_accounts[0]);	
				$item = array(
                    'item_code' => $this->input->post('item_code'),
                    'item_name' => $this->input->post('item_name'),
                    'item_desc' => $this->input->post('item_desc'),
                    'unit_of_measurement_id' => $this->input->post('unit_of_measurement_id'),
                    'gl_accounts_id' => $gl_accounts_id,
                    'item_img'  => $this->input->get('files'), 
                    'updated_by' => 1,
                    'updated_at' => $timestamp
                );
                
                $this->Item_Model->modify($item, $id);

				$history = array(
                    'history_logs' => 'has updated an item',
                    'history_details' => 
					'{
						id:"' . $id . '",
						code: "' . $this->input->post('item_code') . '",
						name: "' . $this->input->post('item_name') . '",
						item_desc: "' . $this->input->post('item_desc') . '",
						uom: "' . $this->input->post('unit_of_measurement_id') . '",
						gl accounts: "' . trim($this->input->post('gl_accounts_id')) . '",
						files: "' . $this->input->get('files') . '"
					}',
                    'history_table' => 'item',
                    'history_table_id' => $id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('item/view/'.$id)
                );

                $history_id = $this->History_Model->insert($history);

                $data = array(
                	'id'      => $id,
                	'header'  => 'Sweet',
	                'message' => 'The item has been successfully updated.',
	                'type'    => 'success'
	            );

	            echo json_encode( $data ); exit();		
			}
		}
	}

	public function remove($id = null)
	{	
		if( $this->input->method() ) 
        {  
	    	$status = array(
	            'removed' => 1
	        );

	    	$this->db->where('item_id', $id);	
	    	$this->db->update('item', $status);

	    	$history = array(
	            'history_logs' => 'has removed an item',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'item',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('item/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('item', array('item_id' => $id));
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

	    	$this->db->where('item_id', $id);	
	    	$this->db->update('item', $status);

	    	$history = array(
	            'history_logs' => 'has restored an item',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'item',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('item/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('item', array('item_id' => $id));
			echo json_encode($q->row());
		}
	}

    public function display_all_active_gl_accounts()
	{	
		if( $this->input->method() ) 
        { 
            $arr = $this->GL_Accounts_Model->display_all_active_gl_accounts2();

            echo json_encode( $arr );

            exit();
        }
	}

	public function display_all_active_groups()
	{	
		if( $this->input->method() ) 
        { 
            $arr = $this->Groups_Model->display_all_active_groups();

            echo json_encode( $arr );

            exit();
        }
	}

	public function display_all_active_uom()
	{	
		if( $this->input->method() ) 
        { 
            $arr = $this->Unit_Of_Measurement_Model->display_all_active_uom();

            echo json_encode( $arr );

            exit();
		}
	}
}
