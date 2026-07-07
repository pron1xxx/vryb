<?php

header('Content-Type: application/json');

$db = createDbConnection();

if (isset($_POST['search']) && !empty($_POST['search'])) {

    $search = $_POST['search'];
    $searchTerm = "%$search%";

    if(isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin') {
        $courses = $db->query(
            "SELECT c.*, u.channel_name, u.avatar_url 
         FROM courses c 
         LEFT JOIN users u ON c.author_id = u.id 
         WHERE c.course_name LIKE ? 
            OR c.course_description LIKE ?
         LIMIT 2",
            [$searchTerm, $searchTerm]
        )->fetchAll();
    }
    else {
       $courses = $db->query(
            "SELECT c.*, u.channel_name, u.avatar_url 
         FROM courses c 
         LEFT JOIN users u ON c.author_id = u.id 
         WHERE (c.course_name LIKE ? 
            OR c.course_description LIKE ?)
            AND c.status = 'public'
         LIMIT 2",
            [$searchTerm, $searchTerm]
        )->fetchAll(); 
    }
    
    echo json_encode([
        'success' => true,
        'courses' => $courses
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Непредвиденная ошибка"
    ]);
    exit;
}
