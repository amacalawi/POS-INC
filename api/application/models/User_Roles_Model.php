<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_Roles_Model extends CI_Model {

    private $user_rolesTable = 'user_roles';
    private $user_rolesColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->user_rolesTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->user_rolesColumn, $id);
        $this->db->update($this->user_rolesTable, $data);
        return true;
    }

    public function modify_user($data, $id)
    {   
        $this->db->where('user_id', $id);
        $this->db->update($this->user_rolesTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->user_rolesColumn, $id);
        $this->db->update($this->user_rolesTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->user_rolesColumn, 'DESC');
        $query = $this->db->get($this->user_rolesTable, 1);

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
        $this->db->order_by($this->user_rolesColumn, 'DESC');
        $query = $this->db->get($this->user_rolesTable, 1);

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

    public function check_privilege_by_user($id)
    {
        $this->db->where('user_id', $id);
        $this->db->where('removed', 0);
        return $this->db->get($this->user_rolesTable)->row()->role_id;
    }
    
    public function remove_user_current_privilege($id)
    {
        $data = array(
            'removed' => 1
        );

        $this->db->where('user_id', $id);
        $this->db->update($this->user_rolesTable, $data);
        return true;
    }

    public function check_if_exist($user, $privilege)
    {
        $this->db->where('user_id', $user);
        $this->db->where('role_id', $privilege);
        $query = $this->db->get($this->user_rolesTable);

        $exist = 0;
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $exist = $row->id;
            }
        }

        return $exist;
    }
}