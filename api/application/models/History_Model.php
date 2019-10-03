<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class history_Model extends CI_Model {

    private $historyTable = 'history';
    private $historyColumn = 'history_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->historyTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->historyColumn, $id);
        $this->db->update($this->historyTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->historyColumn, $id);
        $this->db->update($this->historyTable, $data);
        return true;
    }

    public function check_last_modified($data)
    {    
        $this->db->where('history_table', $data);        
        $this->db->order_by($this->historyColumn, 'DESC');
        $query = $this->db->get($this->historyTable, 1);

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                if(date($row->history_timestamp, time() + 5) > date('Y-m-d H:i:s'))
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