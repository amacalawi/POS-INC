<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shipping_Method_Model extends CI_Model {

    private $shipping_methodTable = 'shipping_method';
    private $shipping_methodColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->shipping_methodTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->shipping_methodColumn, $id);
        $this->db->update($this->shipping_methodTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->shipping_methodColumn, $id);
        $this->db->update($this->shipping_methodTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->shipping_methodColumn, 'DESC');
        $query = $this->db->get($this->shipping_methodTable, 1);

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
        $this->db->order_by($this->shipping_methodColumn, 'DESC');
        $query = $this->db->get($this->shipping_methodTable, 1);

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
    
    public function get_shipping_method_desc_by_id($id)
    {
        $this->db->where($this->shipping_methodColumn, $id);
        return $this->db->get($this->shipping_methodTable)->row()->shipping_method_desc;
    }
}