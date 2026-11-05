<?php
class CssExample {
    private $description;
    private $code;

    public function __construct($dataArray) {
        $this->description = $dataArray["description"];
        $this->code = $dataArray["code"];
    }

    public function getDescription() { return $this->description; }
    public function getCode() { return $this->code; }
    public function getAsArray() {
        return array($this->code, $this->description);
    }
    public function getAsAssocArray() {
    return array('description' => $this->description, 'code' => $this->code);
    }
    public function getAsXML(){
        return '<example>
                    <description>'.$this->description.'</description>
                    <code>'.$this->code.'</code>
                </example>';
    }
}
?>