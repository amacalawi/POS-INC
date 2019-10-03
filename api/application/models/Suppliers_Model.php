<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Suppliers_Model extends CI_Model {

    private $suppliersTable = 'suppliers';
    private $suppliersColumn = 'suppliers_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->suppliersTable, $data);
        return $this->db->insert_id();
    }

    public function modify($data, $id)
    {   
        $this->db->where($this->suppliersColumn, $id);
        $this->db->update($this->suppliersTable, $data);
        return true;
    }

    public function delete($data, $id)
    { 
        $this->db->where($this->suppliersColumn, $id);
        $this->db->update($this->suppliersTable, $data);
        return true;
    }
}