<?php

use mycls\Validator;

// для вывода информации на страницах
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

// получение и trim только нужных полей в массив
function load($fillable, $dataArray)
{
    foreach ($dataArray as $key => $value) {
        if (in_array($key, $fillable)) {
            $data[$key] = trim($value);
        }
    }
    return $data;
}

function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function redirect($path)
{
    header("location: $path");
    die;
}

function showFlashAlert($fieldname)
{
    if (!empty($_SESSION[$fieldname]['success'])) {
        $alertText = $_SESSION[$fieldname]['success'];
        include VIEWS . '/incs/successAlert.tpl.php';
        unset($_SESSION[$fieldname]['success']);
    }

    if (!empty($_SESSION[$fieldname]['error'])) {
        $alertText = $_SESSION[$fieldname]['error'];
        include VIEWS . '/incs/errorAlert.tpl.php';
        unset($_SESSION[$fieldname]['error']);
    }
}

function fillOldvalue($pageName)
{
    foreach ($_POST as $fieldname => $data) {
        $_SESSION['old'][$pageName][$fieldname] = $data;
    }
}


function getOldValue($pageName, $fieldname)
{
    if (isset($_SESSION['old'][$pageName][$fieldname])) {
        $old_data = $_SESSION['old'][$pageName][$fieldname];
        unset($_SESSION['old'][$pageName][$fieldname]);
        return $old_data;
    }
}

use mycls\Db;

function createDbConnection()
{
    $db_config = require CONFIG . '/db.php';
    $db = \mycls\Db::getInstance();
    $db->getConnection($db_config);
    return $db;
}

function checkAuth()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['user']);
}

function normalizeFilesArray($filesArray)
{
    $result = [];

    // Проверяем, что массив файлов существует и имеет правильную структуру
    if (empty($filesArray) || !isset($filesArray['name']) || !is_array($filesArray['name'])) {
        return $result;
    }

    $fileCount = count($filesArray['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        // Получаем текущий error код
        $errorCode = $filesArray['error'][$i] ?? UPLOAD_ERR_NO_FILE;

        // Пропускаем файлы, которые не были загружены
        if ($errorCode == UPLOAD_ERR_NO_FILE) {
            continue;
        }

        // Также пропускаем пустые имена файлов
        if (empty($filesArray['name'][$i])) {
            continue;
        }

        $result["file_$i"] = [
            'name' => $filesArray['name'][$i],
            'type' => $filesArray['type'][$i] ?? '',
            'tmp_name' => $filesArray['tmp_name'][$i] ?? '',
            'error' => $errorCode,
            'size' => $filesArray['size'][$i] ?? 0
        ];
    }

    return $result;
}

function validateFiles($filesArray, $type = 'lecture')
{
    $errors = [];

    $validationRules = [
        'lecture' => [
            'maxSize' => 10 * 1024 * 1024,
            'allowedTypes' => [
                'application/pdf',
                'application/x-pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/zip',
                'application/x-zip-compressed',
                'text/plain',
                'text/csv'
            ],
            'allowedExtensions' => [
                'pdf',
                'doc',
                'docx',
                'xlsx',
                'pptx',
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp',
                'zip',
                'txt',
                'csv'
            ]
        ],
        'practice' => [
            'maxSize' => 10 * 1024 * 1024,
            'allowedTypes' => [
                'application/pdf',
                'application/x-pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/zip',
                'application/x-zip-compressed',
                'text/plain',
                'text/csv',
                'audio/mpeg',
                'audio/wav',
                'audio/ogg',
                'application/x-php',
                'application/javascript',
                'text/html',
                'text/css',
                'application/json'
            ],
            'allowedExtensions' => [
                'pdf',
                'doc',
                'docx',
                'xlsx',
                'pptx',
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp',
                'zip',
                'txt',
                'csv',
                'mp3',
                'wav',
                'ogg',
                'php',
                'js',
                'html',
                'css',
                'json'
            ]
        ]
    ];

    $rules = $validationRules[$type] ?? $validationRules['lecture'];

    foreach ($filesArray as $key => $file) {
        $fileValidator = new Validator();

        $validation = ['file' => $file];

        $fileValidator->validation($validation, [
            'file' => [
                'uploadErrors' => true,
                'maxSize' => $rules['maxSize'],
                'allowedTypes' => $rules['allowedTypes'],
                'allowedExtensions' => $rules['allowedExtensions']
            ]
        ]);

        $fileErrors = $fileValidator->getErrors();

        if (!empty($fileErrors)) {
            $errors[$key] = $fileErrors;
        }
    }

    return $errors;
}

function checkCount($lessonFiles, $count)
{
    $checkArray['files'] = $lessonFiles;
    $validator = new Validator();
    $validator->validation(
        $checkArray,
        [
            'files' => [
                'maxLength' => $count
            ]
        ]
    );

    return $validator->getErrors();
}

function getFileIconClass($extension): string
{
    $extension = strtolower($extension);

    $icons = [
        'pdf' => 'pdf-icon',
        'doc' => 'doc-icon',
        'docx' => 'doc-icon',
        'xls' => 'doc-icon',
        'xlsx' => 'doc-icon',
        'ppt' => 'doc-icon',
        'pptx' => 'doc-icon',
        'jpg' => 'image-icon',
        'jpeg' => 'image-icon',
        'png' => 'image-icon',
        'gif' => 'image-icon',
        'svg' => 'image-icon',
        'webp' => 'image-icon',
        'zip' => 'archive-icon',
        'rar' => 'archive-icon',
        '7z' => 'archive-icon',
        'tar' => 'archive-icon',
        'mp4' => 'video-icon',
        'avi' => 'video-icon',
        'mov' => 'video-icon',
        'mkv' => 'video-icon',
        'mp3' => 'audio-icon',
        'wav' => 'audio-icon',
        'ogg' => 'audio-icon',
        'php' => 'code-icon',
        'js' => 'code-icon',
        'html' => 'code-icon',
        'css' => 'code-icon',
        'py' => 'code-icon',
        'json' => 'code-icon',
    ];

    return $icons[$extension] ?? 'other-icon';
}

function getFileEmoji($extension): string
{
    $extension = strtolower($extension);

    $emojis = [
        'pdf' => '📄',
        'doc' => '📝',
        'docx' => '📝',
        'xls' => '📊',
        'xlsx' => '📊',
        'ppt' => '📽️',
        'pptx' => '📽️',
        'jpg' => '🖼️',
        'jpeg' => '🖼️',
        'png' => '🖼️',
        'zip' => '📦',
        'rar' => '📦',
        '7z' => '📦',
        'mp4' => '🎬',
        'avi' => '🎬',
        'mov' => '🎬',
        'mp3' => '🎵',
        'wav' => '🎵',
        'ogg' => '🎵',
        'php' => '🐘',
        'js' => '📜',
        'html' => '🌐',
        'css' => '🎨',
        'py' => '🐍',
    ];

    return $emojis[$extension] ?? '📎';
}

function formatFileSize($bytes): string
{
    if ($bytes == 0) return '0 Б';

    $units = ['Б', 'КБ', 'МБ', 'ГБ'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);

    return round($bytes, 2) . ' ' . $units[$pow];
}

function canPreviewFile($extension): bool
{
    $previewable = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt', 'html', 'md'];
    return in_array(strtolower($extension), $previewable);
}


function saveFiles($fileArray, $filesDir)
{
    $savedFiles = [];

    foreach ($fileArray as $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $originalName = $file['name'];
            $tmpName = $file['tmp_name'];

            $fileFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $originalName);
            $fileUploadPath = $filesDir . $fileFileName;

            if (move_uploaded_file($tmpName, $fileUploadPath)) {
                $savedFiles[] = [
                    'url' => 'https://' . $_SERVER['HTTP_HOST'] . '/public/uploads/files/' . $fileFileName,
                    'extension' => strtolower(pathinfo($originalName, PATHINFO_EXTENSION)),
                    'size' => $file['size'],
                    'name' => $originalName
                ];
            }
        }
    }
    return $savedFiles;
}

function getStatusColor($status)
{
    switch ($status) {
        case 'public':
            return '#28a745';
        case 'moderation':
            return '#ffc107';
        case 'development':
            return '#17a2b8';
        case 'hidden':
            return '#6c757d';
        default:
            return '#6c757d';
    }
}

function checkBlock($db, $ban_str, $user_id)
{
    $hasUserBan = $db->query('SELECT * FROM blocked_users WHERE user_id = :user_id', [':user_id' => $user_id])->fetch(\PDO::FETCH_ASSOC);

    if ($hasUserBan) {
        $ban_error = $ban_str . $hasUserBan['banned_before'];
        require_once VIEWS . '/errors/banned.tpl.php';
        die;
    }
}
