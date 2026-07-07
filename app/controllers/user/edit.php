<?php

use mycls\Validator;
use mycls\ImgBBUploader;

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $title = 'Изменение профиля';
    require VIEWS . "/channels/profileEdit.tpl.php";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $db = createDbConnection();
    $errorsData = [];
    $validator = new Validator();

    // Обработка загрузки аватарки
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $validator->validation(['avatar' => $_FILES['avatar']], [
            'avatar' => [
                'uploadErrors' => true,
                'maxSize' => 6 * 1024 * 1024,
                'allowedTypes' => ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png'],
                'allowedExtensions' => ['jpg', 'jpeg', 'png']
            ]
        ]);

        $avatarErrors = $validator->getErrors();
        
        if (empty($avatarErrors)) {
            try {
                $uploader = new ImgBBUploader();
                $imageResult = $uploader->uploadImage($_FILES['avatar']['tmp_name']);

                $db->query(
                    "UPDATE users SET avatar_url = :avatar_url WHERE id = :user_id",
                    [
                        ':avatar_url' => $imageResult['url'],
                        ':user_id' => $_SESSION['user']['id']
                    ]
                );
                
                $_SESSION['user']['avatar_url'] = $imageResult['url'];
            } catch (Exception $e) {
                $errorsData['avatar'][] = "Ошибка загрузки изображения: " . $e->getMessage();
            }
        } else {
            $errorsData = array_merge($errorsData, $avatarErrors);
        }
    }

    // Определяем какие поля нужно обновлять
    $fillable = ["channel_name", 'channel_description'];
    
    // Если логин изменился, добавляем его в валидацию
    if ($_SESSION['user']['login'] != $_POST['login']) {
        $fillable[] = "login"; 
    }
    
    // Если пароль не пустой, добавляем его
    if (!empty($_POST['password'])) {
        $fillable[] = "password"; 
    }
    
    $data = load($fillable, $_POST);
    
    // Правила валидации (только для заполненных полей)
    $validationRules = [];
    
    if (isset($data['login'])) {
        $validationRules['login'] = [
            'required' => true,
            'min' => 7,
            'max' => 21,
            'preg' => "/^(?=(?:.*[A-Za-z]){5})[A-Za-z0-9]+$/",
            'unique' => "login"
        ];
    }
    
    if (isset($data['password'])) {
        $validationRules['password'] = [
            'required' => true,
            'min' => 8,
            'max' => 50
        ];
    }
    
    if (isset($data['channel_name'])) {
        $validationRules['channel_name'] = [
            'required' => true,
            'min' => 8,
            'max' => 50,
            'preg' => "/^(?=(?:.*[A-Za-zА-Яа-яёЁ]){5})[A-Za-zА-Яа-яёЁ \\d]+$/u"
        ];
    }
    
    if (isset($data['channel_description'])) {
        $validationRules['channel_description'] = [
            'required' => true,
            'min' => 8,
            'max' => 500
        ];
    }

    if (!empty($validationRules)) {
        $validator->validation($data, $validationRules);
        $formErrors = $validator->getErrors();
        $errorsData = array_merge($errorsData, $formErrors);
    }

    $_SESSION['validation']['editProfile'] = $errorsData;

    if (empty($errorsData)) {
        try {
            $updateFields = [];
            $params = [':id' => $_SESSION['user']['id']];
            
            if (isset($data['channel_name'])) {
                $updateFields[] = "channel_name = :channel_name";
                $params[':channel_name'] = $data['channel_name'];
                $_SESSION['user']['channel_name'] = $data['channel_name'];
            }
            
            if (isset($data['channel_description'])) {
                $updateFields[] = "channel_description = :channel_description";
                $params[':channel_description'] = $data['channel_description'];
            }
            
            if (isset($data['login'])) {
                $updateFields[] = "login = :login";
                $params[':login'] = $data['login'];
                $_SESSION['user']['login'] = $data['login'];
            }
            
            if (isset($data['password'])) {
                $updateFields[] = "password_hash = :password_hash";
                $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            // Если есть поля для обновления
            if (!empty($updateFields)) {
                $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
                $db->query($sql, $params);
            }
            
            redirect('/profile/edit');
            
        } catch (PDOException $e) {
            $_SESSION['validation']['editProfile']['server_error'] = "Ошибка сервера: " . $e->getMessage();
            redirect('/profile/edit');
        }
    } else {
        redirect('/profile/edit');
    }
}