<?php 

$db = createDbConnection();

if(isset($_SESSION['user']['id']) && isset($_POST['subscribed_user_id'])) {
    $db->query("INSERT INTO subscribes (user_id, subscribed_user_id) VALUES (:user_id, :subscribed_user_id)",[":user_id"=>$_SESSION['user']['id'], ":subscribed_user_id"=>$_POST['subscribed_user_id']]);
    $_SESSION['message'] = "Подписка оформлена!";
    redirect("/channel/?id={$_POST['subscribed_user_id']}");
}
else {
    $_SESSION['message'] = "Ошибка подписки!";
    redirect("/channel/?id={$_POST['subscribed_user_id']}");
}
