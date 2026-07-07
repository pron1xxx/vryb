<?php

use mycls\Validator;

header('Content-Type: application/json');

try {
    session_start();

    // Проверка прав доступа
    if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'moderator')) {
        echo json_encode([
            'success' => false,
            'message' => 'Доступ запрещен'
        ]);
        exit;
    }

    $validator = new Validator();
    $db = createDbConnection();

    $course_id = $_POST['course_id'] ?? '';
    $new_status = $_POST['status'] ?? '';
    $comment = $_POST['comment'] ?? '';

    error_log("Status change - course_id: $course_id, new_status: $new_status, comment: $comment");

    if (empty($course_id) || empty($new_status)) {
        echo json_encode([
            'success' => false,
            'message' => "Не все поля заполнены"
        ]);
        exit;
    }

    // Получаем текущий статус
    $current_status = $db->fetchColumn(
        'SELECT status FROM courses WHERE id = :id',
        [':id' => $course_id]
    );

    if ($current_status === false) {
        echo json_encode([
            'success' => false,
            'message' => "Курс не найден"
        ]);
        exit;
    }

    // Валидация
    if ($new_status == 'development') {
        $validator->validation($_POST, [
            'course_id' => ['required' => true],
            'comment' => [
                'required' => true,
                'min' => 20,
                'max' => 1000
            ]
        ]);
    } else {
        $validator->validation($_POST, [
            'course_id' => ['required' => true],
            'status' => [
                'required' => true,
                'inArray' => ['moderation', 'hidden', 'public', 'development']
            ]
        ]);
    }

    $errors = $validator->getErrors();

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибочный курс, статус или комментарий'
        ]);
        exit;
    }

    if ($new_status == $current_status) {
        echo json_encode([
            'success' => true,
            'message' => 'Статус уже установлен'
        ]);
        exit;
    }

    // Обновляем статус
    $updateResult = $db->execute(
        "UPDATE courses SET status = :status WHERE id = :id",
        [
            ':status' => $new_status,
            ':id' => $course_id
        ]
    );

    if ($updateResult === 0) {
        echo json_encode([
            'success' => false,
            'message' => "Не удалось обновить статус"
        ]);
        exit;
    }

    // Вставляем в историю
    $historyResult = $db->execute(
        "INSERT INTO course_status_history 
            (course_id, admin_id, old_status, new_status, comment, created_at) 
         VALUES 
            (:course_id, :admin_id, :old_status, :new_status, :comment, NOW())",
        [
            ':course_id' => $course_id,
            ':admin_id' => $_SESSION['user']['id'],
            ':old_status' => $current_status,
            ':new_status' => $new_status,
            ':comment' => !empty($comment) ? $comment : null
        ]
    );

    if ($historyResult === 0) {
        error_log("WARNING: Status updated but history not recorded for course $course_id");
        echo json_encode([
            'success' => true,
            'warning' => 'Статус обновлен, но история не записана',
            'message' => 'Статус курса изменен'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Статус курса успешно изменен'
        ]);
    }
} catch (Exception $e) {
    error_log('Error changing status: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при обновлении статуса'
    ]);
}
