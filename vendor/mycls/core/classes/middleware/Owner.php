<?php

namespace mycls\middleware;

use mycls\Db;


class Owner
{

    public function handle()
    {
        $db = createDbConnection();

        $id = (int) $_GET['id'];
        if (isset($id) && !empty($id)) {
            $course = $db->query('SELECT * FROM courses WHERE id = :id', [':id' => $id])->fetch();

            if ($course['author_id'] != $_SESSION['user']['id']) {
                redirect('/create');
            }
        } else {
            redirect('/create');
        }
    }
}
