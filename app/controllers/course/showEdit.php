<?php
$db = createDbConnection();

$id = (int) $_GET['id'];
$course_data = $db->query('SELECT * FROM courses WHERE id = :id', [':id' => $id])->fetchAll()[0];

$lessons = $db->query("SELECT * FROM lessons WHERE course_id = {$course_data['id']}")->fetchAll();

if($course_data['status'] == 'moderation') {
    $error = "Курс находится на модерации и его сейчас нельзя редактировать";
    require_once VIEWS . '/errors/noAccess.tpl.php';
    die;
}

$categories = $db->query('SELECT * FROM courses_categories')->fetchAll();

$title = 'Изменение курса';
require VIEWS . "/course/editCourse.tpl.php";
