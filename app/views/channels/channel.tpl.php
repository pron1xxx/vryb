<?php require VIEWS . '/incs/headers/mainheader.tpl.php'; ?>
<?php
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<?php if (isset($message)): ?>
    <div class="message">
        <h1 class="message__title"> Сообщение: </h1>
        <p class="message__text"> <?= $message ?> </p>
    </div>
<?php endif; ?>

<section class="channel-header">
    <div class="channel-header__container">
        <div class="channel-profile">
            <div class="channel-profile__avatar">
                <img src="<?= h($user_data['avatar_url']) ?>" alt="Аватар канала" class="channel-avatar__image">
            </div>
            <div class="channel-profile__info">
                <h1 class="channel-profile__name"><?= h($user_data['channel_name']) ?></h1>
                <p class="channel-profile__username">@<?= h($user_data['id']) ?></p>
            </div>
        </div>
        <?php if ($isSubscribed): ?>
            <form action="/unsubscribed" method="post"><input type="text" name="subscribed_user_id" value="<?= (int) $_GET['id'] ?>" class="hidden"><button class="channel-header__button subscribed">Отписаться</button></form>
        <?php else: ?>
            <form action="/subscribed" method="post"><input type="text" name="subscribed_user_id" value="<?= (int) $_GET['id'] ?>" class="hidden"><button class="channel-header__button" <?php if(!isset($_SESSION['user'])): ?> disabled='true' style="background: grey" <?php endif; ?>>Подписаться</button></form>
        <?php endif; ?>
    </div>
    <p class="channel-header__subtitle">
        <?= h($user_data['channel_description']) ?>
    </p>
</section>

<section class="profile-content">
    <?php if (empty($user_courses)): ?>
        <?php include VIEWS . '/errors/smile_error.tpl.php';
        showerror('=(', 'Пока нет курсов');
        ?>
    <?php else: ?>
        <h2 class="profile-content__title"> Курсы </h2>
        <div class="courses" id="courses">
            <?php foreach ($user_courses as $course): ?>
                <div class="course" data-category="games">
                    <a href="/course/show/?id=<?= $course['id'] ?>"><div class="course__preview"> <img src="<?= htmlspecialchars($course['preview_url']) ?>" alt="course_preview"> </div></a>
                    <h2 class="course__title"> <?= htmlspecialchars($course['course_name']) ?></h2>
                    <div class="course__authorWrapper">
                        <a href="" class="course__author"> <?= htmlspecialchars($user_data['channel_name']) ?> </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>


<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>