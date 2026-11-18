<?php
require_once __DIR__.'/BaseList.php' ;
require_once __DIR__.'/CssProperty.php';

class CssPropertyList extends BaseList {

    public function add($dataArray) {
        $id = (int)($dataArray['id'] ?? 0);
        if ($id > 0) {
            $this->nextId = max($this->nextId, $id);
        } else {
            $dataArray['id'] = ++$this->nextId;
        }
        $property = new CssProperty($dataArray);
        array_push($this->dataArray, $property);
        return $property;
    }
    public function display() {
        echo "Список властивостей:<br>";
        foreach ($this->dataArray as $prop) {
            $prop->printInfo(); 
            echo "<br>";
        }
    }
    public function exportAsArray() {
        $result = array(['name'],['category'],['description']);
        foreach ($this->dataArray as $item) {
            array_push($result, $item->getAsArray());
        }
        return $result;
    }
    public function exportAsXML(){
        $result='<?xml version="1.0" encoding="UTF-8"?><propertys>';
        foreach($this->dataArray as $item){
            $result.=$item->getAsXML();
        }
        $result.='</propertys>';
        return $result;
    }
    public function getAllFromDataBase(PDO $conn) {
        $this->dataArray = [];
        $this->nextId = 0;
        $statement = $conn->prepare(
            "SELECT id, name, categories_id, description FROM properties ORDER BY id"
        );
        $statement->execute();
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $row['category'] = $row['categories_id'];
            $this->add($row);
        }
    }
}
?>