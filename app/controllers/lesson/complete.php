<?php
header('Content-Type: application/json');

if (isset($_POST['lesson_id']) && !empty($_POST['lesson_id'])) {

    $db = createDbConnection();
    $lessonData = $db->query("SELECT * FROM lessons WHERE id = :lesson_id", [':lesson_id' => $_POST['lesson_id']])->fetch();
    if (!$lessonData) {
        echo json_encode([
            'success' => false,
            'message' => "Неверно передан урок"
        ]);
        exit();
    }

    $testData = $db->query("SELECT * FROM tests WHERE lesson_id = :lesson_id", [':lesson_id' => $_POST['lesson_id']])->fetch(PDO::FETCH_ASSOC);
    if (!$testData) {
        $db->query("INSERT INTO completed_lessons (lesson_id, user_id) VALUES (:lesson_id, :user_id)", [':lesson_id' => $_POST['lesson_id'], ':user_id' => $_SESSION['user']['id']]);
        echo json_encode([
            'success' => true,
            'message' => "Урок помечен как пройденный"
        ]);
        exit();
    }

    $userScores = $db->query("SELECT * FROM usersScores WHERE test_id = :test_id AND user_id = :user_id", [':test_id' => $testData['id'], ':user_id' => $_SESSION['user']['id']])->fetchAll(PDO::FETCH_ASSOC);
    if (!$userScores) {
        echo json_encode([
            'success' => false,
            'message' => "Пройдите тест, чтобы урок был засчитан пройденным"
        ]);
        exit();
    }

    foreach ($userScores as $score) {
        $percentageCorrect = (int) $score['score'] * 100 / (int) $score['total_questions'];

        if ($percentageCorrect >= 80) {
            $db->query("INSERT INTO completed_lessons (lesson_id, user_id) VALUES (:lesson_id, :user_id)", [':lesson_id' => $_POST['lesson_id'], ':user_id' => $_SESSION['user']['id']]);
            echo json_encode([
                'success' => true,
                'message' => "Урок помечен как пройденный"
            ]);
            exit();
        }
    }
    echo json_encode([
        'success' => false,
        'message' => "Тест должен быть пройден с неменее чем 80% правильных ответов"
    ]);
    exit();
} else {
    echo json_encode([
        'success' => false,
        'message' => "Неверный id урока"
    ]);
    exit();
}
