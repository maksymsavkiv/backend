<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('./CssCategoryList.php'); 
require_once('./CssExampleList.php'); 
require_once('./CssPropertyList.php');
$catList = new CssCategoryList();
if (($handle = fopen("./Base/CssCategories.csv", "r")) !== false) {
    $row=0;
    while (($fields = fgetcsv($handle, 1000, ",", "\"", "\\")) !== false) {
        if($row>0){
            $dataArray = array('name'=>$fields[0]);
            $catList->add($dataArray);   
        }
        $row++;
    }
    fclose($handle);
}
$exampleList = new CssExampleList();
if (($handle = fopen("./Base/CssExemple.csv", "r")) !== false) {
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
$repo = new CssPropertyList();
if (($handle = fopen("./Base/CssProperty.csv", "r")) !== false) {
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

?>