<?php 

namespace mycls;

class ImgBBUploader
{
    private $uploadPath;
    
    public function __construct($uploadPath = null)
    {
        // Указываем абсолютный путь к папке public
        if ($uploadPath === null) {
            $this->uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/images/';
        } else {
            $this->uploadPath = $uploadPath;
        }
        
        // Создаем директорию если не существует
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
        
        // Проверяем права на запись
        if (!is_writable($this->uploadPath)) {
            throw new \Exception("Upload directory is not writable: " . $this->uploadPath);
        }
    }
    
    public function uploadImage($filePath, $originalExtension = null)
    {
        // Проверяем существование файла
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }
        
        // Определяем расширение файла
        if ($originalExtension) {
            // Если расширение передано явно
            $extension = strtolower(ltrim($originalExtension, '.'));
        } else {
            // Пытаемся определить MIME-тип для определения расширения
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            
            $extension = $this->getExtensionFromMimeType($mimeType);
        }
        
        // Генерируем уникальное имя файла
        $fileName = uniqid() . '.' . $extension;
        $destination = $this->uploadPath . $fileName;
        
        // Копируем файл в целевую директорию
        if (!copy($filePath, $destination)) {
            throw new \Exception("Failed to copy file to: " . $destination);
        }
        
        // Проверяем, что файл действительно сохранился
        if (!file_exists($destination)) {
            throw new \Exception("File was not saved to: " . $destination);
        }
        
        // Полный URL к файлу
        $baseUrl = $this->getBaseUrl();
        $fileUrl = $baseUrl . '/public/uploads/images/' . $fileName;
        
        return [
            'url' => $fileUrl,
            'thumb' => $fileUrl,
            'size' => filesize($destination),
        ];
    }
    
    private function getExtensionFromMimeType($mimeType)
    {
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp' => 'bmp',
        ];
        
        return $mimeToExt[$mimeType] ?? 'jpg'; // По умолчанию jpg
    }
    
    private function getBaseUrl()
    {
        // Определяем базовый URL автоматически
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                    || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'];
        
        return $protocol . $domain;
    }
    
    // Дополнительный метод для удаления файла
    public function deleteImage($fileName)
    {
        $filePath = $this->uploadPath . $fileName;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
}