<?php

header('Content-Type: application/json');

try {
    $db = createDbConnection();

    $input = json_decode(file_get_contents('php://input'), true);

    $user_id = $input['user_id'] ?? 0;
    $csrf_token = $input['csrf_token'] ?? '';

    $hasBlock = $db->query("SELECT * FROM blocked_users WHERE user_id = :user_id", [':user_id' => $user_id])->fetchAll();

    if ($hasBlock) {
        $db->query('DELETE FROM blocked_users WHERE user_id = :user_id',  [':user_id' => $user_id]);
        echo json_encode(['success' => true, 'message' => 'Пользователь разблокирован']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Польщователь не в бане или не существует']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
}
