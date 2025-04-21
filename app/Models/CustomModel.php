<?php namespace App\Models;

use Codeigniter\Database\ConnectionInterface;

class CustomModel {

    protected $db;

    public function __construct(ConnectionInterface &$db) {
        $this->db =& $db;
    }
    function getCompletedTasks() {
        $builder = $this->db->table('list_item')
                            ->join('checklist', 'list_item.list_id = checklist.list_id')
                            ->where(['checked' => 1])
                            ->where('list_item.deleted_at IS NULL')
                            ->orderBy('list_item.updated_at', 'ASC');
    
        $list_items = $builder->get()->getResultArray();

        return $list_items; 
    }
    function getUncompletedTasks() {
        $builder = $this->db->table('list_item')
                            ->join('checklist', 'list_item.list_id = checklist.list_id')
                            ->where(['checked' => 0])
                            ->where('list_item.deleted_at IS NULL')
                            ->orderBy('list_item.updated_at', 'ASC');
        $list_items = $builder->get()->getResultArray();

        return $list_items; 
    }
}