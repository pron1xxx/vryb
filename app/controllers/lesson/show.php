<?php
session_start();

$db = createDbConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    require VIEWS . '/errors/404.tpl.php';
    die;
}

$lessonData = $db->query("SELECT * FROM lessons WHERE id = :id", [':id' => $id])->fetch();
$files_data = $db->query("SELECT * FROM files WHERE lesson_id = :id", [':id' => $id])->fetchAll(PDO::FETCH_ASSOC);
if(isset($_SESSION['user'])) {
    $hasCompleteLesson = $db->query("SELECT * FROM completed_lessons WHERE lesson_id = :lesson_id AND user_id = :user_id", [':lesson_id'=>$lessonData['id'], ':user_id'=>$_SESSION['user']['id']])->fetch();
}


if (!$lessonData) {
    require VIEWS . '/errors/404.tpl.php';
    die;
}

// Проверка доступности курса
$course = $db->query("SELECT * FROM courses WHERE id = :id", [':id' => $lessonData['course_id']])->fetch();
if ($course['status'] != 'public' && $course['author_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] != 'admin') {
    require VIEWS . '/errors/noAccess.tpl.php';
    die;
}

$test = $db->query("SELECT * FROM tests WHERE lesson_id = :lesson_id", [':lesson_id'=>$id])->fetch();

if($test != false) {
    $questions = $db->query("SELECT * FROM questions WHERE test_id = :test_id", [':test_id'=>$test['id']])->fetchAll();
    $questionsData = [];
    
    foreach($questions as $key => $question) {
        $questionsData[$key] = [
            'question_data' => [
                'question_text' => $question['question_text'], 
                'question_id' => $question['id']
            ],
            'answers' => []
        ];
        
        $answers = $db->query(
            "SELECT * FROM answers WHERE question_id = :question_id ORDER BY order_index ASC", 
            [':question_id'=>$question['id']]
        )->fetchAll();
        
        // Проверяем, что ответы есть
        if ($answers) {
            foreach($answers as $answer) {
                $questionsData[$key]['answers'][] = [
                    'answer_text' => $answer['answer_text'],
                    'order_index' => $answer['order_index'],
                    'answer_id' => $answer['id']
                ];
            }
        }
    }
}


if (isset($questionsData)) {$questionsJson = json_encode($questionsData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);}

$title = 'Урок курса';
require VIEWS . "/lesson/lesson.tpl.php";