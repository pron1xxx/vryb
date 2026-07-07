<?php

header('Content-Type: application/json');


// Проверяем наличие course_id
if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Не указан ID курса'
    ]);
    exit;
}

$userId = (int) $_SESSION['user']['id'];
$courseId = (int) $_POST['course_id'];

try {
    $db = createDbConnection();
    
    // Проверяем существование курса
    $courseCheck = $db->query("SELECT id FROM courses WHERE id = :id", [':id' => $courseId]);
    
    if (!$courseCheck->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Курс не найден'
        ]);
        exit;
    }
    
    // Проверяем, не сохранен ли курс уже
    $savedCheck = $db->query(
        "SELECT id FROM saved_courses WHERE user_id = :user_id AND course_id = :course_id",
        [':user_id' => $userId, ':course_id' => $courseId]
    );
    
    if ($savedCheck->fetch()) {
        // удаляем из сохраненных
        $deleteResult = $db->query(
            "DELETE FROM saved_courses WHERE user_id = :user_id AND course_id = :course_id",
            [':user_id' => $userId, ':course_id' => $courseId]
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Курс удален из сохраненных',
            'action' => 'removed',
            'saved' => false
        ]);
    } else {
        // Сохраняем курс
        $insertResult = $db->query(
            "INSERT INTO saved_courses (user_id, course_id, saved_at) VALUES (:user_id, :course_id, NOW())",
            [':user_id' => $userId, ':course_id' => $courseId]
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Курс успешно сохранен!',
            'action' => 'saved',
            'saved' => true
        ]);
    }
    
} catch (PDOException $e) {
    // Логируем ошибку
    echo $e->getMessage();
    
    echo json_encode([
        'success' => false,
        'message' => 'Произошла ошибка при сохранении. Попробуйте позже.'
    ]);
} catch (Exception $e) {
    // Обрабатываем другие исключения
    error_log('Общая ошибка: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Произошла непредвиденная ошибка.'
    ]);
}