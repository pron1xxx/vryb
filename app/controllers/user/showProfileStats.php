<?php

$channel_id = $_SESSION['user']['id'];
$db = createDbConnection();

$total_finish = 0;

$channel = $db->fetch(
    "SELECT * FROM users WHERE id = :id",
    [':id' => $channel_id]
);

$total_courses = $db->fetchColumn(
    "SELECT COUNT(*) FROM courses WHERE author_id = :author_id",
    [':author_id' => $channel_id]
);

$total_lessons = $db->fetchColumn(
    "SELECT COUNT(*) FROM lessons l 
             JOIN courses c ON l.course_id = c.id 
             WHERE c.author_id = :author_id",
    [':author_id' => $channel_id]
);

$total_saved = $db->fetchColumn(
    "SELECT COUNT(*) FROM saved_courses sc 
             JOIN courses c ON sc.course_id = c.id 
             WHERE c.author_id = :author_id",
    [':author_id' => $channel_id]
);

$total_certificates = $db->fetchColumn(
    "SELECT COUNT(*) FROM serteficates c 
             JOIN courses co ON c.course_id = co.id 
             WHERE co.author_id = :author_id",
    [':author_id' => $channel_id]
);

$courses = $db->fetchAll(
    "SELECT c.*,
                    COUNT(DISTINCT l.id) as lessons_count,
                    COUNT(DISTINCT sc.user_id) as saved_count,
                    COUNT(DISTINCT cert.id) as certificates_count
             FROM courses c
             LEFT JOIN lessons l ON c.id = l.course_id
             LEFT JOIN saved_courses sc ON c.id = sc.course_id
             LEFT JOIN serteficates cert ON c.id = cert.course_id
             WHERE c.author_id = :author_id
             GROUP BY c.id
             ORDER BY c.created_at DESC",
    [':author_id' => $channel_id]
);

foreach ($courses as &$course) {
    $course['lessons'] = $db->fetchAll(
        "SELECT 
            l.*,
            COUNT(DISTINCT cl.user_id) as completed_count
         FROM lessons l
         LEFT JOIN completed_lessons cl ON l.id = cl.lesson_id
         WHERE l.course_id = :course_id
         GROUP BY l.id",
        [':course_id' => $course['id']]
    );

    foreach ($course['lessons'] as $lesson) {
        if (!empty($lesson['completed_count'])) {
            $total_finish += 1;
        }
    }
}

$total_files = count($db->fetchAll("SELECT * FROM files WHERE uploaded_by = :user_id", [':user_id' => $channel_id]));
$subscribers_count = $db->fetchAll("SELECT COUNT(*) FROM subscribes WHERE subscribed_user_id = :user_id", [':user_id' => $channel_id])[0][0];

function swith_status($status)
{
    switch ($status) {
        case 'public':
            return 'Публичный';
        case 'moderation':
            return 'На модерации';
        case 'development':
            return 'В разработке';
        case 'hidden':
            return 'Скрытый';
        default:
            return $status;
    }
}

$title = 'Ваша статистика';
require VIEWS . '/channels/channel_stats.tpl.php';
