<?php

namespace mycls\middleware;

use mycls\Db;

class Public1
{

    public function handle()
    {
        $db = createDbConnection();

        $id = (int) $_GET['id'];
        if (isset($id) && !empty($id)) {
            $course = $db->query("SELECT * FROM courses WHERE id = :id", [':id' => $id])->fetch();
            if ($course != false) {
                function checkStatusUser($course, $status)
                {
                    if (isset($_SESSION['user']['id'])) {
                        if ($course['status'] == $status && $course['author_id'] != $_SESSION['user']['id']) {
                            if($_SESSION['user']['role'] != 'admin') {
                                require_once VIEWS . '/errors/noAccess.tpl.php';
                                die;
                            } 
                        }
                    }
                    else {
                        if ($course['status'] != 'public') {
                            redirect('/');
                        }
                    }
                }

                checkStatusUser($course, 'hidden');
                checkStatusUser($course, 'development');
                checkStatusUser($course, 'moderation');
            } else {
                redirect("/");
            }
        } else {
            redirect('/');
        }
    }
}
