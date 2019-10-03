<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

    private $Data = array();

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
            'Customers_Model' => 'Customers_Model',
            'History_Model' => 'History_Model'
        );

        $this->load->model($models);  
    }

    public function index()
    {
        if( $this->input->method() ) 
        {
            $wildcard = $this->input->get("search");
            $current = null != $this->input->get('page') ? $this->input->get('page') : 1;
            $limit   = 5 == -1 ? 0 : 5;
            $page    = $current !== null ? $current : 1;
            $start_from   = ($page-1) * $limit;

            $data['data'] = $this->Customers_Model->get_all_member_list($wildcard, $limit, $start_from);
            $data['total'] = $this->Customers_Model->get_all_member_pagination($wildcard);

            echo json_encode($data);
        }
    }

    public function create($id = null)
    {   
        $timestamp = date('Y-m-d H:i:s');

        $credits = $this->Customers_Model->get_credit_by_id($id);
        $msisdn  = $this->Customers_Model->get_msisdn_by_id($id);

        $total_credits = floatval($credits) + floatval($this->input->get('credits'));

        $data = array(
            'credits' => $total_credits
        );

        $this->Customers_Model->modify($data, $id);

        $credit_info = array(
            'id' => $id,
            'stud_no' => $this->Customers_Model->get_stud_no_by_id($id),
            'credit' => $this->input->get('credits'),
            'timestamp' => $timestamp
        );

        $body = "You've succesfully purchased a load credit ".number_format($this->input->get('credits'), 2)." date ".date('d-M-Y H:i', strtotime($timestamp)).". Your new balance credit is ".number_format($total_credits, 2)." as of ".date('d-M-Y', strtotime($timestamp)).".";

        $this->Message->send('', trim($msisdn), $this->Message->get_network($msisdn), $body);

        $data = array(
            'id'      => $id,
            'datas'   => $credit_info, 
            'header'  => 'Success',
            'message' => 'The credit has been successfully added.',
            'type'    => 'success'
        );

        echo json_encode( $data ); exit();      
    }

    public function search_member_via_keywords($keywords = null)
    {   
        // if( $this->input->method() ) 
        // {
            $data = $this->Customers_Model->search_member_via_keywords($keywords);

            echo json_encode($data);
        // }
    }

    public function deduct_credit()
    {       
        $timestamp = date('Y-m-d H:i:s');
        // $msisdn  = $this->Customers_Model->get_msisdn_by_stud_no($this->input->get('stud_no'));
        $data['result'] = $this->Customers_Model->deduct_credit($this->input->get('barcode'), $this->input->get('total_payments'));

        foreach ($data['result'] as $result) {
            // $body = "You've succesfully purchased a goods with amount of ".number_format($this->input->get('total_payments'), 2)." date ".date('d-M-Y H:i', strtotime($timestamp))." from transaction no ".$this->input->get('trans_num').". Your new balance credit is ".number_format($result['credits'], 2)." as of ".date('d-M-Y', strtotime($timestamp)).".";
            // $this->Message->send('', trim($msisdn), $this->Message->get_network($msisdn), $body);
        }

        echo json_encode($data);
    }

    public function users_discount()
    {
        // if( $this->input->method() ) 
        // {
            $wildcard = $this->input->get("search");
            $current = null != $this->input->get('page') ? $this->input->get('page') : 1;
            $limit   = 5 == -1 ? 0 : 5;
            $page    = $current !== null ? $current : 1;
            $start_from   = ($page-1) * $limit;

            $data['data'] = $this->Customers_Model->get_all_members_discount_list($wildcard, $limit, $start_from);
            $data['total'] = $this->Customers_Model->get_all_members_discount_pagination($wildcard);

            echo json_encode($data);
        // }
    }

    public function get_all_users_discount($id = null)
    {   
        $arr = $this->Customers_Model->get_all_active_users_discount($id);

        echo json_encode( $arr );

        exit();
    }

    public function search_users_discount($id)
    {
        $arr['member'] = $this->Customers_Model->search_users_discount($id);

        echo json_encode( $arr );

        exit();
    }


    public function create_users_discount($id = null)
    {  
        $_POST = json_decode(file_get_contents('php://input'), true);
        $posts = $this->input->post();              
        $timestamp = date('Y-m-d H:i:s');

        $discounts = array(
            'member_id'  => $this->input->post('members_no'),
            'discount' => $this->input->post('members_discount'),
            'created_at' => $timestamp,
            'created_by' => $this->input->get('user_id')
        );

        $discounts_id = $this->Customers_Model->create_users_discount($discounts);

        $data = array(
            'header'    => "Sweet",
            'message'   => "The user's discount was been successfully added.",
            'type'      => "success"
        );

        echo json_encode( $data ); exit();
    }

    public function update_users_discount($id = null)
    {  
        $_POST = json_decode(file_get_contents('php://input'), true);
        $posts = $this->input->post();              
        $timestamp = date('Y-m-d H:i:s');

        $discounts = array(
            'discount' => $this->input->post('members_discount'),
            'updated_at' => $timestamp,
            'updated_by' => $this->input->get('user_id')
        );

        $this->Customers_Model->update_users_discount($discounts, $id);

        $data = array(
            'header'    => "Sweet",
            'message'   => "The user's discount was been successfully added.",
            'type'      => "success"
        );

        echo json_encode( $data ); exit();
    }

    public function edit_users_discount($id = null)
    {
        $q = $this->Customers_Model->edit_users_discount($id);
        echo json_encode($q);
    }
}