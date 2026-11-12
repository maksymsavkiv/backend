<?php
require_once __DIR__ . '/../CsvRepository.php';
$items = (new CsvRepository('examples'))->all();
header('Content-Type: application/json; charset=utf-8');
echo json_encode(array_map(fn($item) => $item->getAsAssocArray(), $items), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
