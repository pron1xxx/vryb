<?php 

$db = createDbConnection();

if(isset($_SESSION['user']['id']) && isset($_POST['subscribed_user_id'])) {
   $db->query(
    "DELETE FROM subscribes WHERE user_id = :user_id AND subscribed_user_id = :subscribed_user_id",
    [":user_id" => $_SESSION['user']['id'], ":subscribed_user_id" => $_POST['subscribed_user_id']]
);
    $_SESSION['message'] = "Вы отписались от пользователя!";
    redirect("/channel/?id={$_POST['subscribed_user_id']}");
}
else {
    $_SESSION['message'] = "Ошибка отписки!";
    redirect("/channel/?id={$_POST['subscribed_user_id']}");
}