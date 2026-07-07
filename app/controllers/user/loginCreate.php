<?php

$db = createDbConnection();

$errorsData = [];
$fillable = ['login', 'password'];
$data = load($fillable, $_POST);
$login = false;

$user = $db->query('SELECT * FROM users WHERE login = :login', [":login" => $data['login']]);
$result = $user->fetch();

if (!$result) {
    $errorsData['login'] = ['Пользователя с таким логином не существует'];
    echo 1;
} else {
    if (password_verify($data['password'], $result['password_hash'])) {
        checkBlock($db, "Ваш аккаунт заблокирован до ", $result['id']);
        $_SESSION['user'] = $result;
        redirect('/about');
    } else {
        $errorsData['password'] = ['Неправильный пароль'];
    }
}

$_SESSION['login']['errors'] = $errorsData;
fillOldvalue('login');
redirect('/login');
