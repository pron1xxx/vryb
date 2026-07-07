<?php
// /vendor/mycls/classes/GigaChatSummarizer.php

namespace mycls;

class GigaChatSummarizer {
    private $authKey;
    private $apiUrl = 'https://gigachat.devices.sberbank.ru/api/v1';
    private $siteUrl = 'https://vryb.local';
    private $projectRoot;
    private $clientId; 
    
    public function __construct() {
        $this->authKey = "MDE5Y2U4YzgtZmFmMy03ODE5LWE3NzktM2MyMzRiNmM1YWFlOjUzOTRiMzJhLTM2ZWYtNDIxZi1hMmI1LTI0NTIyOTFkNjMwYQ==";
        $this->clientId = "MDE5Y2U4YzgtZmFmMy03ODE5LWE3NzktM2MyMzRiNmM1YWFl";
        $this->projectRoot = dirname(__DIR__, 4);
    }
    
    private function getToken() {
        $rquid = $this->generateUuidV4();
        
        $ch = curl_init('https://ngw.devices.sberbank.ru:9443/api/v2/oauth');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'scope=GIGACHAT_API_PERS');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $this->authKey,
            'RqUID: ' . $rquid,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Ошибка соединения с GigaChat: $error");
        }
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception("Ошибка авторизации GigaChat. HTTP код: $httpCode");
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['access_token'])) {
            throw new \Exception("Ответ не содержит access_token");
        }
        
        return $data['access_token'];
    }
    
    private function generateUuidV4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    private function uploadFileToGigaChat($filepath, $filename, $token) {
        $fileSize = filesize($filepath);
        $maxSize = 40 * 1024 * 1024;
        
        if ($fileSize > $maxSize) {
            throw new \Exception("Файл слишком большой (макс. 40 МБ)");
        }
        
        $ch = curl_init($this->apiUrl . '/files');
        $postFields = [
            'file' => new \CURLFile($filepath, $this->getMimeType($filename), $filename),
            'purpose' => 'general'
        ];
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'X-Client-ID: ' . $this->clientId
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Ошибка загрузки файла: $error");
        }
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception("Ошибка загрузки файла. HTTP код: $httpCode");
        }
        
        $result = json_decode($response, true);
        return $result['id'];
    }
    
    public function summarizeFileById($fileId, $summaryType = 'medium') {
        try {
            $token = $this->getToken();
            
            $db = createDbConnection();
            $file = $db->query("SELECT * FROM files WHERE id = ?", [$fileId])->fetch(\PDO::FETCH_ASSOC);
            
            if (!$file) {
                throw new \Exception("Файл не найден");
            }
            
            $localPath = $this->convertUrlToPath($file['file_url']);
            
            if (!file_exists($localPath)) {
                throw new \Exception("Файл не найден на сервере: $localPath");
            }
            
            $gigaFileId = $this->uploadFileToGigaChat($localPath, $file['original_name'], $token);
            
            $prompts = [
                'short' => 'Сделай очень краткий пересказ этого документа в 2-3 предложения на русском языке.',
                'medium' => 'Сделай краткий пересказ этого документа на русском языке. Выдели основные мысли, ключевые термины и выводы. 3-5 предложений.',
                'detailed' => 'Сделай подробный пересказ этого документа на русском языке. Опиши структуру, основные разделы, важные концепции и примеры.'
            ];
            
            $prompt = $prompts[$summaryType] ?? $prompts['medium'];
            
            $data = [
                'model' => 'GigaChat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Ты помощник для студентов. Отвечай только текстом пересказа, без лишних комментариев.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                        'attachments' => [$gigaFileId]
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 1000
            ];
            
            $ch = curl_init($this->apiUrl . '/chat/completions');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
                'X-Client-ID: ' . $this->clientId
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_error($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("Ошибка соединения: $error");
            }
            curl_close($ch);
            
            if ($httpCode !== 200) {
                throw new \Exception("Ошибка API GigaChat. HTTP код: $httpCode");
            }
            
            $result = json_decode($response, true);
            return $result['choices'][0]['message']['content'] ?? 'Не удалось получить пересказ';
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    private function deleteFileFromGigaChat($fileId, $token) {
        try {
            $ch = curl_init($this->apiUrl . '/files/' . $fileId . '/delete');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'X-Client-ID: ' . $this->clientId
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            // Игнорируем ошибки удаления
        }
    }
    
    private function convertUrlToPath($url) {
        $relativePath = str_replace($this->siteUrl, '', $url);
        
        if ($relativePath === $url) {
            $parsed = parse_url($url);
            $relativePath = $parsed['path'] ?? '';
        }
        
        return $this->projectRoot . $relativePath;
    }
    
    private function getMimeType($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $types = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];
        return $types[$ext] ?? 'application/octet-stream';
    }
    
    public function isFormatSupported($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['pdf', 'docx', 'txt', 'jpg', 'jpeg', 'png']);
    }
    
    public function testConnection() {
        try {
            $token = $this->getToken();
            return "✅ Токен получен успешно!";
        } catch (\Exception $e) {
            return "❌ Ошибка: " . $e->getMessage();
        }
    }
}