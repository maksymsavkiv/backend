<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('../CssPropertyList.php');
header('Content-Type: text/xml; charset=utf-8');
$repo = new CssPropertyList();
if (($handle = fopen("../Base/CssProperty.csv", "r")) !== false) {
    $row=0;
    while (($fields = fgetcsv($handle, 1000, ",", "\"", "\\")) !== false) {
        if($row>0){
            $dataArray = array('name'=>$fields[0], 'category'=>$fields[1], 'description'=>$fields[2]);
            $repo->add($dataArray);   
        }
        $row++;
    }
    fclose($handle);
}
echo $repo->exportAsXML();