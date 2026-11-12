<?php
class CssProperty {
    private $id;
    private $name;
    private $category;
    private $description;
    private $examples = [];

    public function __construct($dataArray) {
        $this->id = $dataArray["id"];
        $this->name = $dataArray["name"];
        $this->category = $dataArray["category"];
        $this->description = $dataArray["description"];
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getCategory() { return $this->category; }
    public function getDescription() { return $this->description; }
    public function getExamples() { return $this->examples; }

    public function setName($name) { $this->name = $name; }
    public function setCategory($category) { $this->category = $category; }
    public function setDescription($description) { $this->description = $description; }

    public function addExample($example) {
        $this->examples[] = $example;
    }
    public function getAsArray() {
        return array($this->name, $this->category, $this->description);
    }
    public function printInfo() {
        echo "{$this->name}, {$this->category}, \"{$this->description}\"";
    }
    public function getAsAssocArray() {
    return array('id' => $this->id, 'name' => $this->name, 'categoryId' => $this->category, 'description' => $this->description);
    }
    public function getAsXML(){
        $examplesXml = '';
        foreach ($this->examples as $ex) {
        $examplesXml .= '<example>' . $ex->getCode() . '</example>'; 
    }
        return '<property>
                    <id>'.$this->id.'</id>
                    <name>'.$this->name.'</name>
                    <category>'.$this->category.'</category>
                    <description>'.$this->description.'</description>
                    <examples>'.$examplesXml.'</examples>
                </property>';
    }
}
?>