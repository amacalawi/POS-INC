<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends CI_Model {

    private $userTable = 'user';
    private $userColumn = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->userTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->userColumn, $id);
        $this->db->update($this->userTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->userColumn, $id);
        $this->db->update($this->userTable, $data);
        return true;
    }

    public function check_last_inserted()
    {          
        $this->db->order_by($this->userColumn, 'DESC');
        $query = $this->db->get($this->userTable, 1);

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
        $this->db->order_by($this->userColumn, 'DESC');
        $query = $this->db->get($this->userTable, 1);

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
        $this->db->select('usez.id, usez.secret_id, usez.username, usez.password, usez.profile_id, usez_role.role_id as role_id');
        $this->db->from('user as usez');
        $this->db->join('secret as sec', 'usez.secret_id = sec.id');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('user_roles as usez_role', 'usez.id = usez_role.user_id');
        $this->db->join('roles as role', 'usez_role.role_id = role.id');
        $this->db->where('usez_role.removed', 0);
        $this->db->where('usez.id', $id);
        $query = $this->db->get();

        $arr = array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) {
                $arr = array(
                    'id' => $row->id,
                    'username' => $row->username,
                    'secret_id' => $row->secret_id,
                    'profile_id' => $row->profile_id,
                    'role_id' => $row->role_id
                );
            }
        }

        return $arr;
    }

    public function get_all_users_list($wildcard = '', $limit, $start_from)
    {
        $this->db->select('usez.id, usez.username, prof.firstname, prof.middlename, prof.lastname, prof.gender, role.name');
        $this->db->from('user as usez');
        $this->db->join('secret as sec', 'usez.secret_id = sec.id');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('user_roles as usez_role', 'usez.id = usez_role.user_id');
        $this->db->join('roles as role', 'usez_role.role_id = role.id');
        $this->db->where('usez.removed', 0);
        $this->db->where('usez_role.removed', 0);
        $this->db->where('usez.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('usez.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('usez.username LIKE', '%' . $wildcard . '%');
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

    public function get_all_users_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from('user as usez');
        $this->db->join('secret as sec', 'usez.secret_id = sec.id');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('user_roles as usez_role', 'usez.id = usez_role.user_id');
        $this->db->join('roles as role', 'usez_role.role_id = role.id');
        $this->db->where('usez.removed', 0);
        $this->db->where('usez_role.removed', 0);
        $this->db->where('usez.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('usez.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('usez.username LIKE', '%' . $wildcard . '%');
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

    public function get_all_archived_users_list($wildcard = '', $limit, $start_from)
    {
        $this->db->select('usez.id, usez.username, prof.firstname, prof.middlename, prof.lastname, prof.gender, role.name');
        $this->db->from('user as usez');
        $this->db->join('secret as sec', 'usez.secret_id = sec.id');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('user_roles as usez_role', 'usez.id = usez_role.user_id');
        $this->db->join('roles as role', 'usez_role.role_id = role.id');
        $this->db->where('usez.removed', 1);
        $this->db->where('usez_role.removed', 1);
        $this->db->where('usez.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('usez.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('usez.username LIKE', '%' . $wildcard . '%');
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

    public function get_all_archived_users_pagination($wildcard = '')
    {
        $this->db->select('*');
        $this->db->from('user as usez');
        $this->db->join('secret as sec', 'usez.secret_id = sec.id');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('user_roles as usez_role', 'usez.id = usez_role.user_id');
        $this->db->join('roles as role', 'usez_role.role_id = role.id');
        $this->db->where('usez.removed', 1);
        $this->db->where('usez_role.removed', 1);
        $this->db->where('usez.id !=', 0);
        if(!empty($wildcard)){
            $this->db->group_start();
            $this->db->or_where('usez.id LIKE', '%' . $wildcard . '%');
            $this->db->or_where('usez.username LIKE', '%' . $wildcard . '%');
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

    public function validate($username, $password)
    {
        $this->db->select('usez.id, usez.username, usez.password, prof.firstname, prof.middlename, prof.lastname, prof.email, role.name as roles');
        $this->db->from('user as usez');
        $this->db->join('profile as prof', 'usez.profile_id = prof.id');
        $this->db->join('user_roles as usez_role', 'usez.id = usez_role.user_id');
        $this->db->join('roles as role', 'usez_role.role_id = role.id');
        $this->db->where('usez.removed', 0);
        $this->db->where('usez_role.removed', 0);
        $this->db->where('usez.username', $username);
        $query = $this->db->get();

        if($query->num_rows() == 1) {
            foreach ($query->result() as $row) {
                if( password_verify($password, $row->password) ) {
                    $data = array(
                        'user_id'    => $row->id,
                        'firstname'  => $row->firstname,
                        'middlename' => $row->middlename,
                        'lastname'   => $row->lastname,
                        'username'   => $row->username,
                        'email'      => $row->email,
                        'role'       => $row->roles,
                        'logged_in'  => true
                    );
                    $this->session->set_userdata($data);
                    return true;
                }
            }
        }

        return false;
    }
}