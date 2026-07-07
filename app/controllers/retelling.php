<?php
use mycls\GigaChatSummarizer;

header('Content-Type: application/json');

$fileId = $_POST['file_id'] ?? 0;
$summaryType = $_POST['summary_type'] ?? 'medium';

if (!$fileId) {
    echo json_encode(['success' => false, 'message' => 'Не указан файл']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Ошибка безопасности']);
    exit;
}

$db = createDbConnection();

try {
    $summarizer = new GigaChatSummarizer();
    
    $file = $db->query("SELECT original_name FROM files WHERE id = ?", [$fileId])->fetch(PDO::FETCH_ASSOC);
    if ($file && !$summarizer->isFormatSupported($file['original_name'])) {
        throw new Exception("Формат файла не поддерживается. Поддерживаются: PDF, DOCX, TXT");
    }
    
    $summary = $summarizer->summarizeFileById($fileId, $summaryType);
    
    echo json_encode([
        'success' => true,
        'summary' => $summary,
    ]);
    
} catch (Exception $e) {
    error_log("GigaChat Summarizer Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}