<?php
class CssExample {
    private $id;
    private $description;
    private $code;
    private $image;

    public function __construct($dataArray) {
        $this->id = (int)($dataArray["id"] ?? 0);
        $this->description = $dataArray["description"];
        $this->code = $dataArray["code"];
        $this->image = $dataArray["image"] ?? "";
    }

    public function getId() { return $this->id; }
    public function getDescription() { return $this->description; }
    public function getCode() { return $this->code; }
    public function getImage() { return $this->image; }
    public function getAsArray() {
        return array($this->code, $this->description);
    }
    public function getAsAssocArray() {
    return array('description' => $this->description, 'code' => $this->code, 'image' => $this->image);
    }
    public function getAsXML(){
        return '<example>
                    <description>'.$this->description.'</description>
                    <code>'.$this->code.'</code>
                </example>';
    }
}
?>