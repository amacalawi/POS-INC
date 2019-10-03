<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Groups_Model extends CI_Model {

    private $groupsTable = 'groups';
    private $groupsColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->groupsTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->groupsColumn, $id);
        $this->db->update($this->groupsTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->groupsColumn, $id);
        $this->db->update($this->groupsTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->groupsColumn, 'DESC');
        $query = $this->db->get($this->groupsTable, 1);

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
        $this->db->order_by($this->groupsColumn, 'DESC');
        $query = $this->db->get($this->groupsTable, 1);

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
    
    public function get_groups_desc_by_id($id)
    {
        $this->db->where($this->groupsColumn, $id);
        return $this->db->get($this->groupsTable)->row()->desc;
    }

    public function display_all_active_groups()
    {   
        $this->db->where('removed', 0);
        $this->db->where('id !=', 0);
        $query = $this->db->get($this->groupsTable);

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] =  array(
                    'name' => $row->name,
                    'value' => $row->id
                );
            }
        }
        
        return $arr;
    }

    public function get_all_groups_list($wildcard = '', $limit, $start_from)
    {   
        $this->db->select('*');
        $this->db->from('groups as uom');
        $this->db->where('uom.removed', 0);
        $this->db->where('uom.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('uom.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.code LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.desc LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("uom.id", "asc");
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result();
    }

    public function get_all_groups_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from('groups as uom');
        $this->db->where('uom.removed', 0);
        $this->db->where('uom.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('uom.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.code LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.desc LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("uom.id", "asc");
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_archived_groups_list($wildcard = '', $limit, $start_from)
    {   
        $this->db->select('*');
        $this->db->from('groups as uom');
        $this->db->where('uom.removed', 1);
        $this->db->where('uom.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('uom.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.code LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.desc LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("uom.id", "asc");
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result();
    }

    public function get_all_archived_groups_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from('groups as uom');
        $this->db->where('uom.removed', 1);
        $this->db->where('uom.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('uom.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.code LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.desc LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("uom.id", "asc");
        $query = $this->db->get();
        return $query->num_rows();
    }
}