<?php

namespace mycls\middleware;

class CheckCSRF
{
    public function handle()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Проверяем либо POST, либо JSON
        $token = $_POST['csrf_token'] ?? ($input['csrf_token'] ?? null);
        
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка безопасности'
            ]);
            exit;
        }
    }
}