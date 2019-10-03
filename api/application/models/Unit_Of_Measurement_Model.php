<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Unit_Of_Measurement_Model extends CI_Model {

    private $unit_of_measurementTable = 'unit_of_measurement';
    private $unit_of_measurementColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->unit_of_measurementTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->unit_of_measurementColumn, $id);
        $this->db->update($this->unit_of_measurementTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->unit_of_measurementColumn, $id);
        $this->db->update($this->unit_of_measurementTable, $data);
        return true;
    }

    public function display_all_active_uom()
    {   
        $this->db->where('removed', 0);
        $this->db->where($this->unit_of_measurementColumn.' !=', 0);
        $query = $this->db->get($this->unit_of_measurementTable);

        $arr = array();

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] = array(
                    'name' => $row->code,
                    'value' => $row->id
                );
            }
        }
        
        return $arr;
    }

    public function get_uom_desc_by_id($id)
    {
        $this->db->where($this->unit_of_measurementColumn, $id);
        return $this->db->get($this->unit_of_measurementTable)->row()->desc;
    }

    public function get_all_unit_of_measurement_list($wildcard = '', $limit, $start_from)
    {   
        $this->db->select('*');
        $this->db->from('unit_of_measurement as uom');
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

    public function get_all_unit_of_measurement_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from('unit_of_measurement as uom');
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

    public function get_all_archived_unit_of_measurement_list($wildcard = '', $limit, $start_from)
    {   
        $this->db->select('*');
        $this->db->from('unit_of_measurement as uom');
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

    public function get_all_archived_unit_of_measurement_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from('unit_of_measurement as uom');
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

    public function check_last_inserted()
    {          
        $this->db->order_by($this->unit_of_measurementColumn, 'DESC');
        $query = $this->db->get($this->unit_of_measurementTable, 1);

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
        $this->db->order_by($this->unit_of_measurementColumn, 'DESC');
        $query = $this->db->get($this->unit_of_measurementTable, 1);

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