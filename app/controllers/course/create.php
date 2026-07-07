<?php
use mycls\Db;
use mycls\Validator;
use mycls\ImgBBUploader;

$db = createDbConnection();

$categories_bd = $db->query('SELECT category_name FROM courses_categories')->fetchAll();
$categories = array_column($categories_bd, 'category_name');

$data = array_merge(
    load(['course_name', 'course_description', 'course_category'], $_POST),
    ['course_preview' => $_FILES['course_preview']]
);

$validator = new Validator;
$validator->validation($data, [
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
    ],
    'course_preview' => [
        'uploadErrors' => true,
        'maxSize' => 6 * 1024 * 1024,
        'allowedTypes' => ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png'],
        'allowedExtensions' => ['jpg', 'jpeg', 'png']
    ]
]);

$errors = $validator->getErrors();

if (empty($errors)) {
    try {
        $uploader = new ImgBBUploader();
        $imageResult = $uploader->uploadImage($_FILES['course_preview']['tmp_name']);
        
        $stmt = $db->query(
            "INSERT INTO courses (author_id, course_name, course_description, category, preview_url) 
             VALUES (:author_id, :name, :desc, :course_category, :preview)",
            [
                ':author_id' => $_SESSION['user']['id'],
                ':name' => $data['course_name'],
                ':desc' => $data['course_description'],
                ':course_category' => $data['course_category'],
                ':preview' => $imageResult['url']
            ]
        );
        
        $course_id = $db->lastInsertId(null);
        redirect("/lesson/create/?id=$course_id");
        
    } catch (Exception $e) {
        $errors['course_preview'][] = "Ошибка загрузки изображения: " . $e->getMessage();
        $_SESSION['validation']['create'] = $errors;
        redirect('/create');
    }
} else {
    $_SESSION['validation']['create'] = $errors;
    redirect('/create');
}