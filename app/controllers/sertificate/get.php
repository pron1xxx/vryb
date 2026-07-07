<?php

header('Content-Type: application/json');

$db = createDbConnection();


$name = $_POST['name'] ?? '';
$course_id = $_POST['course_id'] ?? '';


if (!empty(trim($name)) && !empty(trim($course_id))) {

    $courseData = $db->query("SELECT * FROM Courses WHERE id = :id", [':id' => $course_id])->fetchAll(PDO::FETCH_ASSOC);
    if (!$courseData) {
        echo json_encode([
            'success' => false,
            'message' => $courseData
        ]);
        exit;
    }

    if ($courseData['status'] != 'public') {
        echo json_encode([
            'success' => false,
            'message' => "Получение сертификата этого курса сейчас недоступно"
        ]);
        exit;
    }

    $completedLessonsDb = $db->query(
        "
SELECT l.id as lesson_id, cl.id as completed_id 
FROM LESSONS l 
LEFT JOIN completed_lessons cl ON l.id = cl.lesson_id AND cl.user_id = :user_id
WHERE l.course_id = :course_id",
        [
            ':course_id' => $course_id,
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

    if ($lessonsCount != $completedCount) {
        echo json_encode([
            'success' => false,
            'message' => "Не пройдены все уроки"
        ]);
        exit;
    }

    $hasSerteficate = $db->query("SELECT * FROM serteficates WHERE course_id = :course_id AND user_id = :user_id", [':course_id' => $course_id, ':user_id' => $_SESSION['user']['id']])->fetch(PDO::FETCH_ASSOC);

    if ($hasSerteficate) {
        echo json_encode([
            'success' => false,
            'message' => "Сертификат уже существует"
        ]);
        exit;
    }

    if (preg_match("/^[А-Я][а-я]{3,29}\s[А-Я][а-я]{3,29}$/u", $_POST['name'])) {
        $result = $db->query(
            "INSERT INTO serteficates (course_id, user_id, user_fio) VALUES (:course_id, :user_id, :user_fio)",
            [
                ':course_id' => $course_id,
                ':user_id' => $_SESSION['user']['id'],
                ':user_fio' => $name
            ]
        );

        $lastId = $db->fetchColumn("SELECT LAST_INSERT_ID()");

        if ($result && $lastId) {
            echo json_encode([
                'success' => true,
                'message' => "Сертификат успешно создан",
                'sertificate_id' => $lastId
            ]);
            exit;
        }

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => "Сертификат успешно создан"
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => "Ошибка при сохранении сертификата"
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Введите имя и фамилию русскими буквами (например: Иван Петров)"
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "Непредвиденная ошибка"
    ]);
}
