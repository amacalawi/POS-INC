<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Load extends CI_Controller {

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
            'History_Model' => 'History_Model',
            'Load_Credit_Model' => 'Load_Credit_Model'
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

			$data['data'] = $this->Load_Model->get_all_member_list($wildcard, $limit, $start_from);
			$data['total'] = $this->Load_Model->get_all_member_pagination($wildcard);

			echo json_encode($data);
		}
	}

    public function store($id = null)
    {
        // if( $this->input->method() ) 
        // {
            $credits = array(
                'load_credits' => $this->input->get('credit'),
                'stud_no'   => $this->input->get('stud_no'),
                'created_at' => $this->input->get('timestamp'),
                'created_by' => $this->input->get('user_id')
            );

            $credits_id = $this->Load_Credit_Model->insert($credits);

            $history = array(
                'history_logs' => 'has checked out a transactions',
                'history_details' => 
                '{
                    id: "' . $credits_id . '"
                }',
                'history_table' => 'load_credit',
                'history_table_id' => $credits_id,
                'history_timestamp' => $this->input->get('timestamp'),
                'history_by' => $this->input->get('user_id'),
                'history_slug' => base_url('load-credit/view/'.$credits_id)
            );

            $history_id = $this->History_Model->insert($history);

            $data = array(
                'header'    => 'Sweet',
                'message'   => 'The transaction was been successfully done.',
                'type'      => 'success'
            );

            echo json_encode($data);
        // }
    }
}
