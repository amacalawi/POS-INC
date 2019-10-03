<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

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
            'Suppliers_Model' => 'Suppliers_Model',
            'History_Model' => 'History_Model',
            'Payment_Terms_Model' => 'Payment_Terms_Model',
            'Shipping_Method_Model' => 'Shipping_Method_Model'
        );

        $this->load->model($models);  
    }

	public function index($id = null)
	{
		if(!empty($this->input->get("search"))){
			$this->db->group_start();
			$this->db->like('suppliers_id', $this->input->get("search"));
			$this->db->or_like('suppliers_code', $this->input->get("search")); 
			$this->db->or_like('suppliers_name', $this->input->get("search"));
			$this->db->or_like('suppliers_desc', $this->input->get("search"));
			$this->db->group_end();
		}

		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 5 == -1 ? 0 : 5;
		$page    = $current !== null ? $current : 1;
		$start_from   = ($page-1) * $limit;

		$this->db->select('*');
		$this->db->from('suppliers as sup');
		$this->db->where('sup.removed', 0);
		$this->db->order_by("sup.suppliers_id", "asc");
		$query1 = $this->db->get();

		$this->db->select('*');
		$this->db->from('suppliers as sup');
		$this->db->where('sup.removed', 0);
		$this->db->order_by("sup.suppliers_id", "asc");
		$query2 = $this->db->limit( $limit, $start_from )->get();

		$data['data'] = $query2->result();
		$data['total'] = $query1->num_rows();

		echo json_encode($data);
	}

	public function archived($id = null)
	{
		if(!empty($this->input->get("search"))){
			$this->db->group_start();
			$this->db->like('suppliers_id', $this->input->get("search"));
			$this->db->or_like('suppliers_code', $this->input->get("search")); 
			$this->db->or_like('suppliers_name', $this->input->get("search"));
			$this->db->or_like('suppliers_desc', $this->input->get("search"));
			$this->db->group_end();
		}

		$current = null != $this->input->get('page') ? $this->input->get('page') : 1;
		$limit   = 5 == -1 ? 0 : 5;
		$page    = $current !== null ? $current : 1;
		$start_from   = ($page-1) * $limit;

		$this->db->select('*');
		$this->db->from('suppliers as sup');
		$this->db->where('sup.removed', 1);
		$this->db->order_by("sup.suppliers_id", "asc");
		$query1 = $this->db->get();

		$this->db->select('*');
		$this->db->from('suppliers as sup');
		$this->db->where('sup.removed', 1);
		$this->db->order_by("sup.suppliers_id", "asc");
		$query2 = $this->db->limit( $limit, $start_from )->get();

		$data['data'] = $query2->result();
		$data['total'] = $query1->num_rows();

		echo json_encode($data);
	}

	public function create($id = null)
	{	
		// if( $this->input->method() ) 
  //       {   
  //           $check = $this->suppliers_Model->check_last_inserted('suppliers');

  //           if(!($check > 0))
  //           {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$supplier = array(
                    'suppliers_code' => $this->input->post('suppliers_code'),
                    'suppliers_name' => $this->input->post('suppliers_name'),
                    'suppliers_desc' => $this->input->post('suppliers_desc'),
                    'suppliers_phone' => $this->input->post('suppliers_phone'),
                    'suppliers_mobile' => $this->input->post('suppliers_mobile'),
                    'suppliers_fax' => $this->input->post('suppliers_fax'),
                    'suppliers_address' => $this->input->post('suppliers_address'),
                    'suppliers_tin' => $this->input->post('suppliers_tin'),
                    'suppliers_bank_name' => $this->input->post('suppliers_bank_name'),
                    'suppliers_bank_no' => $this->input->post('suppliers_bank_name'),
                    'shipping_method_id' => $this->input->post('shipping_method_id'),
                    'payment_terms_id' => $this->input->post('payment_terms_id'),
                    'created_by' => 1,
                    'created_at' => $timestamp
                );
                
                $suppliers_id = $this->Suppliers_Model->insert($supplier);

				$history = array(
                    'history_logs' => 'has created a supplier',
                    'history_details' => 
					'{
						id: "' . $suppliers_id . '",
						code: "' . $this->input->post('suppliers_code') . '",
						name: "' . $this->input->post('suppliers_name') . '",
						desc: "' . $this->input->post('suppliers_desc') . '",
						phone: "' . $this->input->post('suppliers_phone') . '",
						mobile: "' . $this->input->post('suppliers_mobile') . '"
						fax: "' . $this->input->post('suppliers_fax') . '",
						address: "' . $this->input->post('suppliers_address') . '",
						tin: "' . $this->input->post('suppliers_tin') . '",
						bank name: "' . $this->input->post('suppliers_bank_name') . '",
						bank no: "' . $this->input->post('suppliers_bank_no') . '",
						shipping method: "' . $this->input->post('shipping_method_id') . '",
						payment terms: "' . $this->input->post('payment_terms_id') . '"
					}',
                    'history_table' => 'suppliers',
                    'history_table_id' => $suppliers_id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('supplier-management/view/'.$suppliers_id)
                );

                $history_id = $this->History_Model->insert($history);

                $data = array(
                	'id'      => $suppliers_id,
                	'header'  => 'Sweet',
	                'message' => 'The supplier was been successfully saved.',
	                'type'    => 'success'
	            );

	            echo json_encode( $data ); exit();		
		// 	}
		// }
	}

	public function update($id = null)
	{	
		if( $this->input->method() ) 
        {   
            $check = $this->History_Model->check_last_modified('suppliers');

            if(!($check > 0))
            {
				$_POST = json_decode(file_get_contents('php://input'), true);
				$posts = $this->input->post();

				$timestamp = date('Y-m-d H:i:s');

				$supplier = array(
                    'suppliers_code' => $this->input->post('suppliers_code'),
                    'suppliers_name' => $this->input->post('suppliers_name'),
                    'suppliers_desc' => $this->input->post('suppliers_desc'),
                    'suppliers_phone' => $this->input->post('suppliers_phone'),
                    'suppliers_mobile' => $this->input->post('suppliers_mobile'),
                    'suppliers_fax' => $this->input->post('suppliers_fax'),
                    'suppliers_address' => $this->input->post('suppliers_address'),
                    'suppliers_tin' => $this->input->post('suppliers_tin'),
                    'suppliers_bank_name' => $this->input->post('suppliers_bank_name'),
                    'suppliers_bank_no' => $this->input->post('suppliers_bank_name'),
                    'shipping_method_id' => $this->input->post('shipping_method_id'),
                    'payment_terms_id' => $this->input->post('payment_terms_id')
                );
                
                $this->Suppliers_Model->modify($supplier, $id);

				$history = array(
                    'history_logs' => 'has updated a supplier',
                    'history_details' => 
					'{
						id: "' . $id . '",
						code: "' . $this->input->post('suppliers_code') . '",
						name: "' . $this->input->post('suppliers_name') . '",
						desc: "' . $this->input->post('suppliers_desc') . '",
						phone: "' . $this->input->post('suppliers_phone') . '",
						mobile: "' . $this->input->post('suppliers_mobile') . '"
						fax: "' . $this->input->post('suppliers_fax') . '",
						address: "' . $this->input->post('suppliers_address') . '",
						tin: "' . $this->input->post('suppliers_tin') . '",
						bank name: "' . $this->input->post('suppliers_bank_name') . '",
						bank no: "' . $this->input->post('suppliers_bank_no') . '",
						shipping method: "' . $this->input->post('shipping_method_id') . '",
						payment terms: "' . $this->input->post('payment_terms_id') . '"
					}',
                    'history_table' => 'suppliers',
                    'history_table_id' => $id,
                    'history_timestamp' => $timestamp,
                    'history_by' => 1,
                    'history_slug' => base_url('supplier-management/views/'.$id)
                );

                $history_id = $this->History_Model->insert($history);

                $q = $this->db->get_where('suppliers', array('suppliers_id' => $id));

                $data = array(
                	'id'      => $id,
                	'datas'   => $q->row(),
                	'header'  => 'Sweet',
	                'message' => 'The supplier was been successfully updated.',
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
			$q = $this->db->get_where('suppliers', array('suppliers_id' => $id));
			echo json_encode($q->row());
		}
    }

    public function delete($id = null)
	{	
		if( $this->input->method() ) 
        {  
	    	$status = array(
	            'removed' => 1
	        );

	    	$this->db->where('suppliers_id', $id);	
	    	$this->db->update('suppliers', $status);

	    	$history = array(
	            'history_logs' => 'has deleted a supplier',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'suppliers',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('supplier-management/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('suppliers', array('suppliers_id' => $id));
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

	    	$this->db->where('suppliers_id', $id);	
	    	$this->db->update('suppliers', $status);

	    	$history = array(
	            'history_logs' => 'has restored a supplier',
	            'history_details' => 
				'{
					id: "' . $id . '"
				}',
	            'history_table' => 'suppliers',
	            'history_table_id' => $id,
	            'history_timestamp' => date('Y-m-d H:i:s'),
	            'history_by' => 1,
	            'history_slug' => base_url('supplier-management/view/'.$id)
	        );

	        $history_id = $this->History_Model->insert($history);

	        $q = $this->db->get_where('suppliers', array('suppliers_id' => $id));
			echo json_encode($q->row());
		}
	}
}
