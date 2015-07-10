<?php

require_once "abstractdatabase_class.php";

class AbstractSelect{

    private $db;
    private $from = "";
    private $where = "";
    private $order = "";
    private $limit = "";

    public function __construct($db){
        $this->db = $db;
    }

    public function from($table_name, $fields){
        $table_name = $this->db->getTableName($table_name);
        $from = "";
        if($fields = "*") $from = "*";
        else{
            for($i = 0; $i < count($fields); $i++){
                if(($pos1 = strpos($fields[$i], "(")) !== false){
                    $pos2 = strpos($fields[$i], ")");
                    $from .= substr($fields[$i], 0, $pos1)."(`".substr($fields[$i], $pos1 + 1, $pos2 - $pos1 - 1)."`),";
                }
                else $from = "`".$fields[$i]."`,";
            }
            $from = substr($from, 0, -1);
        }
        $from .= " FROM `$table_name`";
        $this->from = $from;
        return $this;
    }

    public function where($where, $values = array(), $and = true){
        if($where){
            $where = $this->db->getQuery($where, $values);
            $this ->addWhere($where, $and);
        }

    }

    public function whereIn($field, $values, $and = true){
        $where = "`$field` IN (";
        foreach($values as $value){
            $where .= $this->db->getSQ().",";
            $where = substr($where, 0, -1);
            $where .= ")";
        }
        return $this->where($where, $values, $and);
    }

    public function addWhere($where, $and){
        if($this->where){
            if($and) $this->where .= " AND ";
            else $this->where .= " OR ";
            $this->where .= $where;
        }
        else $this->where = "WHERE $where";
    }

    public function order($field, $ask = true){
        if(is_array($field)){
            $this->order = "ORDER BY ";
            if(!is_array($ask)){
                $temp = array();
                for($i = 0; $i < count($field); $i++) $temp[] = $ask;
                $ask = $temp;
            }
            for($i = 0; $i < count($field);$i++){
                $this->order .= "`".$field[$i]."`";
                if(!$ask[$i]) $this->order .= " DESC,";
                else $this->order .= ",";
            }
            $this->order = substr($this->order, 0, -1);
        }
        else {
            $this->order = "ORDER BY `$field`";
            if(!$ask) $this->order .= " DESC";
        }
        return $this;
    }

    public function limit($count, $offset = 0){
        $count = (int) $count;
        $offset = (int) $offset;
        if($count < 0 || $offset < 0) return false;
        $this->limit = "LIMIT $offset, $count";
        return $this;
    }

    public function rand(){
        $this->order = "ORDER BY RAND()";
    }

    public function __toString(){
        if($this->from) $ret = "SELECT ".$this->from." ".$this->where." ".$this->order." ".$this->limit;
        else $ret = "";
        return $ret;
    }
}


?>