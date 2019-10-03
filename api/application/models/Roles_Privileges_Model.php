<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Roles_Privileges_Model extends CI_Model {

    private $roles_privilegesTable = 'roles_privileges';
    private $roles_privilegesColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->roles_privilegesTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->roles_privilegesColumn, $id);
        $this->db->update($this->roles_privilegesTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->roles_privilegesColumn, $id);
        $this->db->update($this->roles_privilegesTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->roles_privilegesColumn, 'DESC');
        $query = $this->db->get($this->roles_privilegesTable, 1);

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
        $this->db->order_by($this->roles_privilegesColumn, 'DESC');
        $query = $this->db->get($this->roles_privilegesTable, 1);

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