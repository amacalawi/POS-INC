<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_Show_Model extends CI_Model {

    private $product_showTable = 'product_show';
    private $product_showColumn = 'product_show_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->product_showTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->product_showColumn, $id);
        $this->db->update($this->product_showTable, $data);
        return true;
    }

    public function modify_by_product($data, $id)
    {
        $this->db->where('product_id', $id);
        $this->db->update($this->product_showTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->product_showColumn, $id);
        $this->db->update($this->product_showTable, $data);
        return true;
    }

    public function fetch($id)
    {
        $this->db->where($this->product_showColumn, $id);
        return $this->db->get($this->product_showTable)->row();
    }
    
    public function find($id)
    {   
        $this->db->select('gl.code as gl_code, gl.name as gl_name, prod.product_show_id, prod.product_show_code, prod.product_show_name, prod.product_show_quantity, prod.product_show_desc, prod.product_show_price, prod.product_show_new, prod.product_show_discount, prod.product_show_discount_percentage, prod.product_show_img');
        $this->db->from('product_show as prod');
        $this->db->join('gl_accounts as gl', 'prod.gl_accounts_id = gl.id');
        $this->db->where('prod.product_show_id', $id);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr = array(
                    'product_show_id'   => $row->product_show_id,
                    'product_show_code' => $row->product_show_code,
                    'product_show_name' => $row->product_show_name,
                    'product_show_desc' => $row->product_show_desc,
                    'product_show_price' => $row->product_show_price,
                    'product_show_quantity' => $row->product_show_quantity,
                    'product_show_new' => $row->product_show_new, 
                    'product_show_discount' => $row->product_show_discount,
                    'product_show_img' => $row->product_show_img,
                    'product_show_discount_percentage' => $row->product_show_discount_percentage,
                    'gl_accounts_id' => $row->gl_code.' ('.$row->gl_name.')'
                );
            }
        }

        return $arr;
    }

    public function get_product_show_info_by_id($id)
    {
        $this->db->where($this->product_showColumn, $id);
        return $this->db->get($this->product_showTable)->row();   
    }
}