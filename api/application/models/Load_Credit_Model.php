<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Load_Credit_Model extends CI_Model {

    private $load_creditTable = 'load_credit';
    private $load_creditColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->load_creditTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->load_creditColumn, $id);
        $this->db->update($this->load_creditTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->load_creditColumn, $id);
        $this->db->update($this->load_creditTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->load_creditColumn, 'DESC');
        $query = $this->db->get($this->load_creditTable, 1);

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
        $this->db->order_by($this->load_creditColumn, 'DESC');
        $query = $this->db->get($this->load_creditTable, 1);

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
    
    
}