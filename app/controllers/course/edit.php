<?php

use mycls\Validator;
use mycls\ImgBBUploader;

$db = createDbConnection();

if(isset($_POST['public']) ) {
    $db->query("UPDATE courses SET status = :status WHERE id = :course_id",[':status'=>'public', ':course_id'=>$_GET['id']]);
    redirect("/profile");
} 
if(isset($_POST['hidden'])) {
    $db->query("UPDATE courses SET status = :status WHERE id = :course_id",[':status'=>'hidden', ':course_id'=>$_GET['id']]);
    redirect("/profile");
}

$categories_bd = $db->query('SELECT category_name FROM courses_categories')->fetchAll();

$categories = array_column($categories_bd, 'category_name');

$validator = new Validator;

$validator->validation($_POST, [
    'course_name' => [
        'required' => true,
        'min' => 15,
        'max' => 35
    ],
    'course_description' => [
        'required' => true,
        'min' => 20,
        'max' => 300
    ],
    'course_category' => [
        'required' => true,
        'inArray' => $categories
    ]
]);

// Проверка загруженного файла
if (isset($_FILES['course_preview']) && $_FILES['course_preview']['error'] === UPLOAD_ERR_OK) {
    $validator->validation(['course_preview' => $_FILES['course_preview']], [
        'course_preview' => [
            'uploadErrors' => true,
            'maxSize' => 6 * 1024 * 1024,
            'allowedTypes' => ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png'],
            'allowedExtensions' => ['jpg', 'jpeg', 'png']
        ]
    ]);

    if (empty($validator->getErrors())) {
        try {
            $uploader = new ImgBBUploader();
            $imageResult = $uploader->uploadImage($_FILES['course_preview']['tmp_name']);

            $db->query(
                "UPDATE courses SET preview_url = :preview WHERE id = :course_id",
                [
                    ':preview' => $imageResult['url'],
                    ':course_id' => $_GET['id']
                ]
            );
        } catch (Exception $e) {
            $errors['course_preview'][] = "Ошибка загрузки изображения: " . $e->getMessage();
            $_SESSION['validation']['create'] = $errors;
            redirect('/create');
        }
    }
}

$errors = $validator->getErrors();

if (!empty($errors)) {
    $_SESSION['validation']['create'] = $errors;
    redirect("/course/edit/?id={$_GET['id']}");
}

$db->query(
    "UPDATE courses SET course_name = :course_name, course_description = :course_description, status = :status, category = :category WHERE id = :course_id", 
    [
        ':course_name' => $_POST['course_name'], 
        ':course_description' => $_POST['course_description'],
        ':status' => 'moderation',
        ':course_id' => $_GET['id'],
        ':category' => $_POST['course_category']
    ]
);

redirect("/profile");