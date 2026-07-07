<?php 

$db = createDbConnection();

$title = 'Профиль пользователя';
$courses = $db->query("SELECT * FROM courses WHERE author_id = {$_SESSION['user']['id']}")->fetchAll();
require VIEWS . '/channels/profile.tpl.php';