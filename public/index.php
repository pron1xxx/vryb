<?php 

session_start();

if (empty($_SESSION['csrf_token'])) {
    // Создаем криптографически безопасный токен
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require dirname(__DIR__) . '/config/const.php';


require_once __DIR__ . '/../vendor/autoload.php';

use mycls\Router;
$router = new Router;

require CORE . '/router.php';

