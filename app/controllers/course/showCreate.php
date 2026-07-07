<?php 

$db = createDbConnection();
$categories = $db->query('SELECT * FROM courses_categories')->fetchAll();

$title = 'Создание курса';
require VIEWS . "/course/createCourse.tpl.php";