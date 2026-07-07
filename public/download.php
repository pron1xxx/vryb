<?php
// download_images.php
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Директория для сохранения изображений
$uploadDir = 'C:\OSPanel\home\vryb.local\public\uploads\images\\';

// Проверяем и создаем директории
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    echo "✓ Создана директория: $uploadDir\n";
}

// Массив изображений для скачивания
$images = [
    // Аватары пользователей
    'avatar1.jpg' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face',
    'avatar2.jpg' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face',
    'avatar3.jpg' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face',
    'avatar4.jpg' => 'https://images.unsplash.com/photo-1544725176-7c40e5a71c5e?w=150&h=150&fit=crop&crop=face',
    'avatar5.jpg' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face',
    
    // Преимущества для курсов
    'course1.jpg' => 'https://images.unsplash.com/photo-1627398242454-45a1465c2479?w=800&h=450&fit=crop',
    'course2.jpg' => 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?w=800&h=450&fit=crop',
    'course3.jpg' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=800&h=450&fit=crop',
    'course4.jpg' => 'https://images.unsplash.com/photo-1546054451-aa0ef10829c9?w=800&h=450&fit=crop',
    'course5.jpg' => 'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&h=450&fit=crop',
    'course6.jpg' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=800&h=450&fit=crop',
    'course7.jpg' => 'https://images.unsplash.com/photo-1534423861386-85a16f5d13fd?w=800&h=450&fit=crop',
    'course8.jpg' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=800&h=450&fit=crop',
    'course9.jpg' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=800&h=450&fit=crop',
    'course10.jpg' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=800&h=450&fit=crop',
    'course11.jpg' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=800&h=450&fit=crop',
    'course12.jpg' => 'https://images.unsplash.com/photo-1603126857599-f6e157fa2fe6?w=800&h=450&fit=crop',
    'course13.jpg' => 'https://images.unsplash.com/photo-1530026405189-7f5d0b47b7c6?w=800&h=450&fit=crop',
    'course14.jpg' => 'https://images.unsplash.com/photo-1462331940025-496dfbfc7564?w=800&h=450&fit=crop',
    'course15.jpg' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=800&h=450&fit=crop',
    'course16.jpg' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=800&h=450&fit=crop',
    'course17.jpg' => 'https://images.unsplash.com/photo-1589998059171-988d887df646?w=800&h=450&fit=crop',
    'course18.jpg' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=800&h=450&fit=crop',
    'course19.jpg' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=450&fit=crop',
    'course20.jpg' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=800&h=450&fit=crop',
    'course21.jpg' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&h=450&fit=crop',
    'course22.jpg' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800&h=450&fit=crop',
    'course23.jpg' => 'https://images.unsplash.com/photo-1545239351-ef35f43d514b?w=800&h=450&fit=crop',
    'course24.jpg' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=800&h=450&fit=crop',
    'course25.jpg' => 'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=800&h=450&fit=crop',
    
    // Превью для уроков
    'lesson1.jpg' => 'https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=600&h=338&fit=crop',
    'lesson2.jpg' => 'https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?w=600&h=338&fit=crop',
    'lesson3.jpg' => 'https://images.unsplash.com/photo-1542744095-fcf48d80b0fd?w=600&h=338&fit=crop',
    'lesson4.jpg' => 'https://images.unsplash.com/photo-1533750349088-cd871a92f312?w=600&h=338&fit=crop',
    'lesson5.jpg' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=600&h=338&fit=crop'
];

echo "========================================\n";
echo "СКАЧИВАНИЕ ИЗОБРАЖЕНИЙ\n";
echo "========================================\n";

$successCount = 0;
$errorCount = 0;

foreach ($images as $filename => $url) {
    $fullPath = $uploadDir . $filename;
    
    // Пропускаем если файл уже существует
    if (file_exists($fullPath)) {
        echo "✓ Файл уже существует: $filename\n";
        $successCount++;
        continue;
    }
    
    // Скачиваем изображение
    $imageData = @file_get_contents($url);
    
    if ($imageData === false) {
        echo "❌ Ошибка скачивания: $filename\n";
        $errorCount++;
        continue;
    }
    
    // Сохраняем файл
    if (file_put_contents($fullPath, $imageData) !== false) {
        echo "✓ Скачан: $filename\n";
        $successCount++;
    } else {
        echo "❌ Ошибка сохранения: $filename\n";
        $errorCount++;
    }
    
    // Небольшая задержка чтобы не нагружать сервер
    usleep(100000); // 100ms
}

echo "\n========================================\n";
echo "РЕЗУЛЬТАТЫ СКАЧИВАНИЯ:\n";
echo "Успешно: $successCount файлов\n";
echo "Ошибок: $errorCount файлов\n";

// Создаем тестовые файлы если какие-то не скачались
$testFiles = [
    'default-avatar.jpg' => imagecreate(150, 150),
    'default-course.jpg' => imagecreate(800, 450),
    'default-lesson.jpg' => imagecreate(600, 338)
];

foreach ($testFiles as $filename => $img) {
    $fullPath = $uploadDir . $filename;
    if (!file_exists($fullPath)) {
        // Создаем цвет фона
        $bgColor = imagecolorallocate($img, 51, 55, 66); // #333742
        $textColor = imagecolorallocate($img, 255, 255, 255);
        
        // Добавляем текст
        $text = str_replace(['.jpg', '-', '_'], ' ', $filename);
        $text = ucfirst($text);
        
        if ($filename == 'default-avatar.jpg') {
            imagestring($img, 5, 20, 60, 'Аватар', $textColor);
        } elseif ($filename == 'default-course.jpg') {
            imagestring($img, 5, 300, 200, 'Курс', $textColor);
        } else {
            imagestring($img, 5, 250, 150, 'Урок', $textColor);
        }
        
        imagejpeg($img, $fullPath, 80);
        imagedestroy($img);
        echo "✓ Создан тестовый файл: $filename\n";
    }
}

echo "\n✅ Все изображения готовы!\n";
echo "Путь к папке: $uploadDir\n";
?>