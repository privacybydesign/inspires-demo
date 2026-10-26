<?php
require_once __DIR__ . '/status.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');   // the slide widget polls this

$status = demo_status();
http_response_code($status['healthy'] ? 200 : 503);
echo json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
