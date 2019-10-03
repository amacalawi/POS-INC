<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class customers_Model extends CI_Model {

    private $customersTable = 'customers';
    private $customersColumn = 'customer_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->customersTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->customersColumn, $id);
        $this->db->update($this->customersTable, $data);
        return true;
    }

    public function search_member_via_keywords($keywords)
    {
        $this->db->select('*');
        $this->db->from('customers as cus');
        if(!empty($keywords)){
            $this->db->where('cus.customer_barcode', $keywords);
        }
        $this->db->order_by("cus.customer_id", "asc");
        $this->db->group_by('cus.customer_id');
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $discounts = $row->customer_discount ? $row->customer_discount : 0;

                $arr[] = array(
                    'searches' => 'true',
                    'fullname' => $row->customer_barcode,
                    'stud_no'  => $row->customer_barcode,
                    'credits'  => $row->customer_credits,
                    'discounts'  => $discounts
                );
            }
        } else {
            $arr[] = array(
                'searches' => 'false'
            ); 
        }

        return $arr;
    }

    public function deduct_credit($barcode, $total_payments)
    {
        $this->db->where('customer_barcode', $barcode);
        $query = $this->db->get('customers');

        $arr = array();
        
        $new_credits = 0;

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $new_credits = floatval($row->customer_credits) - floatval($total_payments);

                $data = array(
                    'customer_credits' => $new_credits
                );

                $this->db->where('customer_barcode', $barcode);
                $this->db->update('customers', $data);                
            }
        }
        
        $arr[] = array(
            'credits' => $new_credits
        );

        return $arr;
    }
}