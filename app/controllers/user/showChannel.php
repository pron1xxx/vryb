<?php

$db = createDbConnection();



$id = (int) $_GET['id'];

checkBlock($db, "Аккаунт пользователя заблокирован до ", $id);
$user_data = $db->query("SELECT * FROM users WHERE id = :id", [':id' => $id])->fetch();
$user_courses = $db->query("SELECT * FROM courses WHERE author_id = :id AND status = 'public'", [':id' => $id])->fetchAll();
if (isset($_SESSION['user'])) {
    $isSubscribed = $db->query(
        "SELECT EXISTS(SELECT 1 FROM subscribes WHERE user_id = :user_id AND subscribed_user_id = :subscribed_user_id) as is_subscribed",
        [':user_id' => $_SESSION['user']['id'], ':subscribed_user_id' => $id]
    )->fetch()['is_subscribed'] == 1;
}
else {
    $isSubscribed = false;
}

$title = "Страница канала";
require VIEWS . "/channels/channel.tpl.php";
