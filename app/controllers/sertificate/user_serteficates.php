<?php 

$db = createDbConnection();

$user_id = $_SESSION['user']['id'];

$certificates = $db->query(
    "SELECT s.*, c.course_name, c.course_description 
     FROM serteficates s 
     LEFT JOIN courses c ON s.course_id = c.id 
     WHERE s.user_id = :user_id",
    [':user_id' => $user_id]
)->fetchAll(PDO::FETCH_ASSOC);

$total_certificates = count($certificates);

$title = 'Ваши сертификаты';
require VIEWS . "/serteficates/user_sertificate.tpl.php";