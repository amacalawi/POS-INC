<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_Terms_Model extends CI_Model {

    private $payment_termsTable = 'payment_terms';
    private $payment_termsColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->payment_termsTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->payment_termsColumn, $id);
        $this->db->update($this->payment_termsTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->payment_termsColumn, $id);
        $this->db->update($this->payment_termsTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->payment_termsColumn, 'DESC');
        $query = $this->db->get($this->payment_termsTable, 1);

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                if(date($row->created_at, time() + 5) > date('Y-m-d H:i:s'))
                {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    public function check_last_modified()
    {          
        $this->db->order_by($this->payment_termsColumn, 'DESC');
        $query = $this->db->get($this->payment_termsTable, 1);

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                if(date($row->updated_at, time() + 5) > date('Y-m-d H:i:s'))
                {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    public function get_payment_terms_desc_by_id($id)
    {
        $this->db->where($this->payment_termsColumn, $id);
        return $this->db->get($this->payment_termsTable)->row()->payment_terms_desc;
    }
}