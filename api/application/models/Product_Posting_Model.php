<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_Posting_Model extends CI_Model {

    private $product_postingTable = 'product_posting';
    private $product_postingColumn = 'posting_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->product_postingTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->product_postingColumn, $id);
        $this->db->update($this->product_postingTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->product_postingColumn, $id);
        $this->db->update($this->product_postingTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->product_postingColumn, 'DESC');
        $query = $this->db->get($this->product_postingTable, 1);

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
        $this->db->order_by($this->product_postingColumn, 'DESC');
        $query = $this->db->get($this->product_postingTable, 1);

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

    public function get_posted_qty_via_date($product, $dateFrom, $dateTo)
    {
        $this->db->select('SUM(quantity) as quantity1');
        $this->db->from('product_posting');
        $this->db->where('product_id', $product);
        $this->db->where('inventory_adjustment', 'Additional Inventory');
        $this->db->where('posting_datetime BETWEEN "'. date('Y-m-d H:i:s', strtotime($dateFrom.' 00:00:00')). '" and "'. date('Y-m-d H:i:s', strtotime($dateTo.' 23:59:59')).'"');
        $query1 = $this->db->get();

        $quantity1 = 0;
        if($query1->num_rows() > 0)
        {
            foreach ($query1->result() as $row1) {
                $quantity1 = $row1->quantity1;
            }
        }

        $this->db->select('SUM(quantity) as quantity2');
        $this->db->from('product_posting');
        $this->db->where('product_id', $product);
        $this->db->where('posting_datetime BETWEEN "'. date('Y-m-d H:i:s', strtotime($dateFrom.' 00:00:00')). '" and "'. date('Y-m-d H:i:s', strtotime($dateTo.' 23:59:59')).'"');
        $this->db->where('inventory_adjustment', 'Deduction Inventory');
        $query2 = $this->db->get();

        $quantity2 = 0;
        if($query2->num_rows() > 0)
        {
            foreach ($query2->result() as $row2) {
                $quantity2 = $row2->quantity2;
            }
        }

        return (floatval($quantity1) - floatval($quantity2));
    }
}