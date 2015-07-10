<?php

abstract class AbstractObjectDB{

    const TYPE_TIMESTAMP = 1;
    const TYPE_IP = 2;

    private static $types = array(self::TYPE_TIMESTAMP, self::TYPE_IP);
    protected static $db = null;

    private $format_date = "";

    private $id = null;
    private $properties = array();

    protected $table_name = "";

    public function __construct($table_name, $format_date){
        $this->table_name = $table_name;
        $this->format_date = $format_date;
    }

    public static function setDB($db){
        self::$db = $db;
    }

    public function load($id){
        $id = (int) $id;
        if($id < 0) return false;
        $select = new Select();
        $select = $select->from($this->table_name, $this->getSelectFields())
            ->where ("`id` = ".self::$db->getSQ(), array($id));
        $row = self::$db->selectRow($select);
        if(!$row) return false;
        if($this->init($row)) return $this->postLoad();
    }
    
    public function init($row){
        foreach ($this->properties as $key=>$value) {
            $val = $row[$key];
            switch ($value["type"]){
                case self::TYPE_TIMESTAMP:
                    if(!is_null($val)) $val = strtotime($this->format_date, $val);
                    break;
                case self::TYPE_IP:
                    if(!is_null($val)) $val = long2ip($val);
                    break;
            }
            $this->properties[$key]["value"] = $val;
        }
        $this->id = $row["id"];
        return $this->postInit();

    }

    public function isSaved(){
        $this->getID() > 0;
    }

    public function getID(){
        return (int) $this->id;
    }

    public function save(){
        $update = $this->isSaved();
        if($update) $commit = $this->preUpdate();
        else $commit = $this->preInsert();
        if(!$commit) return false;
        $row = array();
        foreach($this->properties as $key => $value){
            switch($value["type"]){
                case self::TYPE_TIMESTAMP:
                    if(!is_null($value["value"]))
                        $value["value"] = strtotime($value["value"]);
                    break;
                case self::TYPE_IP:
                    if(!is_null($value["value"])) $value["value"] = ip2long($value["value"]);
                break;
            }
            $row[$key] = $value["value"];
        }
        if(count($row)>0){
            if($update){
                $success = self::$db->update($this->table_name, $row, "`id` = ".self::getSQ(), array($this->getID()));
                if(!$success) throw new Exception();
            }
            else{
                $this->id = self::$db->insert($this->table_name, $row);
                if(!$this->id) throw new Exception();
            }
        }
        if($update) return $this->postUpdate();
        return $this->postInsert();
    }

    public function delete(){
        if(!$this->isSaved()) return false;
        if(!$this->preDelete()) return false;
        $success = self::$db->delete($this->table_name, "`id` = ".self::getSQ(), array($this->getID()));
        if(!$success) throw new Exception();
        $this->id = null;
        return $this->postDelete();
    }

    public function __set($name, $value){
        if(array_key_exists($name, $this->properties)){
            $this->properties[$name]["value"] = $value;
            return true;
        }
        else $this->$name =$value;
    }

    public function __get($name){
        if($name == "id") return $this->getID();
        return array_key_exists($name, $this->properties)? $this->properties[$name]["value"]: null;
    }

    public static function buildMultiple($class, $data){
        $ret = array();
        if(!class_exists($class)) throw new Exception();

        $test_obj = new $class();
        if(!$test_obj instanceof AbstractObjectDB) throw new Exception();
        foreach($data as $row){
            $obj = new $class();
            $obj->init($row);
            $ret[$obj->getID()] = $obj;
        }
        return $ret;
    }

    public static function getAll($count = false, $offset = false){
        $class = get_called_class();
        return self::getAllWithOrder($class::$table, $class, "id", true, $count , $offset);
    }

    public static function getCount(){
        $class = get_called_class();
        return self::getCountOnWhere($class::$table, false, false);
    }

    public static function getAllOnField($table_name, $class, $field, $value, $order = false, $ask = true, $count = false, $offset = false){
        return self::getAllOnWhere($table_name, $class, false, false, $order, $ask, $count, $offset);
    }

    protected static function getCountOnWhere($table_name, $where = false, $values = false){
        $select = new Select();
        $select->from($table_name, array("COUNT(id)"));
        if($where) $select->where($where, $values);
        return self::$db->selectCell($select);
    }

    protected static function getAllWithOrder($table_name, $class, $order = false, $ask = true, $count = false, $offset = false){
        return self::getAllOnWhere($table_name, $class, false, false, $order, $ask, $count, $offset);
    }

    protected static function getAllOnWhere($table_name, $class, $where = false, $values = false, $order = false, $ask = true, $count = false, $offset = false ){
        $select = new Select();
        $select -> from($table_name, "*");
        if($where) $select->where($where, $values);
        if($order) $select->order($order, $ask);
        else $select->order("id");
        if($count) $select->limit($count, $offset);
        $data = self::$db->select($select);
        return ObjectDB::buildMultiple($class, $data);
    }

    protected static function addSubObject($data, $class, $field_out, $field_in){
        $ids = array();
        foreach($data as $value){
            
        }
    }


}

?>