<?php

namespace mycls\middleware;

use mycls\Db;


class NoUser
{

    public function handle()
    {
        $db = createDbConnection();

        $id = (int) $_GET['id'];
        if (isset($id) && !empty($id)) {
            $user = $db->query('SELECT * FROM users WHERE id = :id', [':id' => $id])->fetch();

            if($user == false) {
                require_once VIEWS . '/errors/404.tpl.php';
                die;
            }
            
            if(isset($_SESSION['user']) && $user['id'] == $_SESSION['user']['id']) {
                redirect('/profile');
            }
        } else {
            redirect('/profile');
        }
    }
}
