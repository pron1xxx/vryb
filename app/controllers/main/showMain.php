<?php
$db = createDbConnection();
$courses = $db->query('SELECT c.*, u.channel_name, u.avatar_url FROM courses c LEFT JOIN users u ON c.author_id = u.id WHERE c.status = "public" LIMIT 6');
$categories = $db->query('SELECT * FROM courses_categories')->fetchAll();

$title = "Главная страница";
require VIEWS . "/home.tpl.php";