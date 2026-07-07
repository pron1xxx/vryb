<?php

define('MIDDLEWARES', [
    'guest'=> mycls\middleware\Guest::class,
    'user'=> mycls\middleware\User::class,
    'owner'=> mycls\middleware\Owner::class,
    'noUser'=> mycls\middleware\NoUser::class,
    'public'=> mycls\middleware\Public1::class,
    'admin'=> mycls\middleware\Admin::class,
    'checkCSRF'=> mycls\middleware\CheckCSRF::class
]);


// user
$router->get('register', 'user/registerShow.php')->only('guest');
$router->post('register', 'user/registerCreate.php')->only('guest');
$router->get('login', 'user/loginShow.php')->only('guest');
$router->post('login', 'user/loginCreate.php')->only('guest');
$router->get('logout', 'user/logout.php')->only('user');
$router->get('profile/edit', 'user/edit.php')->only('user');
$router->post('profile/edit', 'user/edit.php')->only('user');


// channels
$router->get('profile', 'user/show.php')->only('user');
$router->get('profile/stats', 'user/showProfileStats.php')->only('user');
$router->get('channel', 'user/showChannel.php')->only('user')->only('noUser');
$router->post('subscribed', 'user/subscribed.php')->only('user');
$router->post('unsubscribed', 'user/unsubscribed.php')->only('user');


// course
$router->get('create', 'course/showCreate.php')->only('user');
$router->post('create', 'course/create.php')->only('user');
$router->get('course/edit', 'course/showEdit.php')->only('user')->only('owner');
$router->post('course/edit', 'course/edit.php')->only('user')->only('owner');
$router->get('course/show', 'course/show.php')->only('public');
$router->post('course/save', 'course/save.php')->only('user');

//lesson 
$router->get('lesson/create', 'lesson/showCreate.php')->only('user')->only('owner');
$router->post('lesson/create', 'lesson/create.php')->only('user')->only('owner');
$router->get('lesson/show', 'lesson/show.php');
$router->get('lesson/edit', 'lesson/edit.php')->only('user');
$router->post('lesson/edit', 'lesson/edit.php')->only('user');
$router->post('lesson/complete', 'lesson/complete.php')->only('user')->only('checkCSRF');

//test 
$router->post('test/complete', 'lesson/testComplete.php')->only('user');
$router->get('test/result', 'lesson/testResult.php')->only('user');


// other
$router->get('saved', 'showSaved.php')->only('user');
$router->get('about', 'showIndex.php');
$router->post('search', 'search.php')->only('checkCSRF');
$router->post('retelling', 'retelling.php')->only('checkCSRF')->only('user');

//serteficate
$router->get('serteficate', 'sertificate/show.php');
$router->get('serteficates/search', 'sertificate/showSearch.php');
$router->get('serteficates', 'sertificate/user_serteficates.php')->only('user');
$router->post('serteficate/get', 'sertificate/get.php')->only('user')->only('checkCSRF');
$router->post('serteficates/search', 'sertificate/search.php')->only('checkCSRF');

//files 
$router->post('file/delete', 'files/deleteFile.php')->only('checkCSRF');

//main
$router->get('', 'main/showMain.php');
$router->post('main/show/more', 'main/showMore.php')->only('checkCSRF');

// admin
$router->get('admin', 'admin/dashboard.php')->only('user')->only('admin');
$router->post('admin/change/status', 'admin/changeStatus.php')->only('user')->only('admin')->only('checkCSRF');
$router->post('admin/block', 'admin/block.php')->only('user')->only('admin')->only('checkCSRF');
$router->post('admin/unblock', 'admin/unblock.php')->only('user')->only('admin')->only('checkCSRF');


// script generate 
$router->get('generate', 'generate.php');
$router->post('generate', 'generate.php');
$router->math();
