<?php

/**
 * Скрипт для замены домена и видео ссылок в базе данных
 * 
 * Функции:
 * 1. Замена домена во всех URL (превью курсов, уроков, аватаров, файлов)
 * 2. Замена всех видео ссылок на одну (например, на единое тестовое видео)
 */

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Конфигурация базы данных
$config = [
    'host' => 'localhost',
    'dbname' => 'cu512526_vrybdata',
    'username' => 'cu512526_vrybdata',
    'password' => 'ieBa2pNsyU!'
];

// Функция для вывода сообщений
function logMessage($message, $type = 'info')
{
    $colors = [
        'info' => "\033[0;36m",
        'success' => "\033[0;32m",
        'warning' => "\033[0;33m",
        'error' => "\033[0;31m",
        'reset' => "\033[0m"
    ];

    $color = $colors[$type] ?? $colors['info'];

    if (php_sapi_name() === 'cli') {
        echo $color . $message . $colors['reset'] . PHP_EOL;
    } else {
        $html_color = str_replace(['[', ']', '0;'], '', $color);
        echo "<div style='color: {$html_color}; margin: 5px 0;'>{$message}</div>";
    }
}

/**
 * Функция для замены домена в URL
 */
function replaceDomainInUrls($pdo, $old_domain, $new_domain)
{
    $total_updated = 0;
    $results = [];

    // Таблицы и поля для обновления
    $tables = [
        'courses' => ['preview_url'],
        'lessons' => ['preview_url', 'video_url'],
        'users' => ['avatar_url'],
        'files' => ['file_url'],
        'courses_categories' => [], // Если есть URL в категориях
    ];

    foreach ($tables as $table => $columns) {
        foreach ($columns as $column) {
            try {
                // Проверяем существование колонки
                $check = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
                if ($check->rowCount() == 0) {
                    logMessage("⚠️ Колонка $column не найдена в таблице $table, пропускаем", 'warning');
                    continue;
                }

                $sql = "UPDATE `$table` 
                        SET `$column` = REPLACE(`$column`, :old_domain, :new_domain) 
                        WHERE `$column` LIKE :pattern";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':old_domain' => $old_domain,
                    ':new_domain' => $new_domain,
                    ':pattern' => '%' . $old_domain . '%'
                ]);

                $count = $stmt->rowCount();
                if ($count > 0) {
                    $results[] = "✅ Таблица $table, колонка $column: обновлено $count записей";
                    $total_updated += $count;
                } else {
                    $results[] = "ℹ️ Таблица $table, колонка $column: нет записей для обновления";
                }
            } catch (PDOException $e) {
                $results[] = "❌ Ошибка в таблице $table, колонка $column: " . $e->getMessage();
            }
        }
    }

    // Обновление URL в текстовых полях (описания)
    $text_tables = [
        'courses' => ['course_description'],
        'lessons' => ['description'],
        'users' => ['channel_description']
    ];

    foreach ($text_tables as $table => $columns) {
        foreach ($columns as $column) {
            try {
                $check = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
                if ($check->rowCount() == 0) continue;

                $sql = "UPDATE `$table` 
                        SET `$column` = REPLACE(`$column`, :old_domain, :new_domain) 
                        WHERE `$column` LIKE :pattern";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':old_domain' => $old_domain,
                    ':new_domain' => $new_domain,
                    ':pattern' => '%' . $old_domain . '%'
                ]);

                $count = $stmt->rowCount();
                if ($count > 0) {
                    $results[] = "✅ Таблица $table, колонка $column (текст): обновлено $count записей";
                    $total_updated += $count;
                }
            } catch (PDOException $e) {
                $results[] = "⚠️ Ошибка в таблице $table, колонка $column: " . $e->getMessage();
            }
        }
    }

    return [
        'total' => $total_updated,
        'results' => $results
    ];
}

/**
 * Функция для замены всех видео ссылок на одну
 */
function replaceVideoUrls($pdo, $new_video_url, $new_video_id = null, $new_playback_id = null)
{
    $total_updated = 0;
    $results = [];

    // Если не указан video_id, генерируем из URL
    if ($new_video_id === null) {
        $video_filename = basename(parse_url($new_video_url, PHP_URL_PATH));
        $new_video_id = 'local_' . pathinfo($video_filename, PATHINFO_FILENAME);
    }

    // Если не указан playback_id
    if ($new_playback_id === null) {
        $new_playback_id = 'local';
    }

    try {
        // 1. Обновляем video_url в таблице lessons
        $sql = "UPDATE `lessons` 
                SET `video_url` = :new_url,
                    `video_id` = :new_id,
                    `playback_id` = :new_playback_id
                WHERE `video_url` != :new_url OR `video_id` != :new_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':new_url' => $new_video_url,
            ':new_id' => $new_video_id,
            ':new_playback_id' => $new_playback_id
        ]);

        $count = $stmt->rowCount();
        $total_updated += $count;
        $results[] = "✅ Таблица lessons: обновлено $count записей видео";

        // 2. Проверяем и обновляем также в других таблицах, если есть видео ссылки
        $other_tables = [
            'files' => ['file_url'], // Если в файлах есть видео
        ];

        foreach ($other_tables as $table => $columns) {
            foreach ($columns as $column) {
                $check = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
                if ($check->rowCount() > 0) {
                    $sql = "UPDATE `$table` 
                            SET `$column` = REPLACE(`$column`, :old_video, :new_video) 
                            WHERE `$column` LIKE :pattern";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':old_video' => '%',
                        ':new_video' => $new_video_url,
                        ':pattern' => '%video%'
                    ]);

                    $count = $stmt->rowCount();
                    if ($count > 0) {
                        $results[] = "✅ Таблица $table, колонка $column: обновлено $count записей";
                        $total_updated += $count;
                    }
                }
            }
        }
    } catch (PDOException $e) {
        $results[] = "❌ Ошибка при замене видео: " . $e->getMessage();
        $total_updated = 0;
    }

    return [
        'total' => $total_updated,
        'results' => $results,
        'video_id' => $new_video_id,
        'playback_id' => $new_playback_id
    ];
}

/**
 * Функция для получения текущей статистики по URL
 */
function getUrlStats($pdo)
{
    $stats = [];

    // Статистика по доменам в превью курсов
    $stmt = $pdo->query("SELECT COUNT(*) as count, 
                                SUBSTRING_INDEX(preview_url, '/', 3) as domain 
                         FROM courses 
                         WHERE preview_url IS NOT NULL 
                         GROUP BY domain");
    $stats['courses_preview'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Статистика по видео
    $stmt = $pdo->query("SELECT COUNT(*) as count, video_url 
                         FROM lessons 
                         WHERE video_url IS NOT NULL 
                         GROUP BY video_url 
                         ORDER BY count DESC 
                         LIMIT 10");
    $stats['video_urls'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Общее количество записей с URL
    $stats['total_courses'] = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $stats['total_lessons'] = $pdo->query("SELECT COUNT(*) FROM lessons")->fetchColumn();
    $stats['total_files'] = $pdo->query("SELECT COUNT(*) FROM files")->fetchColumn();

    return $stats;
}

// Основная логика
try {
    // Подключение к БД
    logMessage("========================================", 'info');
    logMessage("ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ", 'info');
    logMessage("========================================", 'info');

    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    logMessage("✅ Успешное подключение к базе данных\n", 'success');

    // Проверяем метод запуска
    $is_cli = php_sapi_name() === 'cli';

    if ($is_cli) {
        // CLI режим
        logMessage("========================================", 'info');
        logMessage("ВЫБЕРИТЕ ДЕЙСТВИЕ:", 'info');
        logMessage("========================================", 'info');
        logMessage("1. Замена домена во всех фото", 'info');
        logMessage("2. Замена всех видео ссылок на одну", 'info');
        logMessage("3. Выполнить оба действия", 'info');
        logMessage("4. Показать текущую статистику", 'info');

        $choice = trim(fgets(STDIN));

        switch ($choice) {
            case '1':
                logMessage("\nВведите старый домен (например: https://vryb.local): ", 'info');
                $old_domain = trim(fgets(STDIN));
                logMessage("Введите новый домен (например: https://cu512526.tw1.ru): ", 'info');
                $new_domain = trim(fgets(STDIN));

                logMessage("\n========================================", 'info');
                logMessage("ЗАМЕНА ДОМЕНА", 'info');
                logMessage("========================================\n", 'info');

                $result = replaceDomainInUrls($pdo, $old_domain, $new_domain);

                foreach ($result['results'] as $res) {
                    logMessage($res);
                }
                logMessage("\n✅ Всего обновлено записей: " . $result['total'], 'success');
                break;

            case '2':
                logMessage("\nВведите URL нового видео (например: https://example.com/video.mp4): ", 'info');
                $new_video_url = trim(fgets(STDIN));

                logMessage("\n========================================", 'info');
                logMessage("ЗАМЕНА ВИДЕО ССЫЛОК", 'info');
                logMessage("========================================\n", 'info');

                $result = replaceVideoUrls($pdo, $new_video_url);

                foreach ($result['results'] as $res) {
                    logMessage($res);
                }
                logMessage("\n✅ Всего обновлено записей: " . $result['total'], 'success');
                break;

            case '3':
                logMessage("\nВведите старый домен: ", 'info');
                $old_domain = trim(fgets(STDIN));
                logMessage("Введите новый домен: ", 'info');
                $new_domain = trim(fgets(STDIN));
                logMessage("Введите URL нового видео: ", 'info');
                $new_video_url = trim(fgets(STDIN));

                logMessage("\n========================================", 'info');
                logMessage("ВЫПОЛНЕНИЕ ОБОИХ ДЕЙСТВИЙ", 'info');
                logMessage("========================================\n", 'info');

                // Замена домена
                logMessage("1. ЗАМЕНА ДОМЕНА", 'info');
                $domain_result = replaceDomainInUrls($pdo, $old_domain, $new_domain);
                foreach ($domain_result['results'] as $res) {
                    logMessage($res);
                }

                // Замена видео
                logMessage("\n2. ЗАМЕНА ВИДЕО ССЫЛОК", 'info');
                $video_result = replaceVideoUrls($pdo, $new_video_url);
                foreach ($video_result['results'] as $res) {
                    logMessage($res);
                }

                logMessage("\n✅ Всего обновлено: " . ($domain_result['total'] + $video_result['total']) . " записей", 'success');
                break;

            case '4':
                logMessage("\n========================================", 'info');
                logMessage("ТЕКУЩАЯ СТАТИСТИКА", 'info');
                logMessage("========================================\n", 'info');

                $stats = getUrlStats($pdo);

                logMessage("📊 ОБЩАЯ СТАТИСТИКА:", 'info');
                logMessage("  - Курсов: " . $stats['total_courses'], 'info');
                logMessage("  - Уроков: " . $stats['total_lessons'], 'info');
                logMessage("  - Файлов: " . $stats['total_files'], 'info');

                if (!empty($stats['courses_preview'])) {
                    logMessage("\n📸 Домены в превью курсов:", 'info');
                    foreach ($stats['courses_preview'] as $item) {
                        logMessage("  - {$item['domain']}: {$item['count']} записей", 'info');
                    }
                }

                if (!empty($stats['video_urls'])) {
                    logMessage("\n🎬 Используемые видео URL (топ 10):", 'info');
                    foreach ($stats['video_urls'] as $item) {
                        $url_preview = strlen($item['video_url']) > 60 ?
                            substr($item['video_url'], 0, 60) . '...' :
                            $item['video_url'];
                        logMessage("  - {$item['count']} раз: {$url_preview}", 'info');
                    }
                }
                break;

            default:
                logMessage("❌ Неверный выбор!", 'error');
                break;
        }
    } else {
        // Веб-режим - показываем форму
?>
        <!DOCTYPE html>
        <html lang="ru">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Управление ссылками в базе данных</title>
            <style>
                * {
                    box-sizing: border-box;
                }

                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
                    background: #f5f5f5;
                    margin: 0;
                    padding: 20px;
                }

                .container {
                    max-width: 900px;
                    margin: 0 auto;
                }

                .card {
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    margin-bottom: 20px;
                    overflow: hidden;
                }

                .card-header {
                    background: #4a5568;
                    color: white;
                    padding: 15px 20px;
                    font-size: 18px;
                    font-weight: bold;
                }

                .card-body {
                    padding: 20px;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: bold;
                    color: #2d3748;
                }

                input[type="text"],
                input[type="url"] {
                    width: 100%;
                    padding: 10px 12px;
                    border: 1px solid #cbd5e0;
                    border-radius: 6px;
                    font-size: 14px;
                    transition: border-color 0.2s;
                }

                input:focus {
                    outline: none;
                    border-color: #4299e1;
                    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
                }

                button {
                    background: #4299e1;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 6px;
                    font-size: 14px;
                    cursor: pointer;
                    transition: background 0.2s;
                }

                button:hover {
                    background: #3182ce;
                }

                .btn-danger {
                    background: #e53e3e;
                }

                .btn-danger:hover {
                    background: #c53030;
                }

                .btn-success {
                    background: #48bb78;
                }

                .btn-success:hover {
                    background: #38a169;
                }

                .btn-warning {
                    background: #ed8936;
                }

                .stats-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }

                .stats-table th,
                .stats-table td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #e2e8f0;
                }

                .stats-table th {
                    background: #f7fafc;
                    font-weight: bold;
                    color: #4a5568;
                }

                .alert {
                    padding: 12px 15px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                }

                .alert-warning {
                    background: #fff3cd;
                    border: 1px solid #ffc107;
                    color: #856404;
                }

                .alert-success {
                    background: #d4edda;
                    border: 1px solid #c3e6cb;
                    color: #155724;
                }

                .alert-danger {
                    background: #f8d7da;
                    border: 1px solid #f5c6cb;
                    color: #721c24;
                }

                .result-log {
                    background: #1a202c;
                    color: #a0aec0;
                    padding: 15px;
                    border-radius: 6px;
                    font-family: monospace;
                    font-size: 12px;
                    max-height: 400px;
                    overflow-y: auto;
                }

                .button-group {
                    display: flex;
                    gap: 10px;
                    margin-top: 20px;
                }

                hr {
                    margin: 20px 0;
                    border: none;
                    border-top: 1px solid #e2e8f0;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <h1 style="color: #2d3748; margin-bottom: 20px;">🛠️ Управление ссылками</h1>

                <?php
                // Обработка POST запроса
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $action = $_POST['action'] ?? '';
                    $result = null;

                    try {
                        if ($action === 'replace_domain') {
                            $old_domain = trim($_POST['old_domain']);
                            $new_domain = trim($_POST['new_domain']);

                            if (empty($old_domain) || empty($new_domain)) {
                                throw new Exception("Заполните оба поля домена");
                            }

                            $result = replaceDomainInUrls($pdo, $old_domain, $new_domain);
                            echo '<div class="alert alert-success">✅ Замена домена выполнена успешно!</div>';
                        } elseif ($action === 'replace_video') {
                            $new_video_url = trim($_POST['new_video_url']);
                            $new_video_id = trim($_POST['new_video_id'] ?? '');
                            $new_playback_id = trim($_POST['new_playback_id'] ?? '');

                            if (empty($new_video_url)) {
                                throw new Exception("Введите URL видео");
                            }

                            $result = replaceVideoUrls($pdo, $new_video_url, $new_video_id ?: null, $new_playback_id ?: null);
                            echo '<div class="alert alert-success">✅ Замена видео выполнена успешно!</div>';
                        } elseif ($action === 'both') {
                            $old_domain = trim($_POST['old_domain']);
                            $new_domain = trim($_POST['new_domain']);
                            $new_video_url = trim($_POST['new_video_url']);

                            if (empty($old_domain) || empty($new_domain)) {
                                throw new Exception("Заполните поля домена");
                            }
                            if (empty($new_video_url)) {
                                throw new Exception("Введите URL видео");
                            }

                            $domain_result = replaceDomainInUrls($pdo, $old_domain, $new_domain);
                            $video_result = replaceVideoUrls($pdo, $new_video_url);

                            echo '<div class="alert alert-success">✅ Оба действия выполнены успешно!</div>';
                            echo '<div class="alert alert-info">📊 Домены: обновлено ' . $domain_result['total'] . ' записей<br>🎬 Видео: обновлено ' . $video_result['total'] . ' записей</div>';
                        }

                        if ($result && isset($result['results'])) {
                            echo '<div class="result-log">';
                            foreach ($result['results'] as $line) {
                                echo htmlspecialchars($line) . '<br>';
                            }
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">❌ Ошибка: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                }

                // Показываем статистику
                $stats = getUrlStats($pdo);
                ?>

                <div class="card">
                    <div class="card-header">📊 Текущая статистика</div>
                    <div class="card-body">
                        <table class="stats-table">
                            <tr>
                                <th>Параметр</th>
                                <th>Значение</th>
                            </tr>
                            <tr>
                                <td>Всего курсов</td>
                                <td><?= $stats['total_courses'] ?></td>
                            </tr>
                            <tr>
                                <td>Всего уроков</td>
                                <td><?= $stats['total_lessons'] ?></td>
                            </tr>
                            <tr>
                                <td>Всего файлов</td>
                                <td><?= $stats['total_files'] ?></td>
                            </tr>
                        </table>

                        <?php if (!empty($stats['courses_preview'])): ?>
                            <h3 style="margin-top: 20px;">📸 Домены в превью курсов</h3>
                            <table class="stats-table">
                                <tr>
                                    <th>Домен</th>
                                    <th>Количество</th>
                                </tr>
                                <?php foreach ($stats['courses_preview'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['domain']) ?></td>
                                        <td><?= $item['count'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>

                        <?php if (!empty($stats['video_urls'])): ?>
                            <h3 style="margin-top: 20px;">🎬 Используемые видео URL</h3>
                            <table class="stats-table">
                                <tr>
                                    <th>Количество</th>
                                    <th>URL видео</th>
                                </tr>
                                <?php foreach ($stats['video_urls'] as $item): ?>
                                    <tr>
                                        <td><?= $item['count'] ?></td>
                                        <td style="font-family: monospace; font-size: 12px;"><?= htmlspecialchars($item['video_url']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">🔄 Замена домена во всех фото</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="replace_domain">
                            <div class="form-group">
                                <label>Старый домен</label>
                                <input type="text" name="old_domain" placeholder="https://vryb.local" required>
                            </div>
                            <div class="form-group">
                                <label>Новый домен</label>
                                <input type="text" name="new_domain" placeholder="https://cu512526.tw1.ru" required>
                            </div>
                            <button type="submit">Выполнить замену домена</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">🎬 Замена всех видео ссылок на одну</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="replace_video">
                            <div class="form-group">
                                <label>Новый URL видео</label>
                                <input type="url" name="new_video_url" placeholder="https://example.com/video.mp4" required>
                            </div>
                            <div class="form-group">
                                <label>Video ID (необязательно)</label>
                                <input type="text" name="new_video_id" placeholder="local_video_name">
                            </div>
                            <div class="form-group">
                                <label>Playback ID (необязательно)</label>
                                <input type="text" name="new_playback_id" placeholder="local">
                            </div>
                            <button type="submit">Выполнить замену видео</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">⚡ Выполнить оба действия</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="both">
                            <div class="form-group">
                                <label>Старый домен</label>
                                <input type="text" name="old_domain" placeholder="https://vryb.local" required>
                            </div>
                            <div class="form-group">
                                <label>Новый домен</label>
                                <input type="text" name="new_domain" placeholder="https://cu512526.tw1.ru" required>
                            </div>
                            <div class="form-group">
                                <label>Новый URL видео</label>
                                <input type="url" name="new_video_url" placeholder="https://example.com/video.mp4" required>
                            </div>
                            <div class="button-group">
                                <button type="submit" class="btn-success">Выполнить оба действия</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="alert alert-warning">
                    ⚠️ <strong>Внимание!</strong> Перед выполнением операций обязательно сделайте резервную копию базы данных!
                </div>
            </div>
        </body>

        </html>
<?php
    }
} catch (PDOException $e) {
    logMessage("❌ Ошибка подключения к базе данных: " . $e->getMessage(), 'error');
    if (php_sapi_name() === 'cli') {
        exit(1);
    } else {
        echo "<div class='alert alert-danger'>❌ Ошибка подключения: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>