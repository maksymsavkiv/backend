<?php
abstract class BaseList {
    protected $dataArray;
    protected $nextId;

    public function __construct() {
        $this->dataArray = array();
        $this->nextId = 0;
    }

    public function findAll() {
        return $this->dataArray;
    }

    public function findById($id) {
        foreach ($this->dataArray as $prop) {
            if ($prop->getId() == $id) {
                return $prop;
            }
        }
        return null;
    }
    public function update($id, $dataArray) {
    $category = $this->findById($id);
    if ($category != null) {
        $category->setName($dataArray["name"]);
        return true;
    }
    return false;
}
    public function delete($id) {
        foreach ($this->dataArray as $index => $prop) {
            if ($prop->getId() == $id) {
                array_splice($this->dataArray, $index, 1);
                return true;
            }
        }
        return false;
    }
    public function exportAsJSON(){
        $result = array();
        foreach ($this->dataArray as $item) {
            array_push($result, $item->getAsAssocArray());
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
?>
