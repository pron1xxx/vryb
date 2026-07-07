<?php 

namespace mycls\middleware;

class Admin {

    public function handle() {
        if($_SESSION['user']['role'] != 'admin') {
            redirect('/');
        }
    }
}