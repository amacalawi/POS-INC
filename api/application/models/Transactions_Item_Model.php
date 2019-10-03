<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transactions_Item_Model extends CI_Model {

    private $transactions_itemTable = 'transactions_item';
    private $transactions_itemColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->transactions_itemTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->transactions_itemColumn, $id);
        $this->db->update($this->transactions_itemTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->transactions_itemColumn, $id);
        $this->db->update($this->transactions_itemTable, $data);
        return true;
    }

    public function removed($trans_id)
    {   
        $data = array(
            'removed' => 1
        );

        $this->db->where('transaction_id', $trans_id);
        $this->db->update($this->transactions_itemTable, $data);
        return true;
    }

    public function fetch_transactions($transaction)
    {
        $this->db->select('items.product_id, items.quantity, items.price, items.total, prod.product_name, items.discount');
        $this->db->from('transactions_item as items');
        $this->db->join('transactions as trans', 'items.transaction_id = trans.id');
        $this->db->join('product as prod', 'items.product_id = prod.product_id');
        $this->db->where('items.removed', 0);
        $this->db->where('trans.trans_no', $transaction);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] = array(
                    'id' => $row->product_id,
                    'name' => $row->product_name,
                    'quantity' => $row->quantity,
                    'price' => $row->price,
                    'total_price' => $row->total,
                    'discount' => $row->discount
                );
            }
        }

        return $arr;
    }

    public function get_sold_price_via_date($product, $dateFrom, $dateTo)
    {
        $this->db->select('SUM(items.quantity) as quantity, items.price as price, SUM(items.discount) as discount');
        $this->db->from('transactions_item as items');
        $this->db->join('transactions as trans', 'items.transaction_id = trans.id');
        $this->db->where('items.product_id', $product);
        $this->db->where('trans.created_at BETWEEN "'. date('Y-m-d '.'00:00:00', strtotime($dateFrom)). '" and "'. date('Y-m-d '.'23:59:59', strtotime($dateTo)).'"');
        $this->db->where('trans.status_id', 3);
        $this->db->where('trans.removed', 0);
        $this->db->where('items.removed', 0);
        $this->db->group_by('items.id');
        $query = $this->db->get();
        $querys = $query->result();

        $prices = 0;
        if($query->num_rows() > 0  && $querys[0]->quantity !== null)
        {
            foreach ($query->result() as $row) {
                $prices = (floatval($row->discount) / floatval($row->quantity)) + $row->price;
            }
        }

        return $prices;
    }

    public function get_sold_qty_via_date($product, $dateFrom, $dateTo)
    {
        $this->db->select('SUM(items.quantity) as quantity');
        $this->db->from('transactions_item as items');
        $this->db->join('transactions as trans', 'items.transaction_id = trans.id');
        $this->db->where('items.product_id', $product);
        $this->db->where('trans.created_at BETWEEN "'. date('Y-m-d '.'00:00:00', strtotime($dateFrom)). '" and "'. date('Y-m-d '.'23:59:59', strtotime($dateTo)).'"');
        $this->db->where('trans.status_id', 3);
        $this->db->where('trans.removed', 0);
        $this->db->where('items.removed', 0);
        $this->db->group_by('items.id');
        $query = $this->db->get();
        $querys = $query->result();

        $quantity = 0;
        if($query->num_rows() > 0  && $querys[0]->quantity !== null)
        {
            foreach ($query->result() as $row) {
                $quantity = $row->quantity;
            }
        }

        return $quantity;
    }

    public function get_sold_discount_via_date($product, $dateFrom, $dateTo)
    {
        $this->db->select('SUM(items.discount) as discount');
        $this->db->from('transactions_item as items');
        $this->db->join('transactions as trans', 'items.transaction_id = trans.id');
        $this->db->where('items.product_id', $product);
        $this->db->where('trans.created_at BETWEEN "'. date('Y-m-d '.'00:00:00', strtotime($dateFrom)). '" and "'. date('Y-m-d '.'23:59:59', strtotime($dateTo)).'"');
        $this->db->where('trans.status_id', 3);
        $this->db->where('trans.removed', 0);
        $this->db->where('items.removed', 0);
        $this->db->group_by('items.id');
        $query = $this->db->get();
        $querys = $query->result();

        $discount = 0;
        if($query->num_rows() > 0 && $querys[0]->discount !== null)
        {
            foreach ($query->result() as $row) {
                $discount = $row->discount;
            }
        }

        return $discount;
    }

    public function generate_all_transactions($dateFrom, $dateTo, $category, $order)
    {
        if($category == 1)
        {
            $this->db->select('*');
            $this->db->from('product as prod');
            $this->db->join('product_category as cat', 'prod.product_category_id = cat.product_category_id');
            $this->db->where('prod.removed', 0);
            $this->db->order_by('prod.product_name', $order);
            $query = $this->db->get();
            
            $arr = array();
            foreach ($query->result() as $row) {

                $posted_qty = $this->Product_Posting_Model->get_posted_qty_via_date($row->product_id, $dateFrom, $dateTo);
                $sold_qty   = $this->get_sold_qty_via_date($row->product_id, $dateFrom, $dateTo);
                // $sold_price = $this->get_sold_price_via_date($row->product_id, $dateFrom, $dateTo);
                $sold_discount = $this->get_sold_discount_via_date($row->product_id, $dateFrom, $dateTo);

                $arr[] = array(
                    'id'   => $row->product_id,
                    'code' => $row->product_code,
                    'name' => $row->product_name,
                    'desc' => $row->product_desc,
                    'category'   => $row->product_category_name,
                    'posted_qty' => $posted_qty,
                    'sold_qty'   => $sold_qty,    
                    // 'leftover'   => (floatval($posted_qty) - floatval($sold_qty)) + floatval($row->product_quantity),
                    'leftover'   => (floatval($posted_qty) - floatval($sold_qty)) ,
                    // 'unit_price' => $sold_price ? $sold_price : $row->product_price,
                    'unit_price' => $row->product_price,
                    'sold_discount' => $sold_discount                
                );
            }
            return $arr;
        }
        else if($category == 2)
        {
            $this->db->select('prod.product_id, prod.product_code, prod.product_name, prod.product_desc, cat.product_category_name, items.discount, items.quantity, prod.product_price, trans.created_at');
            $this->db->from('transactions_item as items');
            $this->db->join('transactions as trans', 'items.transaction_id = trans.id');
            $this->db->join('product as prod', 'items.product_id = prod.product_id');
            $this->db->join('product_category as cat', 'prod.product_category_id = cat.product_category_id');
            $this->db->where('trans.created_at BETWEEN "'. date('Y-m-d '.'00:00:00', strtotime($dateFrom)). '" and "'. date('Y-m-d '.'23:59:59', strtotime($dateTo)).'"');
            $this->db->where('trans.status_id', 3);
            $this->db->where('trans.removed', 0);
            $this->db->where('items.removed', 0);
            $this->db->order_by('trans.created_at', $order);
            $this->db->group_by('items.id');
            $query = $this->db->get();

            $arr = array();
            foreach ($query->result() as $row) {

                $arr[] = array(
                    'id'   => $row->product_id,
                    'code' => $row->product_code,
                    'name' => $row->product_name,
                    'desc' => $row->product_desc,
                    'datetime' => date('d-M-Y H:iA', strtotime($row->created_at)),
                    'category'   => $row->product_category_name,
                    'quantity'   => $row->quantity,    
                    'unit_price' => $row->product_price,
                    'discount'   => $row->discount                
                );
            }

            return $arr;
        }
        else {
            $this->db->select('*');
            $this->db->from('load_credit as credit');
            $this->db->where('credit.created_at BETWEEN "'. date('Y-m-d '.'00:00:00', strtotime($dateFrom)). '" and "'. date('Y-m-d '.'23:59:59', strtotime($dateTo)).'"');
            $this->db->where('credit.removed', 0);
            $this->db->order_by('credit.created_at', $order);
            $this->db->group_by('credit.id');
            $query = $this->db->get();
        
            $arr = array();
            foreach ($query->result() as $row) {

                $arr[] = array(
                    'id'   => $row->id,
                    'stud_no' => $row->stud_no,
                    'credit' => $row->load_credits,
                    'datetime' => date('d-M-Y H:i A', strtotime($row->created_at))             
                );
            }

            return $arr;
        }
    }

    public function generate_alls_transactions($dateFrom, $dateTo, $category, $order)
    {
        if($category == 1)
        {
            $this->db->select('*');
            $this->db->from('product as prod');
            $this->db->join('product_category as cat', 'prod.product_category_id = cat.product_category_id');
            $this->db->where('prod.removed', 0);
            $this->db->order_by('prod.product_name', $order);
            $query = $this->db->get();
            
            $amount = 0;
            foreach ($query->result() as $row) {

                $posted_qty = $this->Product_Posting_Model->get_posted_qty_via_date($row->product_id, $dateFrom, $dateTo);
                $sold_qty   = $this->get_sold_qty_via_date($row->product_id, $dateFrom, $dateTo);
                $sold_discount = $this->get_sold_discount_via_date($row->product_id, $dateFrom, $dateTo);

                $amount += (floatval($sold_qty) * floatval($row->product_price)) - floatval($sold_discount);
            }

            return $amount;
        }
        else if($category == 2)
        {
            $this->db->select('prod.product_id, prod.product_code, prod.product_name, prod.product_desc, cat.product_category_name, items.discount, items.quantity, prod.product_price');
            $this->db->from('transactions_item as items');
            $this->db->join('transactions as trans', 'items.transaction_id = trans.id');
            $this->db->join('product as prod', 'items.product_id = prod.product_id');
            $this->db->join('product_category as cat', 'prod.product_category_id = cat.product_category_id');
            $this->db->where('trans.created_at BETWEEN "'. date('Y-m-d '.'00:00:00', strtotime($dateFrom)). '" and "'. date('Y-m-d '.'23:59:59', strtotime($dateTo)).'"');
            $this->db->where('trans.status_id', 3);
            $this->db->where('trans.removed', 0);
            $this->db->where('items.removed', 0);
            $this->db->order_by('trans.created_at', $order);
            $this->db->group_by('items.id');
            $query = $this->db->get();

            $amount = 0;
            foreach ($query->result() as $row) {

                $amount += (floatval($row->quantity) * floatval($row->product_price)) - floatval($row->discount);
            }
            
            return $amount;
        }
        else if($category == 3)
        {
            $this->db->select('*');
            $this->db->from('load_credit as credit');
            $this->db->where('credit.created_at BETWEEN "'. date('Y-m-d '.'00:00:00', strtotime($dateFrom)). '" and "'. date('Y-m-d '.'23:59:59', strtotime($dateTo)).'"');
            $this->db->where('credit.removed', 0);
            $this->db->order_by('credit.created_at', $order);
            $this->db->group_by('credit.id');
            $query = $this->db->get();
            
            $amount = 0;
            foreach ($query->result() as $row) {

                $amount += floatval($row->load_credits);
            }
            
            return $amount;
        }
    }
}