<?php

use mycls\Validator;
use mycls\Db;
use mycls\ImgBBUploader;

$db = createDbConnection();
$validator = new Validator;

$fileableLesson = array_merge(
    load(['lesson_name', 'lesson_description'], $_POST),
    [
        'lesson_video' => $_FILES['lesson_video'] ?? null,
        'lesson_preview' => $_FILES['lesson_preview'] ?? null
    ]
);

$validator->validation($fileableLesson, [
    'lesson_name' => [
        'required' => true,
        'min' => 5,
        'max' => 100
    ],
    'lesson_description' => [
        'required' => true,
        'min' => 10,
        'max' => 500
    ],
    'lesson_preview' => [
        'uploadErrors' => true,
        'maxSize' => 6 * 1024 * 1024,
        'allowedTypes' => ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png'],
        'allowedExtensions' => ['jpg', 'jpeg', 'png']
    ],
    'lesson_video' => [
        'uploadErrors' => true,
        'maxSize' => 500 * 1024 * 1024,
        'allowedTypes' => ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'],
        'allowedExtensions' => ['mp4', 'mov', 'avi', 'mpeg']
    ]
]);

$lesson_errors = $validator->getErrors();

$all_errors = [];
$all_errors['lesson'] = $lesson_errors;

if (isset($_POST['questions'])) {
    foreach ($_POST['questions'] as $question_number => $values) {

        $fileable_question = ['text', 'correct_answer'];
        $question_data = load($fileable_question, $_POST['questions'][$question_number]);

        $fileable_answers = ['0', '1', '2', '3'];
        $answers_data = load($fileable_answers, $_POST['questions'][$question_number]['answers']);

        $validator->validation($question_data, [
            'text' => [
                'required' => true,
                'min' => 1,
                'max' => 50
            ],
            'correct_answer' => [
                'required' => true,
                'min' => 1,
                'max' => 1
            ]
        ]);

        $question_errors = $validator->getErrors();
        $answerValidationRules = [
            'required' => true,
            'min' => 1,
            'max' => 20
        ];

        $validator->validation($answers_data, [
            '0' => $answerValidationRules,
            '1' => $answerValidationRules,
            '2' => $answerValidationRules,
            '3' => $answerValidationRules,
        ]);

        $answers_errors = $validator->getErrors();

        if (!empty($question_errors) || !empty($answers_errors)) {
            $all_errors['questions'][$question_number] = [
                'question_errors' => $question_errors,
                'answers_errors' => $answers_errors
            ];
        }
    }
}

$lessonLectFiles = normalizeFilesArray($_FILES['lesson_lect'] ?? []);
$lessonPrakFiles = normalizeFilesArray($_FILES['lesson_prak'] ?? []);

$lectErrors['length'] = checkCount($lessonLectFiles, 5);
$prakErrors['length'] = checkCount($lessonLectFiles, 5);

$lectErrors['files'] = validateFiles($lessonLectFiles, 'lecture');
$prakErrors['files'] = validateFiles($lessonPrakFiles, 'practice');

$filesErrors = array_merge($lectErrors, $prakErrors);

$has_errors = !empty($lesson_errors) || !empty($all_errors['questions']) || !empty($filesErrors['length']) || !empty($filesErrors['files']);


if (!$has_errors) {
    try {
        // Создаем папку для видео
        $videosDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/videos/';

        if (!is_dir($videosDir)) {
            mkdir($videosDir, 0777, true);
        }

        // Создаем папку для файлов
        $filesDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/files/';

        if (!is_dir($filesDir)) {
            mkdir($filesDir, 0777, true);
        }

        // Загрузка превью урока
        $imgbbUploader = new ImgBBUploader();
        $previewResult = $imgbbUploader->uploadImage($_FILES['lesson_preview']['tmp_name']);

        // Локальное сохранение видео
        $videoFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $_FILES['lesson_video']['name']);
        $videoUploadPath = $videosDir . $videoFileName;

        if (move_uploaded_file($_FILES['lesson_video']['tmp_name'], $videoUploadPath)) {

            $videoUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/public/uploads/videos/' . $videoFileName;
            // Сохраняем урок в базу
            $db->query(
                "INSERT INTO lessons (course_id, title, description, preview_url, video_url, video_id, playback_id) 
                 VALUES (:course_id, :name, :desc, :preview, :video_url, :video_id, :playback_id)",
                [
                    ':course_id' => (int) $_GET['id'],
                    ':name' => $fileableLesson['lesson_name'],
                    ':desc' => $fileableLesson['lesson_description'],
                    ':preview' => $previewResult['url'],
                    ':video_url' => $videoUrl, // Сохраняем только имя файла
                    ':video_id' => 'local_' . pathinfo($videoFileName, PATHINFO_FILENAME),
                    ':playback_id' => 'local'
                ]
            );
            $db->query(
                'UPDATE courses SET status = :status WHERE id = :course_id',
                [
                    ':status' => 'development',
                    ':course_id' => (int) $_GET['id']
                ]
            );

            $lesson_id = $db->lastInsertId();
            $filesUrls = [
                'lect' => [],
                'prak' => []
            ];

            $filesUrls['lect'] = saveFiles($lessonLectFiles, $filesDir, 'lect');
            $filesUrls['prak'] = saveFiles($lessonPrakFiles, $filesDir, 'prak');

            $insertData = [];

            foreach ($filesUrls as $fileType => $files) {
                foreach ($files as $fileInfo) {
                    $insertData[] = [
                        'lesson_id' => $lesson_id,
                        'file_url' => $fileInfo['url'],
                        'uploaded_by' => $_SESSION['user']['id'],
                        'file_type' => $fileType,
                        'file_extension' => $fileInfo['extension'],
                        'original_name' => $fileInfo['name'],
                        'size' => $fileInfo['size'],
                    ];
                }
            }

            if (!empty($insertData)) {
                $placeholders = [];
                $values = [];

                foreach ($insertData as $index => $row) {
                    $placeholders[] = "(:lesson_id_$index, :file_url_$index, :uploaded_by_$index, :file_type_$index, :file_extension_$index, :original_name_$index, :size_$index)";

                    $values[":lesson_id_$index"] = $row['lesson_id'];
                    $values[":file_url_$index"] = $row['file_url'];
                    $values[":uploaded_by_$index"] = $row['uploaded_by'];
                    $values[":file_type_$index"] = $row['file_type'];
                    $values[":file_extension_$index"] = $row['file_extension'];
                    $values[":original_name_$index"] = $row['original_name'];
                    $values[":size_$index"] = $row['size'];
                }

                $sql = "INSERT INTO files (lesson_id, file_url, uploaded_by, file_type, file_extension, original_name, size) 
            VALUES " . implode(', ', $placeholders);

                $db->query($sql, $values);
            }

            // Создаем тест (если есть)
            if (!empty($_POST['test_title']) && !empty($_POST['questions'])) {
                // Создаем тест
                $db->query(
                    "INSERT INTO tests (lesson_id, test_title) VALUES (:lesson_id, :title)",
                    [
                        ':lesson_id' => $lesson_id,
                        ':title' => $_POST['test_title']
                    ]
                );

                $test_id = $db->lastInsertId();

                // Создаем вопросы и ответы
                foreach ($_POST['questions'] as $question_data) {
                    if (empty($question_data['text']) || empty($question_data['answers'])) {
                        continue;
                    }

                    // Создаем вопрос
                    $db->query(
                        "INSERT INTO questions (test_id, question_text, correct_answer) 
                         VALUES (:test_id, :text, :correct)",
                        [
                            ':test_id' => $test_id,
                            ':text' => $question_data['text'],
                            ':correct' => $question_data['correct_answer']
                        ]
                    );

                    $question_id = $db->lastInsertId();

                    // Создаем ответы
                    foreach ($question_data['answers'] as $index => $answer_text) {
                        if (!empty($answer_text)) {
                            $db->query(
                                "INSERT INTO answers (question_id, answer_text, order_index) 
                                 VALUES (:question_id, :text, :order)",
                                [
                                    ':question_id' => $question_id,
                                    ':text' => $answer_text,
                                    ':order' => $index + 1
                                ]
                            );
                        }
                    }
                }
            }

            unset($_SESSION['lesson']['errors']);
            redirect("/course/edit/?id=" . $_GET['id']);
        } else {
            throw new Exception("Не удалось сохранить видео файл");
        }
    } catch (Exception $e) {
        $all_errors['upload'][] = "Ошибка загрузки: " . $e->getMessage();
        $_SESSION['lesson']['errors'] = $all_errors;
        redirect("/lesson/create/?id=" . $_GET['id']);
    }
} else {
    fillOldvalue("createLesson");
    $_SESSION['lesson']['errors'] = array_merge($all_errors, $filesErrors);
    redirect("/lesson/create/?id=" . $_GET['id']);
}
