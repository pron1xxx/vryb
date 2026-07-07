<?php

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? '';
$query = trim($data['query'] ?? '');

if (!$query) {
    echo json_encode(['success' => false, 'message' => 'Пустой запрос']);
    exit;
}

$db = createDbConnection();

if ($type === 'name') {
    // Поиск по имени (частичное совпадение)
    $results = $db->query(
        "SELECT s.*, c.course_name 
         FROM serteficates s 
         LEFT JOIN courses c ON s.course_id = c.id 
         WHERE s.user_fio LIKE :query 
         ORDER BY s.received_to DESC",
        [':query' => "%$query%"]
    )->fetchAll(PDO::FETCH_ASSOC);
    
} else {
    // Поиск по ID (точное или частичное)
    if (is_numeric($query)) {
        // Поиск по числовому ID
        $results = $db->query(
            "SELECT s.*, c.course_name 
             FROM serteficates s 
             LEFT JOIN courses c ON s.course_id = c.id 
             WHERE s.id = :id",
            [':id' => $query]
        )->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Поиск по строковому ID (если есть)
        $results = $db->query(
            "SELECT s.*, c.course_name 
             FROM serteficates s 
             LEFT JOIN courses c ON s.course_id = c.id 
             WHERE s.id LIKE :query",
            [':query' => "%$query%"]
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}

echo json_encode([
    'success' => true,
    'results' => $results
]);