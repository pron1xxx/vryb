<?php

$db = createDbConnection();
$courseId = (int) $_GET['id'];

$courseData = $db->query("SELECT c.*, u.channel_name, u.avatar_url FROM courses c LEFT JOIN users u ON c.author_id = u.id WHERE c.id = {$courseId}")->fetch();
if (isset($_SESSION['user'])) {
    $hasSaved = $db->query("SELECT * FROM saved_courses WHERE course_id = :course_id AND user_id = :user_id", [':course_id' => $courseData['id'], ':user_id' => $_SESSION['user']['id']])->fetch();
}

if ($courseData == false) {
    require VIEWS . '/errors/404.tpl.php';
    die;
}

if (isset($_SESSION['user'])) {
    $completedLessonsDb = $db->query(
        "
SELECT l.id as lesson_id, cl.id as completed_id 
FROM lessons l 
LEFT JOIN completed_lessons cl ON l.id = cl.lesson_id AND cl.user_id = :user_id
WHERE l.course_id = :course_id",
        [
            ':course_id' => $courseId,
            ':user_id' => $_SESSION['user']['id']
        ],
    )->fetchAll(PDO::FETCH_ASSOC);
    $completedLessons = [];
    $completedCount = 0;
    $lessonsCount = count($completedLessonsDb);

    foreach ($completedLessonsDb as $array_id => $array) {
    $completedLessons[$array['lesson_id']] = $array['completed_id'];
    if ($array['completed_id'] != NULL) {
        $completedCount += 1;
    }
}

if (isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['id'] == $courseData['author_id'])) {
    $status_history = $db->query("
        SELECT 
            ch.*,
            u.channel_name as admin_name,
            u.avatar_url as admin_avatar
        FROM course_status_history ch
        LEFT JOIN users u ON ch.admin_id = u.id
        WHERE ch.course_id = :course_id
        ORDER BY ch.created_at DESC
    ", [':course_id' => $courseData['id']])->fetchAll();
}

function swith_status($status) {
    switch ($status) {
        case 'public': return 'Публичный';
        case 'moderation': return 'На модерации';
        case 'development': return 'В разработке';
        case 'hidden': return 'Скрытый';
        default: return $status;
    }
}

$hasSerteficate = $db->query("SELECT * FROM serteficates WHERE course_id = :course_id AND user_id = :user_id", [':course_id' => $courseId, ':user_id' => $_SESSION['user']['id']])->fetch(PDO::FETCH_ASSOC);
}

$lessons = $db->query("SELECT * FROM lessons WHERE course_id = :course_id", ['course_id' => $courseId])->fetchAll(PDO::FETCH_ASSOC);
$title = 'Просмотр курса';
require VIEWS . "/course/course.tpl.php";
