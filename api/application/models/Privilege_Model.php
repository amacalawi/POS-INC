<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Privilege_Model extends CI_Model {

    private $privilegesTable = 'privilege';
    private $privilegesColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->privilegesTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->privilegesColumn, $id);
        $this->db->update($this->privilegesTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->privilegesColumn, $id);
        $this->db->update($this->privilegesTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->privilegesColumn, 'DESC');
        $query = $this->db->get($this->privilegesTable, 1);

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
        $this->db->order_by($this->privilegesColumn, 'DESC');
        $query = $this->db->get($this->privilegesTable, 1);

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
    
    public function get_all_privilege()
    {
        $this->db->select('*');
        $this->db->from('privilege as priv');
        $this->db->where('priv.removed', 0);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] = array(
                    'name' => $row->name,
                    'value' => $row->id
                );
            }
        }

        return $arr;
    }

    public function get_privilege_by_id($id)
    {
        $this->db->where($this->privilegesColumn, $id);
        return $this->db->get($this->privilegesTable)->row()->name;
    }
}