<?php
// Устанавливаем заголовок для JSON ответа
header('Content-Type: application/json');

$db = createDbConnection();
$id = (int) $_POST['file_id'];
$fileData = $db->query('SELECT * FROM files WHERE id = :id', [':id' => $id])->fetch();

$parts = explode('/', $fileData['file_url'])[6];
$file_path = PUBLICF . '/uploads/files/' . $parts;


if (!$fileData) {
    echo json_encode([
        'success' => false,
        'message' => 'Файл несуществует'
    ]);
    exit;
} else {
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo json_encode([
                'success' => true,
                'message' => 'Файл успешно удален'
            ]);
            $db->query('DELETE FROM files WHERE id = :id', [':id' => $id]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка при удалении файла'
            ]);
        }
    } else {
        echo json_encode([
                'success' => false,
                'message' => 'Ошибка при удалении файла'
            ]);
    }
}
