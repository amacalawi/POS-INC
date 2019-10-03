<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_Item_Model extends CI_Model {

    private $product_itemTable = 'product_item';
    private $product_itemColumn = 'product_item_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->product_itemTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->product_itemColumn, $id);
        $this->db->update($this->product_itemTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->product_itemColumn, $id);
        $this->db->update($this->product_itemTable, $data);
        return true;
    }

    public function get_all_product_item_list($wildcard = '', $limit, $start_from, $prod_Id = '')
    {   
        $this->db->select('*');
        $this->db->from('product_item as prod_item');
        $this->db->join('product as prod', 'prod_item.product_id = prod.product_id');
        $this->db->join('item as it', 'prod_item.item_id = it.item_id');
        $this->db->where('prod_item.removed', 0);
        if($prod_Id != 0) {
            $this->db->where('prod.product_id', $prod_Id);
        }
        // if(!empty($wildcard)){
        //     $this->db->group_start();
        //     $this->db->or_where('prod.product_item_id LIKE', '%' . $wildcard . '%');
        //     $this->db->or_where('prod.product_item_code LIKE', '%' . $wildcard . '%');
        //     $this->db->or_where('prod.product_item_name LIKE', '%' . $wildcard . '%');
        //     $this->db->or_where('prod.product_item_desc LIKE', '%' . $wildcard . '%');
        //     $this->db->or_where('prod.product_item_price LIKE', '%' . $wildcard . '%');
        //     $this->db->group_end();
        // }
        $this->db->order_by("prod_item.product_item_id", "desc");
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result();
    }

    public function get_all_product_item_pagination($wildcard = '', $prod_Id = '')
    {
        $this->db->select('*');
        $this->db->from('product_item as prod_item');
        $this->db->join('product as prod', 'prod_item.product_id = prod.product_id');
        $this->db->join('item as it', 'prod_item.item_id = it.item_id');
        $this->db->where('prod_item.removed', 0);
        if($prod_Id != 0) {
            $this->db->where('prod.product_id', $prod_Id);
        }
        // if(!empty($wildcard)){
        //     $this->db->group_start();
        //     $this->db->or_where('prod.product_item_id LIKE', '%' . $wildcard . '%');
        //     $this->db->or_where('prod.product_item_code LIKE', '%' . $wildcard . '%');
        //     $this->db->or_where('prod.product_item_name LIKE', '%' . $wildcard . '%');
        //     $this->db->or_where('prod.product_item_desc LIKE', '%' . $wildcard . '%');
        //     $this->db->or_where('prod.product_item_price LIKE', '%' . $wildcard . '%');
        //     $this->db->group_end();
        // }
        $this->db->order_by("prod_item.product_item_id", "desc");
        $query = $this->db->get();
        return $query->num_rows();
    }

    // public function get_all_archived_product_item_list($wildcard = '', $limit, $start_from, $page = '')
    // {   
    //     $this->db->select('*');
    //     $this->db->from('product_item as prod');
    //     $this->db->join('product_item_category as prod_cat', 'prod.product_item_category_id = prod_cat.product_item_category_id');
    //     $this->db->where('prod_cat.product_item_category_name', $page);
    //     $this->db->where('prod.removed', 1);
    //     if(!empty($wildcard)){
    //         $this->db->group_start();
    //         $this->db->or_where('prod.product_item_id LIKE', '%' . $wildcard . '%');
    //         $this->db->or_where('prod.product_item_code LIKE', '%' . $wildcard . '%');
    //         $this->db->or_where('prod.product_item_name LIKE', '%' . $wildcard . '%');
    //         $this->db->or_where('prod.product_item_desc LIKE', '%' . $wildcard . '%');
    //         $this->db->or_where('prod.product_item_price LIKE', '%' . $wildcard . '%');
    //         $this->db->group_end();
    //     }
    //     $this->db->order_by("prod.product_item_id", "desc");
    //     $query = $this->db->limit( $limit, $start_from )->get();
    //     return $query->result();
    // }

    // public function get_all_archived_product_item_pagination($wildcard = '', $page = '')
    // {
    //     $this->db->select('*');
    //     $this->db->from('product_item as prod');
    //     $this->db->join('product_item_category as prod_cat', 'prod.product_item_category_id = prod_cat.product_item_category_id');
    //     $this->db->where('prod_cat.product_item_category_name', $page);
    //     $this->db->where('prod.removed', 1);
    //     if(!empty($wildcard)){
    //         $this->db->group_start();
    //         $this->db->or_where('prod.product_item_id LIKE', '%' . $wildcard . '%');
    //         $this->db->or_where('prod.product_item_code LIKE', '%' . $wildcard . '%');
    //         $this->db->or_where('prod.product_item_name LIKE', '%' . $wildcard . '%');
    //         $this->db->or_where('prod.product_item_desc LIKE', '%' . $wildcard . '%');
    //         $this->db->or_where('prod.product_item_price LIKE', '%' . $wildcard . '%');
    //         $this->db->group_end();
    //     }
    //     $this->db->order_by("prod.product_item_id", "desc");
    //     $query = $this->db->get();
    //     return $query->num_rows();
    // }
}