<?php
require_once __DIR__. '/BaseList.php';
require_once __DIR__.'/CssExample.php';

class CssExampleList extends BaseList {
     public function add($dataArray) {
        $id = (int)($dataArray['id'] ?? 0);
        if ($id > 0) {
            $this->nextId = max($this->nextId, $id);
        } else {
            $dataArray['id'] = ++$this->nextId;
        }
        $newObj = new CssExample($dataArray);
        array_push($this->dataArray, $newObj);
        return $newObj;
    }
    public function display() {
        echo "Список прикладів:<br>";
        foreach ($this->dataArray as $ex) {
            echo $ex->getDescription() . " -> <code>" . $ex->getCode() . "</code><br>";
        }
    }
    public function exportAsArray() {
        $result = array(['description'],['code']);
        foreach ($this->dataArray as $item) {
            array_push($result, $item->getAsArray());
        }
        return $result;
    }
    
    public function exportAsXML(){
        $result='<?xml version="1.0" encoding="UTF-8"?><examples>';
        foreach($this->dataArray as $item){
            $result.=$item->getAsXML();
        }
        $result.='</examples>';
        return $result;
    }
    public function getAllFromDataBase(PDO $conn) {
        $this->dataArray = [];
        $this->nextId = 0;
        $statement = $conn->prepare(
            "SELECT id, description, code, image FROM examples ORDER BY id"
        );
        $statement->execute();
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $this->add($row);
        }
    }
}
?>