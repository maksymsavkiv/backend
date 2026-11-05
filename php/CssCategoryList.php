<?php
require_once __DIR__. '/BaseList.php' ;
require_once __DIR__. '/CssCategory.php' ;

class CssCategoryList extends BaseList {
     public function add($dataArray) {
        $this->nextId++;
        $dataArray['id'] = $this->nextId;
        
        $category = new CssCategory($dataArray);
        array_push($this->dataArray, $category);
        return $category;
    }
    public function display() {
        echo "Список категорій:<br>";
        foreach ($this->dataArray as $cat) {
            $cat->displayInfo();
        }
    }
    public function exportAsArray() {
        $result = array(['name']);
        foreach ($this->dataArray as $item) {
            array_push($result, $item->getAsArray());
        }
        return $result;
    }
    public function exportAsXML(){
        $result='<?xml version="1.0" encoding="UTF-8"?><categories>';
        foreach($this->dataArray as $item){
            $result.=$item->getAsXML();
        }
        $result.='</categories>';
        return $result;
    }
}
?>