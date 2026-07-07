<?php

$db = createDbConnection();

$subscribes = $db->query('SELECT s.user_id, s.subscribed_user_id, u.id, u.channel_name, u.avatar_url FROM subscribes s LEFT JOIN users u ON s.subscribed_user_id = u.id WHERE s.user_id = :user_id', ['user_id'=>$_SESSION['user']['id']])->fetchAll();
$saved_courses = $db->query('SELECT s.user_id, s.course_id, c.author_id, c.course_name, c.preview_url, u.id, u.channel_name, u.avatar_url FROM saved_courses s LEFT JOIN courses c ON s.course_id = c.id LEFT JOIN users u ON u.id = c.author_id WHERE s.user_id = :user_id', [':user_id'=>$_SESSION['user']['id']])->fetchAll();

$title = "Сохраненое";
require VIEWS . "/saved.tpl.php";