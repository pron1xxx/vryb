<?php

$test_id = (int) $_GET['id'];
$db = createDbConnection();

// Проверяем существование теста
$test = $db->query(
    'SELECT id FROM tests WHERE id = :id',
    [':id' => $test_id]
)->fetch();

if (!$test) {
    http_response_code(404);
    exit('Test not found');
}

$results = $db->query('SELECT * FROM usersscores WHERE test_id = :test_id AND user_id = :user_id ORDER BY completed_at DESC', [':user_id'=>$_SESSION['user']['id'], ':test_id'=>$test_id])->fetchALl();

$lesson_id = $db->query('SELECT lesson_id FROM tests WHERE id = :id', [":id"=>$test_id])->fetch()['lesson_id'];

$title = 'Результаты теста';
require VIEWS . "/lesson/testResult.tpl.php";