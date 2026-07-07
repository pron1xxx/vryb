<?php
require_once dirname(__DIR__) . '/vendor/mycls/core/classes/OpenRouterSummarizer.php';

use mycls\OpenRouterSummarizer;

try {
    $summarizer = new OpenRouterSummarizer();
    
    // Используем рефлексию для доступа к приватному методу
    $reflection = new ReflectionClass($summarizer);
    $method = $reflection->getMethod('callOpenRouter');
    $method->setAccessible(true);
    
    // Простой тестовый запрос
    $testData = [
        'model' => 'openrouter/free',
        'messages' => [
            [
                'role' => 'user',
                'content' => 'Say "test" in JSON'
            ]
        ],
        'max_tokens' => 10
    ];
    
    $result = $method->invoke($summarizer, $testData);
    
    echo "<h2>Результат:</h2>";
    echo "<pre>" . htmlspecialchars($result) . "</pre>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Ошибка:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    
    // Показываем лог
    $logFile = dirname(__DIR__) . '/logs/openrouter_errors.log';
    if (file_exists($logFile)) {
        echo "<h2>Последние записи в логе:</h2>";
        echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
    }
}