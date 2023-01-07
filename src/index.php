<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/SatisBuilder.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-Type: text/html; charset=utf-8');
    $name = $_GET['name'] ?? null;
    $result = SatisBuilder::build($name, true);
    echo '<br>' . ($result ? 'Rebuild successful!' : 'Rebuild failed.');
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input') ?? '[]', true);
    $name = $data['repository']['full_name'] ?? null;
    if (empty($name)) {
        header('HTTP/1.1 400');
        exit(1);
    }
    header('Content-Type: application/json; charset=utf-8');
    $result = SatisBuilder::build($name);
    if (!$result) {
        header('HTTP/1.1 500');
        exit(1);
    }
    echo "true";
} else {
    header('HTTP/1.1 405');
    exit(1);
}

exit(0);