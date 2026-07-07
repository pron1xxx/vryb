<?php 

$id = (int) $_GET['id'];
$db = createDbConnection();
$course = $db->query('SELECT * FROM courses WHERE id = :id', [':id'=>$id])->fetch();


$title = 'Добавление урока';
require VIEWS . "/course/createLesson.tpl.php";