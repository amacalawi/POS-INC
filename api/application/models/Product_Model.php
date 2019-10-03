<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_Model extends CI_Model {

    private $productTable = 'product';
    private $productColumn = 'product_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->productTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->productColumn, $id);
        $this->db->update($this->productTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->productColumn, $id);
        $this->db->update($this->productTable, $data);
        return true;
    }

    public function fetch($id)
    {
        $this->db->where($this->productColumn, $id);
        return $this->db->get($this->productTable)->row();
    }
    
    public function find($id)
    {   
        $this->db->select('sh.monday, sh.tuesday, sh.wednesday, sh.thursday, sh.friday, sh.saturday, prod.group_id, prod.gl_accounts_id, gl.code as gl_code, gl.name as gl_name, prod.product_id, prod.product_code, prod.product_name, prod.product_quantity, prod.product_desc, prod.product_price, prod.product_new, prod.product_discount, prod.product_discount_percentage, prod.product_img');
        $this->db->from('product as prod');
        $this->db->join('gl_accounts as gl', 'prod.gl_accounts_id = gl.id');
        $this->db->join('product_show as sh', 'prod.product_id = sh.product_id');
        $this->db->where('prod.product_id', $id);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr = array(
                    'product_id'   => $row->product_id,
                    'product_code' => $row->product_code,
                    'product_name' => $row->product_name,
                    'product_desc' => $row->product_desc,
                    'product_price' => $row->product_price,
                    'product_quantity' => $row->product_quantity,
                    'product_new' => $row->product_new, 
                    'product_discount' => $row->product_discount,
                    'product_img' => $row->product_img,
                    'product_discount_percentage' => $row->product_discount_percentage,
                    'gl_accounts_id' => $row->gl_accounts_id,
                    'group_id' => $row->group_id,
                    'd1' => $row->monday,
                    'd2' => $row->tuesday,
                    'd3' => $row->wednesday,
                    'd4' => $row->thursday,
                    'd5' => $row->friday,
                    'd6' => $row->saturday
                );
            }
        }

        return $arr;
    }

    public function get_product_info_by_id($id)
    {
        $this->db->where($this->productColumn, $id);
        return $this->db->get($this->productTable)->row();   
    }

    public function find_code($code)
    {   
        $this->db->select('gl.code as gl_code, gl.name as gl_name, prod.product_id, prod.product_code, prod.product_name, prod.product_desc, prod.product_price, prod.product_new, prod.product_discount, prod.product_discount_percentage, prod.product_img');
        $this->db->from('product as prod');
        $this->db->join('gl_accounts as gl', 'prod.gl_accounts_id = gl.id');
        $this->db->where('prod.product_code', $code);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr = array(
                    'message' => 'success',
                    'product_id'   => $row->product_id,
                    'product_code' => $row->product_code,
                    'product_name' => $row->product_name,
                    'product_desc' => $row->product_desc,
                    'product_price' => $row->product_price,
                    'product_new' => $row->product_new, 
                    'product_discount' => $row->product_discount,
                    'product_discount_percentage' => $row->product_discount_percentage,
                    'gl_accounts_id' => $row->gl_code.' ('.$row->gl_name.')'
                );
            }
        }
        else
        {
            $arr = array(
               'message' => 'error'
            );
        }

        return $arr;
    }

    public function get_all_product_list($wildcard = '', $limit, $start_from, $page = '')
    {  
        $this->db->select('prod.product_id, prod.product_code, prod.product_name, prod.product_desc, prod.product_price, prod.product_quantity, uom.code as uom, uom.id as uom_id, prod_cat.product_category_name as category, gl.id as gl_id, gl.code as gl_code, gl.name as gl_name, prod.product_discount_percentage');
        $this->db->from('product as prod');
        $this->db->join('product_category as prod_cat', 'prod.product_category_id = prod_cat.product_category_id');
        $this->db->join('gl_accounts as gl', 'prod.gl_accounts_id = gl.id');
        $this->db->join('unit_of_measurement as uom', 'prod.unit_of_measurement_id = uom.id');
        if(!empty($page)) {
            $this->db->where('prod_cat.product_category_name', $page);
        }
        $this->db->where('prod.removed', 0);
        if(!empty($wildcard)) {
            if(!empty($page)) {
                $this->db->group_start();
                $this->db->or_where('prod.product_id LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_code LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_name LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_desc LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_price LIKE', '%' . $wildcard . '%');
                $this->db->group_end();
            } else {
                $this->db->group_start();
                $this->db->or_where('prod.product_id LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_code LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_name LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_quantity LIKE', '%' . $wildcard . '%');
                $this->db->or_where('gl.code LIKE', '%' . $wildcard . '%');
                $this->db->or_where('gl.name LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod_cat.product_category_name LIKE', '%' . $wildcard . '%');
                $this->db->group_end();
            }
        } 
        $this->db->order_by("prod.product_id", "desc");
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result();
    }

    public function get_all_product_pagination($wildcard = '', $page = '', $user = '')
    {
        $this->db->select('*');
        $this->db->from('product as prod');
        $this->db->join('product_category as prod_cat', 'prod.product_category_id = prod_cat.product_category_id');
        $this->db->join('gl_accounts as gl', 'prod.gl_accounts_id = gl.id');
        $this->db->join('unit_of_measurement as uom', 'prod.unit_of_measurement_id = uom.id');
        if(!empty($page)) {
            $this->db->where('prod_cat.product_category_name', $page);
        }
        $this->db->where('prod.removed', 0);
        if(!empty($wildcard)) {
            if(!empty($page)) {
                $this->db->group_start();
                $this->db->or_where('prod.product_id LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_code LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_name LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_desc LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_price LIKE', '%' . $wildcard . '%');
                $this->db->group_end();
            } else {
                $this->db->group_start();
                $this->db->or_where('prod.product_id LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_code LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_name LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod.product_quantity LIKE', '%' . $wildcard . '%');
                $this->db->or_where('gl.code LIKE', '%' . $wildcard . '%');
                $this->db->or_where('gl.name LIKE', '%' . $wildcard . '%');
                $this->db->or_where('prod_cat.product_category_name LIKE', '%' . $wildcard . '%');
                $this->db->group_end();
            }
        } 
        $this->db->order_by("prod.product_id", "desc");
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_archived_product_list($wildcard = '', $limit, $start_from, $page = '')
    {   
        $this->db->select('*');
        $this->db->from('product as prod');
        $this->db->join('product_category as prod_cat', 'prod.product_category_id = prod_cat.product_category_id');
        $this->db->where('prod_cat.product_category_name', $page);
        $this->db->where('prod.removed', 1);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('prod.product_id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prod.product_code LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prod.product_name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prod.product_desc LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prod.product_price LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("prod.product_id", "desc");
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result();
    }

    public function get_all_archived_product_pagination($wildcard = '', $page = '')
    {
        $this->db->select('*');
        $this->db->from('product as prod');
        $this->db->join('product_category as prod_cat', 'prod.product_category_id = prod_cat.product_category_id');
        $this->db->where('prod_cat.product_category_name', $page);
        $this->db->where('prod.removed', 1);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('prod.product_id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prod.product_code LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prod.product_name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prod.product_desc LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prod.product_price LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("prod.product_id", "desc");
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->productColumn, 'DESC');
        $query = $this->db->get($this->productTable, 1);

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
        $this->db->order_by($this->productColumn, 'DESC');
        $query = $this->db->get($this->productTable, 1);

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

    public function debit_inventory($id, $quantity)
    {   
        $this->db->where($this->productColumn, $id);
        $inventory = $this->db->get($this->productTable)->row()->product_quantity;

        $data = array(
            'product_quantity' => floatval($inventory) - floatval($quantity)
        );

        $this->db->where($this->productColumn, $id);
        $this->db->update($this->productTable, $data);
        return true;
    }
}