<?php
// Получаем ID теста
$test_id = (int) ($_GET['id'] ?? 0);
if ($test_id <= 0) {
    require VIEWS . '/errors/404.tpl.php';
    exit;
}

$db = createDbConnection();

// 1. Проверяем существование теста
$test = $db->query(
    "SELECT * FROM tests WHERE id = :id", 
    [':id' => $test_id]
)->fetch();

if (!$test) {
    require VIEWS . '/errors/404.tpl.php';
    exit;
}

// 2. Получаем вопросы теста с правильными ответами
$answers = $db->query(
    "SELECT id, correct_answer FROM questions WHERE test_id = :test_id", 
    [':test_id' => $test_id]
)->fetchAll(PDO::FETCH_KEY_PAIR); // [id => correct_answer]

// 3. Проверяем, что есть вопросы
if (empty($answers)) {
    redirect("/lesson/?id=" . $test['lesson_id']);
}

// 4. Обрабатываем ответы пользователя
$totalCorrectAnswers = 0;
$totalQuestions = count($answers);

foreach ($_POST as $key => $user_answer) {
    // Извлекаем ID вопроса из ключа вида "question-6"
    if (strpos($key, 'question-') === 0) {
        $question_id = (int) str_replace('question-', '', $key);
        
        // Проверяем, что вопрос существует в тесте
        if (isset($answers[$question_id])) {
            $correct_answer = (int) $answers[$question_id];
            $user_answer = (int) $user_answer;
            
            if ($user_answer === $correct_answer) {
                $totalCorrectAnswers++;
            }
        }
    }
}

try {
    $db->query(
        "INSERT INTO usersscores (user_id, test_id, total_questions, score, completed_at) 
         VALUES (:user_id, :test_id, :total_questions, :score, NOW())",
        [
            ':user_id' => $_SESSION['user']['id'],
            ':test_id' => $test_id,
            ':total_questions' => $totalQuestions,
            ':score' => $totalCorrectAnswers
        ]
    );
    
} catch (PDOException $e) {
    error_log('Ошибка сохранения результата теста: ' . $e->getMessage());
    $_SESSION['error'] = 'Ошибка при сохранении результата';
    header("Location: /lesson/?id=" . $test['lesson_id']);
    exit;
}

redirect("/test/result/?id={$test['id']}");