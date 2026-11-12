<?php
require_once __DIR__ . '/../CsvRepository.php';
$items = (new CsvRepository('categories'))->all();
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?><categories>';
foreach ($items as $item) {
 echo '<category>';
 foreach ($item->getAsAssocArray() as $key => $value) {
 $tag = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$key);
 echo '<' . $tag . '>' . htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</' . $tag . '>';
 }
 echo '</category>';
}
echo '</categories>';
