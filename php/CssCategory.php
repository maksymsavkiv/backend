<?php
class CssCategory {
    private $id;
    private $name;

    public function __construct($dataArray) {
        $this->id = $dataArray['id'];
        $this->name = $dataArray['name'];
    }
    
    public function displayInfo() {
        echo $this->id . ". " . $this->name . "<br>";
    }
    public function setName($name) { $this->name = $name; }
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getAsArray() {
        return array($this->id => $this->name);
    }
    public function getAsAssocArray(){
        return array('id' => $this->id, 'name' => $this->name);
    }
    public function getAsXML(){
        return '<category>
                    <id>'.$this->id.'</id>
                    <name>'.$this->name.'</name>
                </category>';
    }
}
?>