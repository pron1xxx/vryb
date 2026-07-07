<?php

use mycls\Validator;

header('Content-Type: application/json');

try {
    $db = createDbConnection();
    $validator = new Validator();

    // Валидация входных данных
    $user_id = $_POST['user_id'] ?? '';
    $comment = trim($_POST['comment'] ?? '');
    $banned_before = $_POST['banned_before'] ?? '';

    // Проверка заполненности полей
    if (empty($user_id) || empty($comment) || empty($banned_before)) {
        echo json_encode([
            'success' => false,
            'message' => 'Не все поля заполнены',
            'fields' => [
                'user_id' => empty($user_id),
                'comment' => empty($comment),
                'banned_before' => empty($banned_before)
            ]
        ]);
        exit;
    }

    $hasUser = $db->query(
        "SELECT id FROM users WHERE id = :id",
        [':id' => $user_id]
    )->fetch();

    if (!$hasUser) {
        echo json_encode([
            'success' => false,
            'message' => "Пользователь с ID {$user_id} не существует"
        ]);
        exit;
    }

    // Валидация комментария
    $errors = $validator->validation($_POST, [
        'comment' => [
            'required' => true,
            'min' => 5,
            'max' => 1000
        ]
    ]);

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Комментарий должен быть от 5 до 1000 символов',
            'errors' => $errors
        ]);
        exit;
    }

    // Проверка даты
    try {
        $dateTime = new DateTime($banned_before);
        $now = new DateTime();

        if ($dateTime < $now) {
            echo json_encode([
                'success' => false,
                'message' => 'Дата должна быть в будущем'
            ]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Некорректный формат даты'
        ]);
        exit;
    }

    // Проверяем, не заблокирован ли уже пользователь
    $existingBlock = $db->query(
        "SELECT id FROM blocked_users WHERE user_id = :user_id AND (banned_before > NOW() OR banned_before IS NULL)",
        [':user_id' => $user_id]
    )->fetch();

    if ($existingBlock) {
        echo json_encode([
            'success' => false,
            'message' => 'Пользователь уже заблокирован'
        ]);
        exit;
    }

    // Блокировка пользователя
    $db->query(
        "INSERT INTO blocked_users (user_id, admin_id, banned_before, comment) 
         VALUES (:user_id, :admin_id, :banned_before, :comment)",
        [
            ':user_id' => $user_id,
            ':admin_id' => $_SESSION['user']['id'],
            ':banned_before' => $banned_before,
            ':comment' => $comment
        ]
    );

    // Обновляем статус пользователя
    $db->query(
        "UPDATE users SET status = 'blocked' WHERE id = :user_id",
        [':user_id' => $user_id]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Пользователь успешно заблокирован',
        'user_id' => $user_id,
        'banned_until' => $banned_before
    ]);
} catch (Exception $e) {
    error_log("Error in admin/block: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Произошла внутренняя ошибка сервера'
    ]);
}
