<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('../CssExampleList.php');
header('Content-Type: text/xml; charset=utf-8');
$exampleList = new CssExampleList();
if (($handle = fopen("../Base/CssExemple.csv", "r")) !== false) {
    $row=0;
    while (($fields = fgetcsv($handle, 1000, ",", "\"", "\\")) !== false) {
        if($row>0){
            $dataArray = array('description'=>$fields[0], 'code'=>$fields[1]);
            $exampleList->add($dataArray);   
        }
        $row++;
    }
    fclose($handle);
}
echo $exampleList->exportAsXML();