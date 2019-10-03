<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transactions_Model extends CI_Model {

    private $transactionsTable = 'transactions';
    private $transactionsColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->transactionsTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->transactionsColumn, $id);
        $this->db->update($this->transactionsTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->transactionsColumn, $id);
        $this->db->update($this->transactionsTable, $data);
        return true;
    }

    public function find($trans_no)
    { 
        $this->db->where('trans_no', $trans_no);
        return $this->db->get($this->transactionsTable)->row();
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->transactionsColumn, 'DESC');
        $query = $this->db->get($this->transactionsTable, 1);

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
        $this->db->order_by($this->transactionsColumn, 'DESC');
        $query = $this->db->get($this->transactionsTable, 1);

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

    public function generate()
    {   
        $year = date('Y');
        $query = $this->db->get($this->transactionsTable);
        $count = $query->num_rows();
        $trans_no = '';

        if(($count + 1) <= 9)
        {
            $trans_no = $year."000000". ($count + 1);
        }
        else if(($count + 1) <= 99) 
        {
            $trans_no = $year."00000". ($count + 1);
        }
        else if(($count + 1) <= 999) 
        {
            $trans_no = $year."0000". ($count + 1);
        }
        else if(($count + 1) <= 9999) 
        {
            $trans_no = $year."000". ($count + 1);
        }
        else if(($count + 1) <= 99999) 
        {
            $trans_no = $year."00". ($count + 1);
        }
        else if(($count + 1) <= 999999) 
        {
            $trans_no = $year."0". ($count + 1);
        }
        else
        {
            $trans_no = $year."". ($count + 1);
        }

        return $trans_no;
    }

    public function get_all_pending_notifications($wildcard = '', $limit, $start_from)
    {   
        $today = date('Y-m-d');
        $this->db->select('trans.trans_no, trans.stud_no, stat.name as status');
        $this->db->from('transactions as trans');
        $this->db->join('status as stat', 'trans.status_id = stat.id');
        $this->db->where('trans.removed', 0);
        $this->db->where('trans.status_id', 1);
        if($wildcard == '') {
            $this->db->where('trans.created_at LIKE', '%' . $today . '%');
        }
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('trans.trans_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('trans.stud_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('stat.name LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("trans.id", "desc");
        $this->db->group_by('trans.id');
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result(); 
    }

    public function get_alls_pending_notifications($wildcard = '')
    {   
        $today = date('Y-m-d');
        $this->db->select('*');
        $this->db->from('transactions as trans');
        $this->db->join('status as stat', 'trans.status_id = stat.id');
        $this->db->where('trans.removed', 0);
        $this->db->where('trans.status_id', 1);
        if($wildcard == '') {
            $this->db->where('trans.created_at LIKE', '%' . $today . '%');
        }
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('trans.trans_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('trans.stud_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('stat.name LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("trans.id", "desc");
        $this->db->group_by('trans.id');
        $query = $this->db->get();
        return $query->result();  
    }

    public function get_all_reserved_notifications($wildcard = '', $limit, $start_from)
    {   
        $today = date('Y-m-d');
        $this->db->select('trans.trans_no, trans.stud_no, stat.name as status');
        $this->db->from('transactions as trans');
        $this->db->join('status as stat', 'trans.status_id = stat.id');
        $this->db->where('trans.removed', 0);
        $this->db->where('trans.status_id', 2);
        if($wildcard == '') {
            $this->db->where('trans.created_at LIKE', '%' . $today . '%');
        }
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('trans.trans_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('trans.stud_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('stat.name LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("trans.id", "desc");
        $this->db->group_by('trans.id');
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result(); 
    }

    public function get_alls_reserved_notifications($wildcard = '')
    {   
        $today = date('Y-m-d');
        $this->db->select('*');
        $this->db->from('transactions as trans');
        $this->db->join('status as stat', 'trans.status_id = stat.id');
        $this->db->where('trans.removed', 0);
        $this->db->where('trans.status_id', 2);
        if($wildcard == '') {
            $this->db->where('trans.created_at LIKE', '%' . $today . '%');
        }
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('trans.trans_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('trans.stud_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('stat.name LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("trans.id", "desc");
        $this->db->group_by('trans.id');
        $query = $this->db->get();
        return $query->result();  
    }

    public function get_all_served_notifications($wildcard = '', $limit, $start_from)
    {   
        $today = date('Y-m-d');
        $this->db->select('trans.trans_no, trans.stud_no, stat.name as status');
        $this->db->from('transactions as trans');
        $this->db->join('status as stat', 'trans.status_id = stat.id');
        $this->db->where('trans.removed', 0);
        $this->db->where('trans.status_id', 3);
        if($wildcard == '') {
            $this->db->where('trans.created_at LIKE', '%' . $today . '%');
        }
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('trans.trans_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('trans.stud_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('stat.name LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("trans.id", "desc");
        $this->db->group_by('trans.id');
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result(); 
    }

    public function get_alls_served_notifications($wildcard = '')
    {   
        $today = date('Y-m-d');
        $this->db->select('*');
        $this->db->from('transactions as trans');
        $this->db->join('status as stat', 'trans.status_id = stat.id');
        $this->db->where('trans.removed', 0);
        $this->db->where('trans.status_id', 3);
        if($wildcard == '') {
            $this->db->where('trans.created_at LIKE', '%' . $today . '%');
        }
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('trans.trans_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('trans.stud_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('stat.name LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("trans.id", "desc");
        $this->db->group_by('trans.id');
        $query = $this->db->get();
        return $query->result();  
    }

    public function get_all_cancelled_notifications($wildcard = '', $limit, $start_from)
    {   
        $today = date('Y-m-d');
        $this->db->select('trans.trans_no, trans.stud_no, stat.name as status');
        $this->db->from('transactions as trans');
        $this->db->join('status as stat', 'trans.status_id = stat.id');
        $this->db->where('trans.removed', 0);
        $this->db->where('trans.status_id', 4);
        if($wildcard == '') {
            $this->db->where('trans.created_at LIKE', '%' . $today . '%');
        }
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('trans.trans_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('trans.stud_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('stat.name LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("trans.id", "desc");
        $this->db->group_by('trans.id');
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result(); 
    }

    public function get_alls_cancelled_notifications($wildcard = '')
    {   
        $today = date('Y-m-d');
        $this->db->select('*');
        $this->db->from('transactions as trans');
        $this->db->join('status as stat', 'trans.status_id = stat.id');
        $this->db->where('trans.removed', 0);
        $this->db->where('trans.status_id', 4);
        if($wildcard == '') {
            $this->db->where('trans.created_at LIKE', '%' . $today . '%');
        }
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('trans.trans_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('trans.stud_no LIKE', '%' . $wildcard . '%');
            $this->db->or_where('stat.name LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("trans.id", "desc");
        $this->db->group_by('trans.id');
        $query = $this->db->get();
        return $query->result();  
    }

    public function fetch_transactions($transaction)
    {
        $this->db->where('trans_no', $transaction);
        return $this->db->get($this->transactionsTable)->row();
    }

    public function check_if_has_transaction_today($barcode, $today)
    {
        $this->db->where('created_at LIKE', '%' . $today . '%');
        $this->db->where('barcode', $barcode);
        $query = $this->db->get('transactions');
        return $query->num_rows();
    }
}