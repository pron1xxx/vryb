<?php

use mycls\Validator;

$db = createDbConnection();

try {
    $fillable = ["login", "password", "email", "channel_name", "password_confirmation"];
    $data = load($fillable, $_POST);


    $validator = new Validator();
    $validation = $validator->validation($data, [
        'login' => [
            'required' => true,
            'min' => 7,
            'max' => 21,
            'preg' => "/^(?=(?:.*[A-Za-z]){5})[A-Za-z0-9]+$/",
            'unique' => "login"
        ],
        'password' => [
            'required' => true,
            'min' => 8,
            'max' => 50,
        ],
        'password_confirmation' => [
            'required' => true,
            'confirm' => $data['password']
        ],
        'email' => [
            'required' => true,
            'min2' => 8,
            'max' => 50,
            'email' => true,
            'unique' => "email"
        ],
        'channel_name' => [
            'required' => true,
            'min' => 8,
            'max' => 50,
            'preg' => "/^(?=(?:.*[A-Za-zА-Яа-яёЁ]){5})[A-Za-zА-Яа-яёЁ\d\s]+$/u"
        ],
    ]);
    $errorsData = $validator->getErrors();
    $_SESSION['register']['errors'] = $errorsData;
} catch (Exception $e) {
    die($e);
}

if (empty($errorsData)) {
    try {
        $db->query(
            "INSERT INTO users (login, channel_name, email, password_hash) VALUES (:login, :channel_name, :email, :password_hash)",
            [
                ':login' => $data['login'],
                ':channel_name' => $data['channel_name'],
                ':email' => $data['email'],
                ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
            ]
        );
        $_SESSION['register']['success'] = "Вы успешно зарегестрированы!";
        redirect('/login');
    } catch (PDOException $e) {
        $_SESSION['register']['error'] = "Server Error";
    }
}
else {
    fillOldvalue('register');
    redirect('/register');
}
