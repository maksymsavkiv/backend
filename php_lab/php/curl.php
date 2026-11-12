<?php
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/php/api/categories-json.php");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);
    curl_close($ch);
    $data=json_decode($output, true);
    foreach($data as $item){
        echo $item['id']. '.'.$item['name']. '</br>'; 
    } 
?>
