<?php

namespace mycls\middleware;

class User
{
    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $db = createDbConnection();

        if (!empty($_SESSION['user']['id'] ?? '')) {
            checkBlock($db, "Ваш аккаунт заблокирован до ", $_SESSION['user']['id']);
        } else {
            redirect('/login');
        }
    }
}
