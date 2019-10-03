<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

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
            'Product_Item_Model' => 'Product_Item_Model',
            'Product_Model' => 'Product_Model',
            'Item_Model' => 'Item_Model',
            'History_Model' => 'History_Model',
            'Product_Posting_Model' => 'Product_Posting_Model',
            'Product_Show_Model' => 'Product_Show_Model'
        );

        $this->load->model($models);  
    }

	public function index()
	{	
		$wildcard = $this->input->get("search");
		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 5 == -1 ? 0 : 5;
		$page    = $current !== null ? $current : 1;
		$start_from   = ($page-1) * $limit;

		$this->db->select('*');
		$this->db->from('product_category as prod');
		if(!empty($wildcard)){
			$this->db->group_start();
	        $this->db->or_where('prod.product_category_id LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_code LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_name LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_desc LIKE', '%' . $wildcard . '%');
	        $this->db->group_end();
		}
		$this->db->where('prod.removed', 0);
		$this->db->order_by("prod.product_category_id", "asc");
		$query1 = $this->db->get();

		$this->db->select('*, (SELECT count(product_id) from product where product_category_id = prod.product_category_id) as total_items');
		$this->db->from('product_category as prod');
		$this->db->where('prod.removed', 0);
		$this->db->order_by("prod.product_category_id", "asc");
		if(!empty($wildcard)){
			$this->db->group_start();
	        $this->db->or_where('prod.product_category_id LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_code LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_name LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_desc LIKE', '%' . $wildcard . '%');
	        $this->db->group_end();
		}
		$query2 = $this->db->limit( $limit, $start_from )->get();

		$data['total'] = $query1->num_rows();
		$data['data'] = $query2->result();

		echo json_encode($data);
	}

	public function create($id = null)
	{	
		if( $this->input->method() ) 
        {   
            // $check = $this->product_Model->check_last_inserted('item'];

            // if(!($check > 0))
            // {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$item = array(
                    'product_category_code' => $this->input->post('product_category_code'),
                    'product_category_name' => $this->input->post('product_category_name'),
                    'product_category_desc' => $this->input->post('product_category_desc'),
                    'product_category_slug' => strtolower(str_replace(' ','-',$this->input->post('product_category_name'))),
                    'created_by' => $this->input->get('user_id'),
                    'created_at' => $timestamp
                );
                
                $product_category_id = $this->Product_Category_Model->insert($item);

				$history = array(
                    'history_logs' => 'has created a product category',
                    'history_details' => 
					'{
						id:"' . $product_category_id . '",
						code: "' . $this->input->post('product_category_code') . '",
						name: "' . $this->input->post('product_category_name') . '",
						description: "' . $this->input->post('product_category_desc') . '",
						slug: "' . strtolower(str_replace(' ','-',$this->input->post('product_category_name'))) . '"
					}',
                    'history_table' => 'product_category',
                    'history_table_id' => $product_category_id,
                    'history_timestamp' => $timestamp,
                    'history_by' => $this->input->get('user_id'),
                    'history_slug' => base_url('product-category/view/'.$product_category_id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('product_category', array('product_category_id' => $product_category_id));

                $data = array(
                	'id'      => $product_category_id,
                	'data'    => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The product category was been successfully saved.',
	                'type'    => 'success'
	            );

	            echo json_encode( $data ); exit();
		
			// }
		}
	}

	public function edit($id = null)
	{
		$q = $this->Product_Category_Model->find($id);
		echo json_encode($q);
	}

	public function update($id = null)
	{	
		if( $this->input->method() ) 
        {   
            // $check = $this->product_Model->check_last_inserted('item'];

            // if(!($check > 0))
            // {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$item = array(
                    'product_category_code' => $this->input->post('product_category_code'),
                    'product_category_name' => $this->input->post('product_category_name'),
                    'product_category_desc' => $this->input->post('product_category_desc'),
                    'product_category_slug' => strtolower(str_replace(' ','-',$this->input->post('product_category_name'))),
                    'updated_by' => $this->input->get('user_id'),
                    'updated_at' => $timestamp
                );
                
                $this->Product_Category_Model->modify($item, $id);

				$history = array(
                    'history_logs' => 'has updated a product',
                    'history_details' => 
					'{
						id:"' . $id . '",
						code: "' . $this->input->post('product_category_code') . '",
						name: "' . $this->input->post('product_category_name') . '",
						description: "' . $this->input->post('product_category_desc') . '",
						slug: "' . strtolower(str_replace(' ','-',$this->input->post('product_category_name'))) . '"
					}',
                    'history_table' => 'product_category',
                    'history_table_id' => $id,
                    'history_timestamp' => $timestamp,
                    'history_by' => $this->input->get('user_id'),
                    'history_slug' => base_url('product-category/view/'.$id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('product_category', array('product_category_id' => $id));

                $data = array(
                	'id'      => $id,
                	'data'    => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The product category was been successfully updated.',
	                'type'    => 'success'
	            );

	            echo json_encode( $data ); exit();
		
			// }
		}
	}

	public function remove($id = null)
	{	
		if( $this->input->method() ) 
        {  	
        	$_POST = json_decode(file_get_contents('php://input'), true);

	    	$status = array(
	            'removed' => 1
	        );

	    	$this->db->where('product_category_id', $id);	
	    	$this->db->update('product_category', $status);

	    	$history = array(
	            'history_logs' => 'has removed a product category',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'product_category',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => $this->input->get('user_id'),
	            'history_slug' => base_url('product-category/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('product_category', array('product_category_id' => $id));

			echo json_encode($q->row());
		}
	}

	public function archived($id = null)
	{	
		$wildcard = $this->input->get("search");
		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 5 == -1 ? 0 : 5;
		$page    = $current !== null ? $current : 1;
		$start_from   = ($page-1) * $limit;

		$this->db->select('*');
		$this->db->from('product_category as prod');
		if(!empty($wildcard)){
			$this->db->group_start();
	        $this->db->or_where('prod.product_category_id LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_code LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_name LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_desc LIKE', '%' . $wildcard . '%');
	        $this->db->group_end();
		}
		$this->db->where('prod.removed', 1);
		$this->db->order_by("prod.product_category_id", "asc");
		$query1 = $this->db->get();

		$this->db->select('*, (SELECT count(product_id) from product where product_category_id = prod.product_category_id) as total_items');
		$this->db->from('product_category as prod');
		$this->db->where('prod.removed', 1);
		$this->db->order_by("prod.product_category_id", "asc");
		if(!empty($wildcard)){
			$this->db->group_start();
	        $this->db->or_where('prod.product_category_id LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_code LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_name LIKE', '%' . $wildcard . '%');
	        $this->db->or_where('prod.product_category_desc LIKE', '%' . $wildcard . '%');
	        $this->db->group_end();
		}
		$query2 = $this->db->limit( $limit, $start_from )->get();

		$data['total'] = $query1->num_rows();
		$data['data'] = $query2->result();

		echo json_encode($data);
	}

	public function category($page = null, $view = null, $params1 = null)
	{	
		if($view == null || $view == 'manage')
		{	
			$wildcard = $this->input->get("search");
			$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
			$limit   = 5 == -1 ? 0 : 5;
			$pages    = $current !== null ? $current : 1;
			$start_from   = ($pages-1) * $limit;

			$data['data']  = $this->Product_Model->get_all_product_list($wildcard, $limit, $start_from, $page);
			$data['total'] = $this->Product_Model->get_all_product_pagination($wildcard, $page);

			echo json_encode($data);
		}
		else if($view == 'archived')
		{
			$wildcard = $this->input->get("search");
			$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
			$limit   = 5 == -1 ? 0 : 5;
			$pages    = $current !== null ? $current : 1;
			$start_from   = ($pages-1) * $limit;

			$data['data']  = $this->Product_Model->get_all_archived_product_list($wildcard, $limit, $start_from, $page);
			$data['total'] = $this->Product_Model->get_all_archived_product_pagination($wildcard, $page);

			echo json_encode($data);
		}
		else if($view == 'edit')
		{
			$q = $this->Product_Model->find($params1);
			echo json_encode($q);
		}
		else if($view == 'delete')
		{
	    	$_POST = json_decode(file_get_contents('php://input'), true);

	    	$status = array(
                'removed' => 1
            );

	    	$this->db->where('product_id', $params1);	
	    	$this->db->update('product', $status);

	        $q = $this->db->get_where('product', array('product_id' => $params1));
			echo json_encode($q->row());
		}
		else if($view == 'create')
		{	
			if( $this->input->method() ) 
            {   
                // $check = $this->product_Model->check_last_inserted('item'];

                // if(!($check > 0))
                // {
					$_POST = json_decode(file_get_contents('php://input'), true);
					$posts = $this->input->post();

					$timestamp = date('Y-m-d H:i:s');
					// $gl_accounts = explode(' ', trim($this->input->post('gl_accounts_id')));
					// $gl_accounts_id = $this->GL_Accounts_Model->get_gl_accounts_id_by_code($gl_accounts[0]);
					$product_category_id = $this->Product_Category_Model->get_product_category_id_by_slug($page);
					$product_new = ($this->input->post('product_new') !== NULL) ? 1 : 0;
					$product_discount = ($this->input->post('product_discount') !== NULL) ? 1 : 0;
					$product_percent = ($this->input->post('product_discount') !== NULL) ? $this->input->post('product_discount_percentage') : 0;

					$item = array(
                        'product_code' => $this->input->post('product_code'),
                        'product_name' => $this->input->post('product_name'),
                        'product_desc' => $this->input->post('product_desc'),
                        'product_price' => $this->input->post('product_price'),
                        'product_discount_percentage' => $product_percent,
                        'product_discount' => $product_discount,
                        'product_new' => $product_new,
                        'product_img' => $this->input->get('files'), 
                        'gl_accounts_id' => $this->input->post('gl_accounts_id'),
                        'product_category_id' => $product_category_id,
                        'group_id' => $this->input->post('group_id'),	
                        'created_by' => $this->input->get('user_id'),
                        'created_at' => $timestamp
                    );
                    
                    $product_id = $this->Product_Model->insert($item);

                    $d1 = ($this->input->post('d1') !== NULL) ? 1 : 0;
                    $d2 = ($this->input->post('d2') !== NULL) ? 1 : 0;
                    $d3 = ($this->input->post('d3') !== NULL) ? 1 : 0;
                    $d4 = ($this->input->post('d4') !== NULL) ? 1 : 0;
                    $d5 = ($this->input->post('d5') !== NULL) ? 1 : 0;
                    $d6 = ($this->input->post('d6') !== NULL) ? 1 : 0;

                    $product_days = array(
                    	'product_id' => $product_id,
		                'monday' => $d1,
		                'tuesday' => $d2,
		                'wednesday' => $d3,
		                'thursday' => $d4,
		                'friday' => $d5,
		                'saturday' => $d6
		            ); 

                    $product_days_id = $this->Product_Show_Model->insert($product_days);

					$history = array(
                        'history_logs' => 'has created a product',
                        'history_details' => 
						'{
							id:"' . $product_id . '",
							code: "' . $this->input->post('product_code') . '",
							name: "' . $this->input->post('product_name') . '",
							price: "' . $this->input->post('product_price') . '",
							product category: "' . $this->Product_Category_Model->get_product_category_name_by_slug($page) . '"
							gl accounts: "' . trim($this->input->post('gl_accounts_id')) . '",
							discount: "' . $this->check_value($product_discount) . '",
							discount percentage: "' . $product_percent . '",
							new item: "' . $this->check_value($product_new) . '",
							file: "' . $this->input->get('files') . '" 
						}',
                        'history_table' => 'product',
                        'history_table_id' => $product_id,
                        'history_timestamp' => $timestamp,
                        'history_by' => $this->input->get('user_id'),
                        'history_slug' => base_url('products/'.$page.'/view/'.$product_id)
                    );

                    $history_id = $this->History_Model->insert($history);

                    $q = $this->db->get_where('product', array('product_id' => $product_id));

                    $data = array(
                    	'id'      => $product_id,
                    	'data'    => $q->row(),
                    	'header'  => 'Sweet',
		                'message' => 'The product was been successfully saved.',
		                'type'    => 'success'
		            );

		            echo json_encode( $data ); exit();
			
				// }
			}
		}
		else if($view == 'update')
		{	
			if( $this->input->method() ) 
            {   
                // $check = $this->product_Model->check_last_inserted('item'];

                // if(!($check > 0))
                // {
					$_POST = json_decode(file_get_contents('php://input'), true);
					$posts = $this->input->post();

					$timestamp = date('Y-m-d H:i:s');
					$product_category_id = $this->Product_Category_Model->get_product_category_id_by_slug($page);
					$product_new = ($this->input->post('product_new') !== NULL) ? 1 : 0;
					$product_discount = ($this->input->post('product_discount') !== NULL) ? 1 : 0;
					$product_percent = ($this->input->post('product_discount') !== NULL) ? $this->input->post('product_discount_percentage') : 0;

					$item = array(
                        'product_code' => $this->input->post('product_code'),
                        'product_name' => $this->input->post('product_name'),
                        'product_desc' => $this->input->post('product_desc'),
                        'product_price' => $this->input->post('product_price'),
                        'product_discount_percentage' => $product_percent,
                        'product_discount' => $product_discount,
                        'product_new' => $product_new,
                        'product_img' => $this->input->get('files'), 
                        'gl_accounts_id' => $this->input->post('gl_accounts_id'),
                        'product_category_id' => $product_category_id,
                        'group_id' => $this->input->post('group_id'),
                        'updated_by' => $this->input->get('user_id'),
                        'updated_at' => $timestamp
                    );
                    
                    $this->Product_Model->modify($item, $params1);

                    $d1 = ($this->input->post('d1') !== NULL) ? 1 : 0;
                    $d2 = ($this->input->post('d2') !== NULL) ? 1 : 0;
                    $d3 = ($this->input->post('d3') !== NULL) ? 1 : 0;
                    $d4 = ($this->input->post('d4') !== NULL) ? 1 : 0;
                    $d5 = ($this->input->post('d5') !== NULL) ? 1 : 0;
                    $d6 = ($this->input->post('d6') !== NULL) ? 1 : 0;

                    $product_days = array(
		                'monday' => $d1,
		                'tuesday' => $d2,
		                'wednesday' => $d3,
		                'thursday' => $d4,
		                'friday' => $d5,
		                'saturday' => $d6
		            ); 

                    $this->Product_Show_Model->modify_by_product($params1);

					$history = array(
                        'history_logs' => 'has updated a product',
                        'history_details' => 
						'{
							id:"' . $params1 . '",
							code: "' . $this->input->post('product_code') . '",
							name: "' . $this->input->post('product_name') . '",
							price: "' . $this->input->post('product_price') . '",
							product category: "' . $this->Product_Category_Model->get_product_category_name_by_slug($page) . '"
							gl accounts: "' . trim($this->input->post('gl_accounts_id')) . '",
							discount: "' . $this->check_value($product_discount) . '",
							discount percentage: "' . $product_percent . '",
							new item: "' . $this->check_value($product_new) . '",
							file: "' . $this->input->get('files') . '" 
						}',
                        'history_table' => 'product',
                        'history_table_id' => $params1,
                        'history_timestamp' => $timestamp,
                        'history_by' => $this->input->get('user_id'),
                        'history_slug' => base_url('products/'.$page.'/view/'.$params1)
                    );

                    $history_id = $this->History_Model->insert($history);

                    $q = $this->db->get_where('product', array('product_id' => $params1));

                    $data = array(
                    	'id'      => $params1,
                    	'data'    => $this->input->post('product_discount'),
                    	'header'  => 'Sweet',
		                'message' => 'The product was been successfully updated.',
		                'type'    => 'success'
		            );

		            echo json_encode( $data ); exit();
			
				// }
			}
		}
		else if($view == 'remove')
		{	
			if( $this->input->method() ) 
	        {  	
	        	$_POST = json_decode(file_get_contents('php://input'), true);

		    	$status = array(
		            'removed' => 1
		        );

		    	$this->db->where('product_id', $params1);	
		    	$this->db->update('product', $status);

		    	$history = array(
		            'history_logs' => 'has removed a product',
		            'history_details' => 
					'{
						id: "' . $params1 . '"
					}',
		            'history_table' => 'product',
		            'history_table_id' => $params1,
		            'history_timestamp' => date('Y-m-d H:i:s'),
		            'history_by' => $this->input->get('user_id'),
		            'history_slug' => base_url('product/view/'.$params1)
		        );

		        $history_id = $this->History_Model->insert($history);

		        $q = $this->db->get_where('product', array('product_id' => $params1));

				echo json_encode($q->row());
			}
		}
		else if($view == 'restore')
		{	
			if( $this->input->method() ) 
	        {  	
	        	$_POST = json_decode(file_get_contents('php://input'), true);

		    	$status = array(
		            'removed' => 0
		        );

		    	$this->db->where('product_id', $params1);	
		    	$this->db->update('product', $status);

		    	$history = array(
		            'history_logs' => 'has restored product',
		            'history_details' => 
					'{
						id: "' . $params1 . '"
					}',
		            'history_table' => 'product',
		            'history_table_id' => $params1,
		            'history_timestamp' => date('Y-m-d H:i:s'),
		            'history_by' => $this->input->get('user_id'),
		            'history_slug' => base_url('product/view/'.$params1)
		        );

		        $history_id = $this->History_Model->insert($history);

		        $q = $this->db->get_where('product', array('product_id' => $params1));

				echo json_encode($q->row());
			}
		}
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
		$target_file = "./uploads/product/" . $f_name;
		$response = array("status" => 0, "message" => "File Upload Success!", "filename" => $f_name);
		print json_encode($response);

		if(!move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file))
		{
			
			$response=array("status"=>0,"message"=>"File Upload Failed!");
			print json_encode($response);
			exit;
		}		
	}

	public function check_value($value)
	{
		if($value == 1)
		{
			return 'Yes';
		}
		else
		{
			return 'No';
		}
	}

	public function display_all_active_gl_accounts()
	{
		// if( $this->input->is_ajax_request() ) 
		// {
            $arr = $this->GL_Accounts_Model->display_all_active_gl_accounts();

            echo json_encode( $arr );

            exit();
        // }
	}

	public function product_item($page = null)
	{	
		$wildcard = $this->input->get("search");
		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 5 == -1 ? 0 : 5;
		$pages    = $current !== null ? $current : 1;
		$start_from   = ($pages-1) * $limit;

		$data['data_product']  = $this->Product_Item_Model->get_all_product_item_list($wildcard, $limit, $start_from, $page);
		$data['total_product'] = $this->Product_Item_Model->get_all_product_item_pagination($wildcard, $page);

		echo json_encode($data);
	}

	public function all_item($page = null)
	{	
		$wildcard = $this->input->get("search");
		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 5 == -1 ? 0 : 5;
		$pages    = $current !== null ? $current : 1;
		$start_from   = ($pages-1) * $limit;

		if(!empty($wildcard)) {
			$data['data_item']  = $this->Item_Model->get_all_item_list($wildcard, $limit, $start_from);
			$data['total_item'] = $this->Item_Model->get_all_item_pagination($wildcard);
		} else {
			$data['data_item']  = '';
			$data['total_item'] = 0;
		}

		echo json_encode($data);
	}

	public function product_posting($page = null, $view = null, $params1 = null)
	{
		if($page == null)
		{	
			$wildcard = $this->input->get("search");
			$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
			$limit   = 5 == -1 ? 0 : 5;
			$pages    = $current !== null ? $current : 1;
			$start_from   = ($pages-1) * $limit;

			$data['data']  = $this->Product_Model->get_all_product_list($wildcard, $limit, $start_from );
			$data['total'] = $this->Product_Model->get_all_product_pagination($wildcard);

			echo json_encode($data);
		}
		else if($page == 'edit')
		{
	    	$data = $this->Product_Model->find($view);
			echo json_encode($data);
		}
		else if($page == 'post')
		{
			$_POST = json_decode(file_get_contents('php://input'), true);
			$posts = $this->input->post();

			$timestamp = date('Y-m-d H:i:s');
			$old_quantity = $this->Product_Model->get_product_info_by_id($view)->product_quantity;

			if ($this->input->post('inventory_adjustment') == 1) 
			{
				$new_quantity = floatval($old_quantity) + floatval($this->input->post('post_quantity')); 
				
				$products = array(
					'product_quantity' => $new_quantity,
					'updated_at' => $timestamp,
					'updated_by' => $this->input->get('user_id')
				);

				$this->Product_Model->modify($products, $view);
			} 
			else 
			{
				$new_quantity = floatval($old_quantity) - floatval($this->input->post('post_quantity')); 
				
				$products = array(
					'product_quantity' => $new_quantity,
					'updated_at' => $timestamp,
					'updated_by' => $this->input->get('user_id')
				);

				$this->Product_Model->modify($products, $view);
			}

			$inventory_adjustment = ($this->input->post('inventory_adjustment') == 1) ? 'Additional Inventory' : 'Deduction Inventory';

			$posting = array(
				'product_id' => $view,
				'quantity' => $this->input->post('post_quantity'),
				'user_id' => $this->input->get('user_id'),
				'inventory_adjustment' => $inventory_adjustment,
				'reason' => $this->input->post('reason') ? $this->input->post('reason') : '',
				'posting_datetime' => $timestamp
			);

			$posting_id = $this->Product_Posting_Model->insert($posting);

	    	$history = array(
	            'history_logs' => 'has posted a product quantity',
	            'history_details' => 
				'{
					id: "' . $posting_id . '",
					product: "' . $this->Product_Model->get_product_info_by_id($view)->product_name . '"
					quantity: "' . $this->input->post('post_quantity') . '",
					inventory adjustment: "' . $inventory_adjustment . '",
					reason: "' . $this->input->post('reason') . '"
				}',
	            'history_table' => 'product_posting',
	            'history_table_id' => $posting_id,
	            'history_timestamp' => $timestamp,
	            'history_by' => $this->input->get('user_id'),
	            'history_slug' => base_url('product/view/'.$view)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $data = array(
	        	'id'      => $view,
	        	'header'  => 'Sweet',
	            'message' => 'The product was been successfully posted.',
	            'type'    => 'success'
	        );

	        echo json_encode( $data ); exit();
		}
	}
}
