<?php 

namespace mycls\middleware;

class Guest {

    public function handle() {
        if (checkAuth()) {
            redirect('/profile');
        }
    }
}