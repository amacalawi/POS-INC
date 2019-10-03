<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Load_Model extends CI_Model {

    private $Table = 'members';
    private $Column = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->Table, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->Column, $id);
        $this->db->update($this->Table, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->Column, $id);
        $this->db->update($this->Table, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->Column, 'DESC');
        $query = $this->db->get($this->Table, 1);

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
        $this->db->order_by($this->Column, 'DESC');
        $query = $this->db->get($this->Table, 1);

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
    
    public function find($id)
    {
        $this->db->select('usez.id, usez.secret_id, usez.name, usez.password, usez.profile_id, usez_priv.privilege_id as priv_id');
        $this->db->from(' as usez');
        $this->db->join('secret as sec', 'usez.secret_id = sec.id');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('_privilege as usez_priv', 'usez.id = usez_priv._id');
        $this->db->join('privilege as priv', 'usez_priv.privilege_id = priv.id');
        $this->db->where('usez_priv.removed', 0);
        $this->db->where('usez.id', $id);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr = array(
                    'id' => $row->id,
                    'name' => $row->name,
                    'secret_id' => $row->secret_id,
                    'profile_id' => $row->profile_id,
                    'privilege_id' => $row->priv_id
                );
            }
        }

        return $arr;
    }

    public function get_all_member_list($wildcard = '', $limit, $start_from)
    {
        $this->db->select('usez.id, usez.name, prof.firstname, prof.middlename, prof.lastname, prof.gender, priv.name');
        $this->db->from(' as usez');
        $this->db->join('secret as sec', 'usez.secret_id = sec.id');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('_privilege as usez_priv', 'usez.id = usez_priv._id');
        $this->db->join('privilege as priv', 'usez_priv.privilege_id = priv.id');
        $this->db->where('usez.removed', 0);
        $this->db->where('usez_priv.removed', 0);
        $this->db->where('usez.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('usez.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('usez.name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('sec.desc LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prof.firstname LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prof.middlename LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prof.lastname LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("usez.id", "asc");
        $this->db->group_by('usez.id');
        $query = $this->db->limit( $limit, $start_from )->get();
        return $query->result(); 
    }

    public function get_all_member_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from(' as usez');
        $this->db->join('secret as sec', 'usez.secret_id = sec.id');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('_privilege as usez_priv', 'usez.id = usez_priv._id');
        $this->db->join('privilege as priv', 'usez_priv.privilege_id = priv.id');
        $this->db->where('usez.removed', 0);
        $this->db->where('usez_priv.removed', 0);
        $this->db->where('usez.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('usez.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('usez.name LIKE', '%' . $wildcard . '%');
            $this->db->or_where('sec.desc LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prof.firstname LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prof.middlename LIKE', '%' . $wildcard . '%');
            $this->db->or_where('prof.lastname LIKE', '%' . $wildcard . '%');
            $this->db->group_end();
        }
        $this->db->order_by("usez.id", "asc");
        $this->db->group_by('usez.id');
        $query = $this->db->get();
        return $query->num_rows(); 
    }
}