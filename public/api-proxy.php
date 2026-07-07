<?php
header('Content-Type: application/json');

// Включаем отладку
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Логируем все ошибки
$log_file = '/home/c/cu512526/vrybsite/public_html/api_debug.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - START\n", FILE_APPEND);

// Только POST запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Проверка, что файл загружен
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    file_put_contents($log_file, "File upload error: " . ($_FILES['file']['error'] ?? 'no file') . "\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode(['error' => 'File not uploaded', 'code' => $_FILES['file']['error'] ?? 'no file']);
    exit;
}

file_put_contents($log_file, "File: " . $_FILES['file']['name'] . ", size: " . $_FILES['file']['size'] . "\n", FILE_APPEND);

// URL FastAPI
$api_url = 'http://localhost:8000/api/parse-test/';

// Проверяем, доступен ли FastAPI
$test_conn = @fsockopen('localhost', 8000, $errno, $errstr, 5);
if (!$test_conn) {
    file_put_contents($log_file, "FastAPI not reachable: $errstr ($errno)\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => "FastAPI not reachable: $errstr"]);
    exit;
}
fclose($test_conn);
file_put_contents($log_file, "FastAPI is reachable\n", FILE_APPEND);

// Отправляем запрос к FastAPI
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name'])]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

rewind($verbose);
$verbose_log = stream_get_contents($verbose);
fclose($verbose);

file_put_contents($log_file, "cURL verbose: $verbose_log\n", FILE_APPEND);
file_put_contents($log_file, "HTTP Code: $http_code, Error: $error\n", FILE_APPEND);
file_put_contents($log_file, "Response: $response\n", FILE_APPEND);

curl_close($ch);

if ($http_code !== 200) {
    http_response_code($http_code);
    echo json_encode(['error' => "API error: $error", 'code' => $http_code, 'response' => $response]);
    exit;
}

echo $response;