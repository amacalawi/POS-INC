<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Item_Model extends CI_Model {

    private $itemTable = 'item';
    private $itemColumn = 'item_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->itemTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->itemColumn, $id);
        $this->db->update($this->itemTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->itemColumn, $id);
        $this->db->update($this->itemTable, $data);
        return true;
    }

    public function find($id)
    {   
        $this->db->select('*');
        $this->db->from('item as it');
        $this->db->join('gl_accounts as gl', 'it.gl_accounts_id = gl.id');
        $this->db->where('it.'.$this->itemColumn, $id);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr = array(
                    'item_id' => $row->item_id,
                    'item_code' => $row->item_code,
                    'item_name' => $row->item_name,
                    'item_desc' => $row->item_desc,
                    'item_img' => $row->item_img,
                    'unit_of_measurement_id' => $row->unit_of_measurement_id,
                    'gl_accounts_id' => $row->code.' ('.$row->name.')'
                );
            }
        }
        
        return $arr;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->itemColumn, 'DESC');
        $query = $this->db->get($this->itemTable, 1);

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
        $this->db->order_by($this->itemColumn, 'DESC');
        $query = $this->db->get($this->itemTable, 1);

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

    public function get_all_item_list($wildcard = '', $limit, $start_from)
    {
        $this->db->select('it.item_id, it.item_code, it.item_name, it.item_quantity, uom.code as uom_code, it.item_img');
        $this->db->from('item as it');
        $this->db->join('unit_of_measurement as uom', 'it.unit_of_measurement_id = uom.id');
        $this->db->join('gl_accounts as gl', 'it.gl_accounts_id = gl.id');
        $this->db->where('it.removed', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('it.item_id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('it.item_name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('it.item_quantity LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.code LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("it.item_id", "asc");
        $this->db->group_by('it.item_id');
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result(); 
    }

    public function get_all_item_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from('item as it');
        $this->db->join('unit_of_measurement as uom', 'it.unit_of_measurement_id = uom.id');
        $this->db->join('gl_accounts as gl', 'it.gl_accounts_id = gl.id');
        $this->db->where('it.removed', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('it.item_id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('it.item_name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('it.item_quantity LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.code LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("it.item_id", "asc");
        $this->db->group_by('it.item_id');
        $query = $this->db->get();
        return $query->num_rows(); 
    }

    public function get_all_archived_item_list($wildcard = '', $limit, $start_from)
    {
        $this->db->select('it.item_id, it.item_code, it.item_name, it.item_quantity, uom.code as uom_code, it.item_img');
        $this->db->from('item as it');
        $this->db->join('unit_of_measurement as uom', 'it.unit_of_measurement_id = uom.id');
        $this->db->join('gl_accounts as gl', 'it.gl_accounts_id = gl.id');
        $this->db->where('it.removed', 1);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('it.item_id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('it.item_name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('it.item_quantity LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.code LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("it.item_id", "asc");
        $this->db->group_by('it.item_id');
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result(); 
    }

    public function get_all_archived_item_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from('item as it');
        $this->db->join('unit_of_measurement as uom', 'it.unit_of_measurement_id = uom.id');
        $this->db->join('gl_accounts as gl', 'it.gl_accounts_id = gl.id');
        $this->db->where('it.removed', 1);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('it.item_id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('it.item_name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('it.item_quantity LIKE', '%' . $wildcard . '%');
            $this->db->or_where('uom.code LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("it.item_id", "asc");
        $this->db->group_by('it.item_id');
        $query = $this->db->get();
        return $query->num_rows(); 
    }
}