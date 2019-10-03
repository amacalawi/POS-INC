<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

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
			$this->db->from('settings as sets');
			$this->db->where('sets.removed', 0);
			$this->db->where('sets.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('sets.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('sets.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('sets.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('sets.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("sets.id", "asc");
			$query1 = $this->db->get();

			$this->db->select('*');
			$this->db->from('settings as sets');
			$this->db->where('sets.removed', 0);
			$this->db->where('sets.id !=', 0);
			if(!empty($wildcard)){
				$this->db->group_start();
		        $this->db->or_where('sets.id LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('sets.code LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('sets.name LIKE', '%' . $wildcard . '%');
		        $this->db->or_where('sets.desc LIKE', '%' . $wildcard . '%');
		        $this->db->group_end();
			}
			$this->db->order_by("sets.id", "asc");
			$query2 = $this->db->limit( $limit, $start_from )->get();

			$data['data'] = $query2->result();
			$data['total'] = $query1->num_rows();

			echo json_encode($data);
		}
	}
}
