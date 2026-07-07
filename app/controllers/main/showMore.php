<?php
header('Content-Type: application/json');

if (isset($_POST['counts_courses']) && !empty($_POST['counts_courses'])) {
    $db = createDbConnection();
    $offset = (int)$_POST['counts_courses'];
    $limit = 6;

    if (isset($_SESSION['user']) && $_SESSION['user']["role"] == 'admin') {
        if (!empty($_POST['search_str'])) {
            $search = $_POST['search_str'];
            $searchTerm = "%$search%";
            $coursesData = $db->query(
                "SELECT c.*, u.channel_name, u.avatar_url 
         FROM courses c 
         LEFT JOIN users u ON c.author_id = u.id 
         WHERE c.course_name LIKE ? 
            OR c.course_description LIKE ?
         LIMIT $limit OFFSET $offset",
                [$searchTerm, $searchTerm]
            )->fetchAll();
        } else {
            $coursesData = $db->query(
                "SELECT c.*, u.channel_name, u.avatar_url 
        FROM courses c 
        LEFT JOIN users u ON c.author_id = u.id  
        LIMIT $limit OFFSET $offset"
            )->fetchall();
        }
    } else {
        if (!empty($_POST['search_str'])) {
            $search = $_POST['search_str'];
            $searchTerm = "%$search%";
            $coursesData = $db->query(
                "SELECT c.*, u.channel_name, u.avatar_url 
         FROM courses c 
         LEFT JOIN users u ON c.author_id = u.id 
         WHERE (c.course_name LIKE ? 
            OR c.course_description LIKE ?)
            AND c.status = 'public'
         LIMIT $limit OFFSET $offset",
                [$searchTerm, $searchTerm]
            )->fetchAll();
        } else {
            $coursesData = $db->query(
                "SELECT c.*, u.channel_name, u.avatar_url 
        FROM courses c 
        LEFT JOIN users u ON c.author_id = u.id  
        WHERE c.status = 'public'
        LIMIT $limit OFFSET $offset"
            )->fetchall();
        }
    }
    
    if (empty($coursesData)) {
        echo json_encode([
            'success' => false,
            'message' => "Невозможно загрузить больше курсов"
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'courses' => $coursesData
    ]);
} else {
    echo json_encode([
        'success' => false,
        'courses' => "Непредвиденая ошибка"
    ]);
}
