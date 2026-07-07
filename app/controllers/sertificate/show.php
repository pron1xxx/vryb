<?php 

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'] ?? '';
}
else {
    $id = 0;
}


$db = createDbConnection();

$sertificate_data = $db->query('SELECT s.*, c.course_name FROM serteficates s LEFT JOIN courses c ON s.course_id = c.id WHERE s.id = :id', [':id'=>$id])->fetch(PDO::FETCH_ASSOC);

if(!$sertificate_data) {
    require VIEWS . "/errors/404.tpl.php";
    exit(404);
}

$lessons = $db->query("SELECT COUNT(*) FROM lessons WHERE course_id = :course_id", [':course_id'=> $sertificate_data['course_id']])->fetch();

$title = 'Сертификат о прохождении курса';
require VIEWS . "/serteficates/sertificate.tpl.php";