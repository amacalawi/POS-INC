<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Secret_Model extends CI_Model {

    private $secretTable = 'secret';
    private $secretColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->secretTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->secretColumn, $id);
        $this->db->update($this->secretTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->secretColumn, $id);
        $this->db->update($this->secretTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->secretColumn, 'DESC');
        $query = $this->db->get($this->secretTable, 1);

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
        $this->db->order_by($this->secretColumn, 'DESC');
        $query = $this->db->get($this->secretTable, 1);

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

    public function get_all_secret_question()
    {
        $this->db->select('*');
        $this->db->from('secret');
        $this->db->where('removed', 0);
        $this->db->where('id !=', 0);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] = array(
                    'name' => $row->name,
                    'value' => $row->id
                );
            }
        }

        return $arr;
    }

    public function get_secret_question_by_id($id)
    {
        $this->db->where($this->secretColumn, $id);
        return $this->db->get($this->secretTable)->row()->name;
    }
}