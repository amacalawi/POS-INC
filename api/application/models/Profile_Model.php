<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Profile_Model extends CI_Model {

    private $profileTable = 'profile';
    private $profileColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->profileTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->profileColumn, $id);
        $this->db->update($this->profileTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->profileColumn, $id);
        $this->db->update($this->profileTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->profileColumn, 'DESC');
        $query = $this->db->get($this->profileTable, 1);

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
        $this->db->order_by($this->profileColumn, 'DESC');
        $query = $this->db->get($this->profileTable, 1);

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
    
    public function get_all_profile_for_user()
    {
        $this->db->select('*');
        $this->db->from('profile as prof');
        $this->db->where('prof.id NOT IN(select profile_id from user)');
        $this->db->where('prof.removed', 0);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] = array(
                    'name' => $row->firstname.' '.$row->middlename.' '.$row->lastname,
                    'value' => $row->id
                );
            }
        }

        return $arr;
    }

    public function get_profile_by_id($id)
    {
        $this->db->where($this->profileColumn, $id);
        $query = $this->db->get($this->profileTable);
        
        $profile = '';
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $profile = $row->firstname.' '.$row->middlename.' '.$row->lastname;
            }
        }
        return $profile;
    }

    public function reload_lists($id)
    {
        $this->db->select('*');
        $this->db->from($this->profileTable.' as prof');
        $this->db->where('prof.id NOT IN(select id from user where id != ' . $id . ')');
        $this->db->where('prof.removed', 0);
        $query = $this->db->get();

        $arr = array();
        
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr[] = array(
                    'name' => $row->firstname.' '.$row->middlename.' '.$row->lastname,
                    'value' => $row->id
                );
            }
        }

        return $arr;
    }
}