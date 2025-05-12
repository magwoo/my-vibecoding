<?php

namespace App\Core;

abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function find($id)
    {
        return $this->db->find($this->table, $id);
    }
    
    public function findAll($conditions = [], $order = '', $limit = null, $offset = null)
    {
        return $this->db->findAll($this->table, $conditions, $order, $limit, $offset);
    }
    
    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }
    
    public function update($id, $data)
    {
        return $this->db->update($this->table, $id, $data);
    }
    
    public function delete($id)
    {
        return $this->db->delete($this->table, $id);
    }
    
    public function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }
} 