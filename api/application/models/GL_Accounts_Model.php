<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class GL_Accounts_Model extends CI_Model {

    private $gl_accountsTable = 'gl_accounts';
    private $gl_accountsColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->gl_accountsTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->gl_accountsColumn, $id);
        $this->db->update($this->gl_accountsTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->gl_accountsColumn, $id);
        $this->db->update($this->gl_accountsTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->gl_accountsColumn, 'DESC');
        $query = $this->db->get($this->gl_accountsTable, 1);

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
        $this->db->order_by($this->gl_accountsColumn, 'DESC');
        $query = $this->db->get($this->gl_accountsTable, 1);

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
    
    public function display_all_active_gl_accounts()
    {   
        $this->db->where('removed', 0);
        $query = $this->db->get($this->gl_accountsTable);

        $arr = array();

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] = $row->code.' ('.$row->name.')';
            }
        }
        
        return $arr;
    }

    public function display_all_active_gl_accounts2()
    {   
        $this->db->where('removed', 0);
        $this->db->where('id !=', 0);
        $query = $this->db->get($this->gl_accountsTable);

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] =  array(
                    'name' => $row->code.' ('.$row->name.')',
                    'value' => $row->id
                );
            }
        }
        
        return $arr;
    }

    public function get_gl_accounts_id_by_code($code)
    {
        $this->db->where('code', $code);
        $query = $this->db->get($this->gl_accountsTable);
    
        $gl_accounts_id = 0;

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $gl_accounts_id = $row->id;
            }
        }

        return $gl_accounts_id;
    }
}