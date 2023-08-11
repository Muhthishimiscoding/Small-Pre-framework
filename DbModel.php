<?php
namespace MuhthishimisCoding\PreFramework;
abstract class DbModel extends Model
{
    public Database $db;
    abstract public function tableName():string;
    abstract public function col_values():array;
    public function __construct(){
        $this->db = Application::app()->db;
    }
    public function save(){
        $colvalues = $this->col_values();
       return $this->db->insert($this->tableName(),$colvalues);
    }
}
