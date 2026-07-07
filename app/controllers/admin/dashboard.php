<?php

$db = createDbConnection();

// Получаем все курсы
$courses = $db->query('SELECT c.*, u.channel_name, u.avatar_url FROM courses c LEFT JOIN users u ON c.author_id = u.id ORDER BY c.created_at DESC LIMIT 6')->fetchAll();

// Получаем курсы на модерации
$moderation_courses_list = $db->query("SELECT c.*, u.channel_name FROM courses c LEFT JOIN users u ON c.author_id = u.id WHERE c.status = 'moderation' ORDER BY c.created_at DESC")->fetchAll();

// Получаем всех пользователей
$users = $db->query("SELECT u.*,b.banned_before AS status FROM users u LEFT JOIN blocked_users b ON u.id = b.user_id ORDER BY id DESC")->fetchAll();

$stats = $db->query("
    SELECT 
        (SELECT COUNT(*) FROM courses) as total_courses,
        (SELECT COUNT(*) FROM courses WHERE status = 'moderation') as moderation_courses,
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as new_users_today
")->fetch();

$total_courses = $stats['total_courses'] ?? 0;
$moderation_courses = $stats['moderation_courses'] ?? 0;
$total_users = $stats['total_users'] ?? 0;
$new_users_today = $stats['new_users_today'] ?? 0;

$status_array = ['moderation', 'public', 'hidden', 'development'];

function swith_status($switch_item)
{
    switch ($switch_item) {
        case "public":
            return "Публичный";
        case "moderation":
            return "На модерации";
        case "development":
            return "В разработке";
        case "hidden":
            return "Скрытый";
    }
}

$title = 'Админ панель';
require VIEWS . "/admin/dashboard.tpl.php";
