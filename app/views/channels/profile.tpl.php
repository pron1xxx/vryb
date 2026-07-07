<?php require VIEWS . '/incs/headers/mainheader.tpl.php'; ?>
<section class="channel-header">
    <div class="channel-header__container">
        <div class="channel-profile">
            <div class="channel-profile__avatar">
                <img src="<?= $_SESSION['user']['avatar_url'] ?>" alt="Аватар канала" class="channel-avatar__image">
            </div>
            <div class="channel-profile__info">
                <h1 class="channel-profile__name"> <?= $_SESSION['user']['channel_name'] ?> </h1>
                <p class="channel-profile__username">@<?= $_SESSION['user']['id'] ?></p>
            </div>
        </div>
        <div class="buttons" style="display: flex; gap: 13px; flex-wrap: wrap;">
            <a href="/profile/edit"><button class="channel-header__button"> Изменить профиль </button></a>
            <a href="/profile/stats"><button class="channel-header__button"> Посмотреть статистику </button></a>
        </div>

    </div>
    <p class="channel-header__subtitle">
        <?= $_SESSION['user']['channel_description'] ?>
    </p>
</section>
<section class="profile-content">
    <?php if (empty($courses)): ?>
        <?php require VIEWS . '/errors/smile_error.tpl.php';
        showerror('=(', 'Пока нет курсов');
        ?>
    <?php else: ?>
        <h2 class="profile-content__title"> Курсы </h2>
        <div class="courses" id="courses">
            <?php foreach ($courses as $course): ?>
                <div class="course" data-category="games">
                    <div class="course__preview"> <img src="<?= $course['preview_url'] ?>" alt="course_preview"> </div>
                    <h2 class="course__title"> <?= $course['course_name'] ?> </h2>
                    <p class="course__author" style="margin-bottom: 10px;"> Статус: <?php switch ($course['status']) {
                                                                                        case 'public':
                                                                                            echo 'Публичный';
                                                                                            break;
                                                                                        case 'hidden':
                                                                                            echo 'Скрытый';
                                                                                            break;
                                                                                        case 'moderation':
                                                                                            echo 'На модерации';
                                                                                            break;
                                                                                        case 'development':
                                                                                            echo 'В разработке';
                                                                                            break;
                                                                                    } ?> </p>
                    <div class="course__buttons">
                        <a class="channel-header__button" href="/course/edit/?id=<?= $course['id'] ?>"> Изменить </a>
                        <a class="channel-header__button" href="/course/show/?id=<?= $course['id'] ?>"> Посмотреть </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>


<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>