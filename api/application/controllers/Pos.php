<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos extends CI_Controller {

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
            'Transactions_Model' => 'Transactions_Model',
            'Transactions_Item_Model' => 'Transactions_Item_Model',
            'Product_Category_Model' => 'Product_Category_Model',
            'Product_Model' => 'Product_Model',
            'History_Model' => 'History_Model',
            'Load_Credit_Model' => 'Load_Credit_Model',
            'Product_Posting_Model' => 'Product_Posting_Model'
        );

        $this->load->model($models);  
    }

	public function index()
	{
		$this->load->database();

		$today 		 = strtolower(date('l'));
		$keywords    = $this->input->get('keywords');
		$user        = $this->input->get('user_id');
		$current     = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit       = 9 == -1 ? 0 : 9;
		$page        = $current !== null ? $current : 1;
		$start_from  = ($page-1) * $limit;

		$this->db->select('*');
		$this->db->from('product as prod');
		$this->db->join('product_category as prod_cat', 'prod.product_category_id = prod_cat.product_category_id');
		$this->db->where('prod.group_id IN(SELECT ug.group_id from user_group as ug where ug.user_id = ' . $user . ')');
        $this->db->where('prod.product_id IN(SELECT product_id from product_show where ' . $today . ' = 1 and removed != 1)');
		if(empty($keywords)){
			$this->db->where('prod_cat.product_category_slug', ucfirst($this->input->get('products')));
		}
		if(!empty($keywords)){
			$this->db->group_start();
			$this->db->or_where('prod.product_code LIKE', '%' . $keywords . '%');
			$this->db->or_where('prod.product_name LIKE', '%' . $keywords . '%');
			$this->db->group_end();
		}
		$this->db->order_by("prod.product_id", "desc");
		$query1 = $this->db->get();

		$this->db->select('*');
		$this->db->from('product as prod');
		$this->db->join('product_category as prod_cat', 'prod.product_category_id = prod_cat.product_category_id');
		$this->db->where('prod.group_id IN(SELECT ug.group_id from user_group as ug where ug.user_id = ' . $user . ')');
        $this->db->where('prod.product_id IN(SELECT product_id from product_show where ' . $today . ' = 1 and removed != 1)');
		if(empty($keywords)){
			$this->db->where('prod_cat.product_category_slug', ucfirst($this->input->get('products')));
		}
		if(!empty($keywords)){
			$this->db->group_start();
			$this->db->or_where('prod.product_code LIKE', '%' . $keywords . '%');
			$this->db->or_where('prod.product_name LIKE', '%' . $keywords . '%');
			$this->db->group_end();
		}
		$this->db->order_by("prod.product_id", "desc");
		$query2 = $this->db->limit( $limit, $start_from )->get();

		$data['data'] = $query2->result();
		$data['data2'] = $query2->result();
		$data['total'] = $query1->num_rows();

		echo json_encode($data);
	}

	public function get_all_pending_notifications()
	{
		$wildcard   = $this->input->get("keywords");
		$current    = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit      = 6 == -1 ? 0 : 6;
		$page       = $current !== null ? $current : 1;
		$start_from = ($page-1) * $limit;

		$data['data'] = $this->Transactions_Model->get_all_pending_notifications($wildcard, $limit, $start_from);
		$data['total'] = $this->Transactions_Model->get_alls_pending_notifications($wildcard);

		echo json_encode($data);
	}

	public function get_all_reserved_notifications()
	{
		$wildcard   = $this->input->get("keywords");
		$current    = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit      = 6 == -1 ? 0 : 6;
		$page       = $current !== null ? $current : 1;
		$start_from = ($page-1) * $limit;

		$data['data'] = $this->Transactions_Model->get_all_reserved_notifications($wildcard, $limit, $start_from);
		$data['total'] = $this->Transactions_Model->get_alls_reserved_notifications($wildcard);

		echo json_encode($data);
	}

	public function get_all_served_notifications()
	{
		$wildcard   = $this->input->get("keywords");
		$current    = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit      = 6 == -1 ? 0 : 6;
		$page       = $current !== null ? $current : 1;
		$start_from = ($page-1) * $limit;

		$data['data'] = $this->Transactions_Model->get_all_served_notifications($wildcard, $limit, $start_from);
		$data['total'] = $this->Transactions_Model->get_alls_served_notifications($wildcard);

		echo json_encode($data);
	}

	public function get_all_cancelled_notifications()
	{
		$wildcard   = $this->input->get("keywords");
		$current    = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit      = 6 == -1 ? 0 : 6;
		$page       = $current !== null ? $current : 1;
		$start_from = ($page-1) * $limit;

		$data['data'] = $this->Transactions_Model->get_all_cancelled_notifications($wildcard, $limit, $start_from);
		$data['total'] = $this->Transactions_Model->get_alls_cancelled_notifications($wildcard);

		echo json_encode($data);
	}

	public function create($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->Transactions_Model->check_last_inserted();

            if(!($check > 0))
            {	
            	$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();				
				$timestamp = date('Y-m-d H:i:s');

				// if($this->input->get('trans_type') == 'pending')
				// {	
				// 	$transaction_id = $this->Transactions_Model->find($this->input->get('trans_num'))->id;

				// 	$transactions = array(
				// 		'type'	=> $this->input->get('type'),
				// 		'mode_of_payment_id' => $this->input->get('payment'),
				// 		'stud_no'	=> $this->input->get('stud_no'),
				// 		'status_id'	=> 3,						
				// 		'updated_at' => $timestamp,
				// 		'updated_by' => $this->input->get('user_id')
				// 	);

				// 	$this->Transactions_Model->modify($transactions, $transaction_id);
				// 	$this->Transactions_Item_Model->removed($transaction_id);

				// 	$arr = array(); $total_price = 0;
				// 	foreach ($posts as $post) {
				// 		$item  = json_decode($post, true);
				// 		$total = (floatval($item['price'])) * (floatval($item['quantity']));
				// 		$discounts = (floatval($item['discount'])) * (floatval($item['quantity']));

				// 		$items = array(
				// 			'transaction_id' => $transaction_id,
				// 			'product_id' => $item['id'],
				// 			'quantity' => $item['quantity'],
				// 			'price' => $item['price'],
				// 			'discount' => $discounts,
				// 			'total' => $total
				// 		);

				// 		$trans_id = $this->Transactions_Item_Model->insert($items);
				// 		$arr[] = $item['id']; $total_price += $total;	

				// 		$this->Product_Model->debit_inventory($item['id'], $item['quantity']);		
				// 	}

				// 	$updatePrice = array(
				// 		'total_price' => $total_price
				// 	);

				// 	$this->Transactions_Model->modify($updatePrice, $transaction_id);

				// 	$history = array(
			 //            'history_logs' => 'has checked out a transactions',
			 //            'history_details' => 
				// 		'{
				// 			id: "' . $transaction_id . '",
				// 			transaction: "' . $this->input->get('trans_num') . '"
				// 		}',
			 //            'history_table' => 'transactions',
			 //            'history_table_id' => $transaction_id,
			 //            'history_timestamp' => $timestamp,
			 //            'history_by' => $this->input->get('user_id'),
			 //            'history_slug' => base_url('transactions/view/'.$this->input->get('trans_num'))
			 //        );

			 //        $history_id = $this->History_Model->insert($history);

				// 	$data = array(
				//     	'trans_no'  => $this->input->get('trans_num'),
				//     	'total_pay' => $total_price,
				//     	'header'    => 'Sweet',
				//         'message'   => 'The transaction was been successfully done.',
				//         'type'      => 'success'
				//     );
				// }
				// else if($this->input->get('trans_type') == 'reserved')
				// {	
				// 	$transaction_id = $this->Transactions_Model->find($this->input->get('trans_num'))->id;

				// 	$transactions = array(
				// 		'status_id'	=> 3,						
				// 		'updated_at' => $timestamp,
				// 		'updated_by' => $this->input->get('user_id')
				// 	);

				// 	$this->Transactions_Model->modify($transactions, $transaction_id);
				// 	$this->Transactions_Item_Model->removed($transaction_id);

				// 	$arr = array(); $total_price = 0;
				// 	foreach ($posts as $post) {
				// 		$item  = json_decode($post, true);
				// 		$total = (floatval($item['price'])) * (floatval($item['quantity']));
				// 		$discounts = (floatval($item['discount'])) * (floatval($item['quantity']));

				// 		$items = array(
				// 			'transaction_id' => $transaction_id,
				// 			'product_id' => $item['id'],
				// 			'quantity' => $item['quantity'],
				// 			'price' => $item['price'],
				// 			'discount' => $discounts,
				// 			'total' => $total
				// 		);

				// 		$trans_id = $this->Transactions_Item_Model->insert($items);
				// 		$arr[] = $item['id']; $total_price += $total;	
				// 	}

				// 	$history = array(
			 //            'history_logs' => 'has checked out a transactions',
			 //            'history_details' => 
				// 		'{
				// 			id: "' . $transaction_id . '",
				// 			transaction: "' . $this->input->get('trans_num') . '"
				// 		}',
			 //            'history_table' => 'transactions',
			 //            'history_table_id' => $transaction_id,
			 //            'history_timestamp' => $timestamp,
			 //            'history_by' => $this->input->get('user_id'),
			 //            'history_slug' => base_url('transactions/view/'.$this->input->get('trans_num'))
			 //        );

			 //        $history_id = $this->History_Model->insert($history);

				// 	$data = array(
				//     	'trans_no'  => $this->input->get('trans_num'),
				//     	'total_pay' => $total_price,
				//     	'header'    => 'Sweet',
				//         'message'   => 'The transaction was been successfully done.',
				//         'type'      => 'success'
				//     );
				// }
				// else
    			// {				
					$trans_no = $this->Transactions_Model->generate();

					$transactions = array(
						'trans_no' => $trans_no,
						'created_at' => $timestamp,
						'type'	=> $this->input->get('type'),
						'mode_of_payment_id'	=> $this->input->get('payment'),
						'barcode'	=> $this->input->get('barcode'),
						'status_id'	=> 3,
						'created_by' => $this->input->get('user_id')
					);

					$transactions_id = $this->Transactions_Model->insert($transactions);

					$arr = array(); $total_price = 0;
					foreach ($posts as $post) {
						$item  = json_decode($post, true);
						$total = (floatval($item['price'])) * (floatval($item['quantity']));
						$discounts = (floatval($item['discount'])) * (floatval($item['quantity']));

						$items = array(
							'transaction_id' => $transactions_id,
							'product_id' => $item['id'],
							'quantity' => $item['quantity'],
							'price' => $item['price'],
							'discount' => $discounts,
							'total' => $total
						);

						$trans_id = $this->Transactions_Item_Model->insert($items);
						$arr[] = $item['id']; $total_price += $total;	

						$this->Product_Model->debit_inventory($item['id'], $item['quantity']);		
					}

					$updatePrice = array(
						'total_price' => $total_price
					);

					$this->Transactions_Model->modify($updatePrice, $transactions_id);

					$history = array(
			            'history_logs' => 'has created a transactions',
			            'history_details' => 
						'{
							id: "' . $transactions_id . '",
							transaction: "' . $trans_no . '"
						}',
			            'history_table' => 'transactions',
			            'history_table_id' => $transactions_id,
			            'history_timestamp' => $timestamp,
			            'history_by' => $this->input->get('user_id'),
			            'history_slug' => base_url('transactions/view/'.$trans_no)
			        );

			        $history_id = $this->History_Model->insert($history);

					$data = array(
				    	'trans_no'  => $trans_no,
				    	'total_pay' => $total_price,
				    	'header'    => 'Sweet',
				        'message'   => 'The transaction was been successfully done.',
				        'type'      => 'success'
				    );
				// } 
			    echo json_encode( $data ); exit();
			}
		}
	}

	public function order($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->Transactions_Model->check_last_inserted();

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();
				$trans_no = $this->Transactions_Model->generate();
				$timestamp = date('Y-m-d H:i:s');

				$mode_of_payment =  ($this->input->get('type') == 2) ? 2 : 3;  
				$status =  ($this->input->get('type') == 2) ? 2 : 1;  

				$transactions = array(
					'trans_no' => $trans_no,
					'created_at' => $timestamp,
					'type'	=> $this->input->get('type'),
					'mode_of_payment_id' => $mode_of_payment,
					'stud_no'	=> $this->input->get('stud_no'),
					'status_id'	=> $status,
					'created_by' => $this->input->get('user_id')
				);

				$transactions_id = $this->Transactions_Model->insert($transactions);

				$arr = array(); $total_price = 0;
				foreach ($posts as $post) {
					$item  = json_decode($post, true);
					$total = (floatval($item['price'])) * (floatval($item['quantity']));
					$discounts = (floatval($item['discount'])) * (floatval($item['quantity']));

					$items = array(
						'transaction_id' => $transactions_id,
						'product_id' => $item['id'],
						'quantity' => $item['quantity'],
						'price' => $item['price'],
						'discount' => $discounts,
						'total' => $total
					);

					$trans_id = $this->Transactions_Item_Model->insert($items);
					$arr[] = $item['id']; $total_price += $total;	

					if($status == 2) {
						$this->Product_Model->debit_inventory($item['id'], $item['quantity']);	
					}	
				}

				$updatePrice = array(
					'total_price' => $total_price
				);

				$this->Transactions_Model->modify($updatePrice, $transactions_id);

				$history = array(
		            'history_logs' => 'has created a transactions',
		            'history_details' => 
					'{
						id: "' . $transactions_id . '",
						transaction: "' . $trans_no . '"
					}',
		            'history_table' => 'transactions',
		            'history_table_id' => $transactions_id,
		            'history_timestamp' => $timestamp,
		            'history_by' => $this->input->get('user_id'),
		            'history_slug' => base_url('transactions/view/'.$trans_no)
		        );

		        $history_id = $this->History_Model->insert($history);

				$data = array(
			    	'trans_no'  => $trans_no,
			    	'total_pay' => $total_price,
			    	'header'    => 'Sweet',
			        'message'   => 'The transaction was been successfully ordered.',
			        'type'      => 'success'
			    );

			    echo json_encode( $data ); exit();
			}
		}
	}

	public function search_code()
	{
        $arr = $this->Product_Model->find_code($this->input->get('item_code'));

        echo json_encode( $arr );

        exit();
	}

	public function fetch_transaction($transaction)
	{
		$arr['infos'] = $this->Transactions_Model->fetch_transactions($transaction);
		$arr['items'] = $this->Transactions_Item_Model->fetch_transactions($transaction);

        echo json_encode( $arr );

        exit();
	}

	public function cancel_transaction($type, $transaction)
	{	
		if($type == 'pending' || $type == 'reserved')
		{
			if( $this->input->method() ) 
	        {   
	            $check = $this->Transactions_Model->check_last_modified();

	            if(!($check > 0))
	            {
					$timestamp = date('Y-m-d H:i:s');

					$transaction_id = $this->Transactions_Model->find($transaction)->id;

					$transactions = array(
						'status_id'	=> 4,						
						'updated_at' => $timestamp,
						'updated_by' => $this->input->get('user_id')
					);

					$this->Transactions_Model->modify($transactions, $transaction_id);

					$history = array(
			            'history_logs' => 'has cancelled a transactions',
			            'history_details' => 
						'{
							id: "' . $transaction_id . '",
							transaction: "' . $transaction . '"
						}',
			            'history_table' => 'transactions',
			            'history_table_id' => $transaction_id,
			            'history_timestamp' => $timestamp,
			            'history_by' => $this->input->get('user_id'),
			            'history_slug' => base_url('transactions/view/'.$transaction)
			        );

			        $history_id = $this->History_Model->insert($history);

					$data = array(
				    	'trans_no'  => $transaction,
				    	'header'    => 'Sweet',
				        'message'   => "The transaction was been successfully cancelled.",
				        'type'      => 'success'
				    );

				    echo json_encode( $data ); exit();
				}
			}
		}
		else
		{
			if( $this->input->method() ) 
	        {   
	            $check = $this->Transactions_Model->check_last_modified();

	            if(!($check > 0))
	            {
					$timestamp = date('Y-m-d H:i:s');

					$transaction_id = $this->Transactions_Model->find($transaction)->id;

					$transactions = array(
						'status_id'	=> 4,						
						'updated_at' => $timestamp,
						'updated_by' => $this->input->get('user_id')
					);

					$this->Transactions_Model->modify($transactions, $transaction_id);

					$transaction_items = $this->Transactions_Item_Model->fetch_transactions($transaction);

					foreach ($transaction_items as $item) {
						
						$inventory = $this->Product_Model->fetch($item['id'])->product_quantity;
						$slug = $this->Product_Category_Model->get_slug_by_prod_id($item['id']);

						$new_quantity = floatval($inventory) + floatval($item['quantity']);

						$debit = array(
							'product_quantity' => $new_quantity
						);

						$this->Product_Model->modify($debit, $item['id']);		

						$history = array(
				            'history_logs' => 'has returned an item from a cancelled transaction',
				            'history_details' => 
							'{
								id: "' . $item['id'] . '",
								transaction: "' . $transaction . '",
								old inventory: "' . $inventory . '",
								new inventory: "' . $new_quantity . '"
							}',
				            'history_table' => 'produdct',
				            'history_table_id' => $item['id'],
				            'history_timestamp' => $timestamp,
				            'history_by' => $this->input->get('user_id'),
				            'history_slug' => base_url('products/'.$slug.'/view/'.$item['id'])
				        );

				        $history_id = $this->History_Model->insert($history);
					}

					$history = array(
			            'history_logs' => 'has cancelled a transactions',
			            'history_details' => 
						'{
							id: "' . $transaction_id . '",
							transaction: "' . $transaction . '"
						}',
			            'history_table' => 'transactions',
			            'history_table_id' => $transaction_id,
			            'history_timestamp' => $timestamp,
			            'history_by' => $this->input->get('user_id'),
			            'history_slug' => base_url('transactions/view/'.$transaction)
			        );

			        $history_id = $this->History_Model->insert($history);

					$data = array(
				    	'trans_no'  => $transaction,
				    	'header'    => 'Sweet',
				        'message'   => "The transaction was been successfully cancelled.",
				        'type'      => 'success'
				    );

				    echo json_encode( $data ); exit();
				}
			}
		}
	}

	public function generate_transactions()
	{
		$wildcard = $this->input->get("search");
		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 6 == -1 ? 0 : 6;
		$page    = $current !== null ? $current : 1;
		$start_from   = ($page-1) * $limit;

		$dateFrom  = $this->input->get('date_from');
		$dateTo    = $this->input->get('date_to');
		$category  = $this->input->get('category');
		$order 	   = $this->input->get('order');

		$data['data'] = $this->Transactions_Item_Model->generate_all_transactions($dateFrom, $dateTo, $category, $order);
		$data['total_amount'] = $this->Transactions_Item_Model->generate_alls_transactions($dateFrom, $dateTo, $category, $order);
		
		echo json_encode($data);
	}

	public function validate_transactions($studNo)
	{	
		$today = date('Y-m-d');
		$data['transactions'] = $this->Transactions_Model->check_if_has_transaction_today($studNo, $today);
		echo json_encode($data);
	}
}
