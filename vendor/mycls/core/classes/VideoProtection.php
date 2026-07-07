<?php
namespace mycls;

class VideoProtection {
    public static function validateRequest($filename, $token) {
        error_log("=== VIDEO PROTECTION ===");
        error_log("Filename: $filename");
        error_log("Token: " . substr($token, 0, 16) . "...");
        error_log("Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'NULL'));
        error_log("User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'NULL'));
        error_log("Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'NULL'));
        
        // 1. Проверка источника запроса
        if (!self::checkRequestSource()) {
            error_log("❌ Request source check FAILED");
            return false;
        }
        
        // 2. Проверка токена
        if (!self::checkToken($filename, $token)) {
            error_log("❌ Token check FAILED");
            return false;
        }
        
        // 3. Проверка User-Agent
        if (!self::checkUserAgent()) {
            error_log("❌ User-Agent check FAILED");
            return false;
        }
        
        // 4. Проверка сессии
        if (!self::checkSession()) {
            error_log("❌ Session check FAILED");
            return false;
        }
        
        error_log("✅ All checks PASSED");
        return true;
    }
    
    private static function checkRequestSource() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'];
        
        error_log("Referer analysis: $referer");
        
        // Разрешаем пустой Referer для Range запросов (перемотка)
        // Но только если это не первый запрос
        $isRangeRequest = isset($_SERVER['HTTP_RANGE']);
        
        if (empty($referer)) {
            if ($isRangeRequest) {
                error_log("Empty referer but Range request - allowing for seeking");
                return true;
            } else {
                error_log("Empty referer - likely direct link");
                return false;
            }
        }
        
        // Проверяем что Referer с нашего домена
        if (strpos($referer, $host) === false) {
            error_log("Referer not from our domain");
            return false;
        }
        
        return true;
    }
    
    private static function checkToken($filename, $token) {
        if (!isset($_SESSION['video_tokens']) || !is_array($_SESSION['video_tokens'])) {
            error_log("Session video_tokens not set");
            return false;
        }
        
        $tokenData = $_SESSION['video_tokens'][$filename] ?? [];
        
        if (empty($tokenData) || !is_array($tokenData)) {
            error_log("No token data for: $filename");
            return false;
        }
        
        if (!isset($tokenData['token']) || $tokenData['token'] !== $token) {
            error_log("Token mismatch");
            return false;
        }
        
        // Проверяем время жизни (30 минут)
        if (time() - $tokenData['created'] > 1800) {
            error_log("Token expired");
            unset($_SESSION['video_tokens'][$filename]);
            return false;
        }
        
        // УВЕЛИЧИВАЕМ лимит запросов для перемотки
        if (!isset($tokenData['request_count'])) {
            $_SESSION['video_tokens'][$filename]['request_count'] = 1;
        } else {
            $_SESSION['video_tokens'][$filename]['request_count']++;
        }
        
        // 50 запросов максимум (вместо 3) для поддержки перемотки
        if ($_SESSION['video_tokens'][$filename]['request_count'] > 50) {
            error_log("Token used too many times: " . $_SESSION['video_tokens'][$filename]['request_count']);
            return false;
        }
        
        error_log("Request count: " . $_SESSION['video_tokens'][$filename]['request_count']);
        return true;
    }
    
    private static function checkUserAgent() {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Должен быть браузер
        if (empty($ua)) {
            error_log("Empty User-Agent");
            return false;
        }
        
        // Блокируем явных ботов и утилиты
        $blocked = [
            '/curl/i', '/wget/i', '/python/i', '/java/i',
            '/postman/i', '/insomnia/i', '/httpclient/i',
            '/bot/i', '/crawl/i', '/scrapy/i', '/fetch/i'
        ];
        
        foreach ($blocked as $pattern) {
            if (preg_match($pattern, $ua)) {
                error_log("Blocked User-Agent: $ua");
                return false;
            }
        }
        
        return true;
    }
    
    private static function checkSession() {
        // Проверяем что пользователь авторизован
        if (!isset($_SESSION['user']['id'])) {
            error_log("User not authenticated");
            return false;
        }
        
        return true;
    }
    
    public static function generateToken($filename) {
        if (!isset($_SESSION['video_tokens']) || !is_array($_SESSION['video_tokens'])) {
            $_SESSION['video_tokens'] = [];
        }
        
        $token = bin2hex(random_bytes(32));
        
        $_SESSION['video_tokens'][$filename] = [
            'token' => $token,
            'created' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_id' => $_SESSION['user']['id'] ?? null,
            'request_count' => 0
        ];
        
        error_log("Generated token for: $filename, user: " . ($_SESSION['user']['id'] ?? 'unknown'));
        return $token;
    }
    
    public static function markTokenAsUsed($filename) {
        if (isset($_SESSION['video_tokens'][$filename])) {
            $_SESSION['video_tokens'][$filename]['used'] = true;
            error_log("Token marked as used: $filename");
        }
    }
    
    // Очистка старых токенов
    public static function cleanupTokens() {
        if (!isset($_SESSION['video_tokens']) || !is_array($_SESSION['video_tokens'])) {
            return;
        }
        
        $now = time();
        foreach ($_SESSION['video_tokens'] as $filename => $tokenData) {
            if ($now - $tokenData['created'] > 1800) { // 30 минут
                unset($_SESSION['video_tokens'][$filename]);
                error_log("Cleaned up expired token: $filename");
            }
        }
    }
    
    // Сброс счетчика запросов (можно вызывать периодически)
    public static function resetRequestCounters() {
        if (!isset($_SESSION['video_tokens']) || !is_array($_SESSION['video_tokens'])) {
            return;
        }
        
        foreach ($_SESSION['video_tokens'] as $filename => $tokenData) {
            $_SESSION['video_tokens'][$filename]['request_count'] = 0;
        }
        
        error_log("Reset all request counters");
    }
}