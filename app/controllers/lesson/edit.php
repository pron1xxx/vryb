<?php

use mycls\Validator;
use mycls\ImgBBUploader;
use mycls\VideoProtection;

$db = createDbConnection();
$validator = new Validator();

$id = (int) $_GET['id'];
$lesson_data = $db->query('SELECT l.*, c.course_name, c.author_id FROM lessons l LEFT JOIN courses c ON l.course_id = c.id WHERE l.id = :id', [":id" => $id])->fetch();

if (!$lesson_data) {
    require(VIEWS . '/errors/404.tpl.php');
    exit;
}

if ($lesson_data['author_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] != 'admin') {
    $error = 'Урок недоступен';
    require(VIEWS . '/errors/noAccess.tpl.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lessonLectFiles = normalizeFilesArray($_FILES['lesson_lect'] ?? []);
    $lessonPrakFiles = normalizeFilesArray($_FILES['lesson_prak'] ?? []);

    $lectErrors['length'] = checkCount($lessonLectFiles, 5);
    $prakErrors['length'] = checkCount($lessonLectFiles, 5);

    $dbFilesData = $db->query("SELECT * FROM files WHERE lesson_id = :lesson_id", [':lesson_id' => $lesson_data['id']])->fetchAll();
    $lectFilesCount = 0;
    $prakFilesCount = 0;
    foreach ($dbFilesData as $file) {
        if ($file['file_type'] == 'lect') {
            $lectFilesCount += 1;
        } else {
            $prakFilesCount += 1;
        }
    }
    if (count($lessonLectFiles) + $lectFilesCount > 5) {
        $lectErrors['lect_length'] = 'Максимум файлов 5 лекционных файлов';
    }
    if (count($lessonPrakFiles) + $prakFilesCount > 5) {
        $prakErrors['prak_length'] = 'Максимум файлов 5 практических файлов';
    }

    if (!empty($lessonLectFiles)) {
        $lectErrors['files'] = validateFiles($lessonLectFiles, 'lecture');
    }
    if (!empty($lessonPrakFiles)) {
        $prakErrors['files'] = validateFiles($lessonPrakFiles, 'practice');
    }

    $filesErrors = array_merge($lectErrors, $prakErrors);

    $all_errors = [];

    $fillableLesson = array_merge(
        load(['lesson_name', 'lesson_description'], $_POST),
        [
            'lesson_video' => $_FILES['lesson_video'] ?? null,
            'lesson_preview' => $_FILES['lesson_preview'] ?? null
        ]
    );

    // Правила валидации
    $validationRules = [
        'lesson_name' => [
            'required' => true,
            'min' => 5,
            'max' => 100
        ],
        'lesson_description' => [
            'required' => true,
            'min' => 10,
            'max' => 500
        ]
    ];

    // Добавляем правила для файлов только если они загружены
    if (!empty($_FILES['lesson_preview']['name'])) {
        $validationRules['lesson_preview'] = [
            'uploadErrors' => true,
            'maxSize' => 6 * 1024 * 1024,
            'allowedTypes' => ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png'],
            'allowedExtensions' => ['jpg', 'jpeg', 'png']
        ];
    }

    if (!empty($_FILES['lesson_video']['name'])) {
        $validationRules['lesson_video'] = [
            'uploadErrors' => true,
            'maxSize' => 500 * 1024 * 1024,
            'allowedTypes' => ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'],
            'allowedExtensions' => ['mp4', 'mov', 'avi', 'mpeg']
        ];
    }

    $validator->validation($fillableLesson, $validationRules);
    $lesson_errors = $validator->getErrors();
    $all_errors['lesson'] = $lesson_errors;

    if (isset($_POST['questions'])) {
        foreach ($_POST['questions'] as $question_key => $values) {
            $question_id = isset($values['question_id']) ? (int)$values['question_id'] : null;

            $fillable_question = ['text', 'correct_answer'];
            $question_data = load($fillable_question, $values);

            $fillable_answers = ['0', '1', '2', '3'];
            $answers_data = load($fillable_answers, $values['answers'] ?? []);

            // Валидация вопроса
            $validator->validation($question_data, [
                'text' => [
                    'required' => true,
                    'min' => 5,
                    'max' => 50
                ],
                'correct_answer' => [
                    'required' => true,
                    'min' => 1,
                    'max' => 4,
                    'preg' => '/^[1-4]$/'
                ]
            ]);

            $question_errors = $validator->getErrors();

            // Валидация ответов
            $answerValidationRules = [
                'required' => true,
                'min' => 1,
                'max' => 30
            ];

            $validator->validation($answers_data, [
                '0' => $answerValidationRules,
                '1' => $answerValidationRules,
                '2' => $answerValidationRules,
                '3' => $answerValidationRules,
            ]);

            $answers_errors = $validator->getErrors();

            if (!empty($question_errors) || !empty($answers_errors)) {
                $all_errors['questions'][$question_key] = [
                    'question_errors' => $question_errors,
                    'answers_errors' => $answers_errors
                ];
            }
        }
    }

    $has_errors = !empty($lesson_errors) || !empty($all_errors['questions']) || !empty($filesErrors['prak_length']) || !empty($filesErrors['lect_length']) || !empty($filesErrors['files']);

    if (!$has_errors) {
        try {
            $db->beginTransaction();

            $updateData = [
                ':id' => $id,
                ':name' => $fillableLesson['lesson_name'],
                ':desc' => $fillableLesson['lesson_description']
            ];

            $updateFields = [
                "title = :name",
                "description = :desc"
            ];

            if (!empty($_FILES['lesson_preview']['name'])) {
                $imgbbUploader = new ImgBBUploader();
                $previewResult = $imgbbUploader->uploadImage($_FILES['lesson_preview']['tmp_name']);

                $updateFields[] = "preview_url = :preview";
                $updateData[':preview'] = $previewResult['url'];
            }

            if (!empty($_FILES['lesson_video']['name'])) {
                $videosDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/videos/';

                if (!is_dir($videosDir)) {
                    mkdir($videosDir, 0777, true);
                }

                // Удаляем старое видео
                $oldVideoPath = $videosDir . $lesson_data['video_url'];
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
                }

                // Сохраняем новое видео
                $videoFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $_FILES['lesson_video']['name']);
                $videoUploadPath = $videosDir . $videoFileName;
                $videoUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/public/uploads/videos/' . $videoFileName;

                if (move_uploaded_file($_FILES['lesson_video']['tmp_name'], $videoUploadPath)) {
                    $updateFields[] = "video_url = :video_url";
                    $updateFields[] = "video_id = :video_id";
                    $updateFields[] = "playback_id = :playback_id";

                    $updateData[':video_url'] = $videoUrl;
                    $updateData[':video_id'] = 'local_' . pathinfo($videoFileName, PATHINFO_FILENAME);
                    $updateData[':playback_id'] = 'local';
                } else {
                    throw new Exception("Не удалось сохранить видео файл");
                }
            }

            // Выполняем обновление урока
            $sql = "UPDATE lessons SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $db->query($sql, $updateData);

            $db->query(
                'UPDATE courses SET status = :status WHERE id = :course_id',
                [
                    ':status' => 'development',
                    ':course_id' => $lesson_data['course_id']
                ]
            );

            if (!empty($_POST['test_title'])) {
                $existingTest = $db->query(
                    'SELECT id FROM tests WHERE lesson_id = :lesson_id',
                    [':lesson_id' => $id]
                )->fetch();

                if ($existingTest) {
                    $db->query(
                        "UPDATE tests SET test_title = :title WHERE id = :test_id",
                        [
                            ':title' => $_POST['test_title'],
                            ':test_id' => $existingTest['id']
                        ]
                    );
                    $test_id = $existingTest['id'];

                    $existingQuestions = $db->query(
                        'SELECT id FROM questions WHERE test_id = :test_id',
                        [':test_id' => $test_id]
                    )->fetchAll();

                    $existingQuestionIds = array_column($existingQuestions, 'id');
                    $processedQuestionIds = [];

                    foreach ($_POST['questions'] as $question_data) {
                        if (empty($question_data['text']) || empty($question_data['answers'])) {
                            continue;
                        }

                        $question_id = isset($question_data['question_id']) ? (int)$question_data['question_id'] : null;

                        if ($question_id && in_array($question_id, $existingQuestionIds)) {
                            $db->query(
                                "UPDATE questions SET question_text = :text, correct_answer = :correct WHERE id = :question_id",
                                [
                                    ':text' => $question_data['text'],
                                    ':correct' => $question_data['correct_answer'],
                                    ':question_id' => $question_id
                                ]
                            );

                            $processedQuestionIds[] = $question_id;

                            // Обновляем ответы
                            foreach ($question_data['answers'] as $index => $answer_text) {
                                // Получаем ID ответа из массива answer_ids, если он есть
                                $answer_id = isset($question_data['answer_ids'][$index]) ? (int)$question_data['answer_ids'][$index] : null;

                                if ($answer_id) {
                                    // Обновляем существующий ответ
                                    $db->query(
                                        "UPDATE answers SET answer_text = :text WHERE id = :answer_id",
                                        [
                                            ':text' => $answer_text,
                                            ':answer_id' => $answer_id
                                        ]
                                    );
                                } else {
                                    // Добавляем новый ответ
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
                        } else {
                            // Добавляем новый вопрос
                            $db->query(
                                "INSERT INTO questions (test_id, question_text, correct_answer) 
                                     VALUES (:test_id, :text, :correct)",
                                [
                                    ':test_id' => $test_id,
                                    ':text' => $question_data['text'],
                                    ':correct' => $question_data['correct_answer']
                                ]
                            );

                            $new_question_id = $db->lastInsertId();
                            $processedQuestionIds[] = $new_question_id;

                            // Добавляем ответы
                            foreach ($question_data['answers'] as $index => $answer_text) {
                                if (!empty($answer_text)) {
                                    $db->query(
                                        "INSERT INTO answers (question_id, answer_text, order_index) 
                                             VALUES (:question_id, :text, :order)",
                                        [
                                            ':question_id' => $new_question_id,
                                            ':text' => $answer_text,
                                            ':order' => $index + 1
                                        ]
                                    );
                                }
                            }
                        }
                    }

                    $questionsToDelete = array_diff($existingQuestionIds, $processedQuestionIds);
                    if (!empty($questionsToDelete)) {
                        $placeholders = implode(',', array_fill(0, count($questionsToDelete), '?'));
                        $db->query("DELETE FROM questions WHERE id IN ($placeholders)", array_values($questionsToDelete));
                    }
                } else {
                    $db->query(
                        "INSERT INTO tests (lesson_id, test_title) VALUES (:lesson_id, :title)",
                        [
                            ':lesson_id' => $id,
                            ':title' => $_POST['test_title']
                        ]
                    );

                    $test_id = $db->lastInsertId();

                    // Добавляем вопросы
                    foreach ($_POST['questions'] as $question_data) {
                        if (empty($question_data['text']) || empty($question_data['answers'])) {
                            continue;
                        }

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

                        // Добавляем ответы
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
            } else {
                // Если тест удален, удаляем все связанные данные
                $db->query('DELETE FROM tests WHERE lesson_id = :lesson_id', [':lesson_id' => $id]);
            }

            $filesDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/files/';

            if (!is_dir($filesDir)) {
                mkdir($filesDir, 0777, true);
            }

            $lesson_id = $lesson_data['id'];
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

            $db->commit();

            unset($_SESSION['lesson']['errors']);
            unset($_SESSION['lesson']['file']['errors']);
            redirect("/course/edit/?id=" . $lesson_data['course_id']);
        } catch (Exception $e) {
            $db->rollBack();
            $all_errors['upload'][] = "Ошибка загрузки: " . $e->getMessage();
            $_SESSION['lesson']['errors'] = $all_errors;
            redirect("/lesson/edit/?id=" . $id);
        }
    } else {
        fillOldvalue("editLesson");
        $_SESSION['lesson']['errors'] = $all_errors;
        $_SESSION['lesson']['file']['errors'] = $filesErrors;
        redirect("/lesson/edit/?id=" . $id);
    }
} else {

    $videoFilename = $lesson_data['video_url'];
    $videoToken = VideoProtection::generateToken($videoFilename);

    $test_data = $db->query(
        'SELECT id, test_title FROM tests WHERE lesson_id = :lesson_id',
        [':lesson_id' => $lesson_data['id']]
    )->fetch();

    $files_data = $db->query("SELECT * FROM files WHERE lesson_id = :id", [':id' => $id])->fetchAll(PDO::FETCH_ASSOC);

    $questions_data = [];

    if ($test_data) {
        $questions = $db->query(
            'SELECT id, question_text, correct_answer FROM questions WHERE test_id = :test_id',
            ['test_id' => $test_data['id']]
        )->fetchAll();

        foreach ($questions as $question) {
            $answers = $db->query(
                "SELECT id, answer_text FROM answers WHERE question_id = :question_id ORDER BY order_index",
                [':question_id' => $question['id']]
            )->fetchAll();

            $questions_data[] = [
                'question' => $question,
                'answers' => $answers
            ];
        }
    }

    $title = 'Изменение урока';
    require VIEWS . '/course/editLesson.tpl.php';
}
