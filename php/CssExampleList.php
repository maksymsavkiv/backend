<?php
require_once __DIR__. '/BaseList.php';
require_once __DIR__.'/CssExample.php';

class CssExampleList extends BaseList {
     public function add($dataArray) {
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
}
?>