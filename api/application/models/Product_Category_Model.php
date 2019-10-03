<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_Category_Model extends CI_Model {

    private $product_categoryTable = 'product_category';
    private $product_categoryColumn = 'product_category_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->product_categoryTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->product_categoryColumn, $id);
        $this->db->update($this->product_categoryTable, $data);
        return true;
    }

    public function find($id)
    {   
        $this->db->where($this->product_categoryColumn, $id);
        $query = $this->db->get($this->product_categoryTable);

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr = array(
                    'product_category_id'   => $row->product_category_id,
                    'product_category_code' => $row->product_category_code,
                    'product_category_name' => $row->product_category_name,
                    'product_category_desc' => $row->product_category_desc,
                    'product_category_slug' => $row->product_category_slug
                );
            }
        }

        return $arr;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->product_categoryColumn, $id);
        $this->db->update($this->product_categoryTable, $data);
        return true;
    }

    public function get_product_category_id_by_slug($slug)
    {
        $this->db->where('product_category_slug', $slug);
        $query = $this->db->get($this->product_categoryTable);

        $product_category_id = 0;

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $product_category_id = $row->product_category_id;
            }
        }

        return $product_category_id;
    }

    public function get_product_category_name_by_slug($slug)
    {
        $this->db->where('product_category_slug', $slug);
        $query = $this->db->get($this->product_categoryTable);

        $product_category_name = 0;

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $product_category_name = $row->product_category_name;
            }
        }

        return $product_category_name;
    }

    public function get_slug_by_prod_id($id)
    {   
        $this->db->select('*');
        $this->db->from($this->product_categoryTable.' as cat');
        $this->db->join('product as prod', 'cat.product_category_id = prod.product_category_id');
        $this->db->where('prod.product_id', $id);
        $query = $this->db->get();

        $slug = '';

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $slug = $row->product_category_slug;
            }
        }

        return $slug;
    }

    public function display_active_product_menus()
    {
        $this->db->where('removed', 0);
        $query = $this->db->get($this->product_categoryTable);

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] = array(
                    'id'   => $row->product_category_id,
                    'name' => $row->product_category_name,
                    'slug' => $row->product_category_slug
                );
            }
        }

        return $arr;
    }
}