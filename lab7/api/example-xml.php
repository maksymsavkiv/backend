<?php
require_once __DIR__ . '/../CsvRepository.php';
$items = (new CsvRepository('examples'))->all();
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?><examples>';
foreach ($items as $item) {
 echo '<example>';
 foreach ($item->getAsAssocArray() as $key => $value) {
 $tag = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$key);
 echo '<' . $tag . '>' . htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</' . $tag . '>';
 }
 echo '</example>';
}
echo '</examples>';
