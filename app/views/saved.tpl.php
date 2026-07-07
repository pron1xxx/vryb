<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>

<div class="authors-container">
    <h2 class="course-header__title"> Авторы </h2>
    <div class="authors">
        <?php foreach ($subscribes as $subscribe): ?>
            <a href="/channel/?id=<?= $subscribe['subscribed_user_id'] ?>">
                <div class="author">
                    <div class="author__avatar"> <img src="<?= $subscribe['avatar_url']  ?>" alt=""> </div>
                    <div class="author__name"> <?= $subscribe['channel_name'] ?> </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php if ($subscribes == false): ?>
    <?php include_once VIEWS . '/errors/smile_error.tpl.php';
    showError('=(', 'Начните отслеживать авторов'); ?>
<?php endif; ?>


<div class="courses-container">
    <h2 class="course-header__title"> Курсы </h2>
    <div class="courses" id="courses">
        <?php foreach ($saved_courses as $course): ?>
            <div class="course">
                <a href="/course/show/?id=<?= $course['course_id'] ?>"><div class="course__preview"> <img src="<?= $course['preview_url'] ?>" alt="course_preview"> </div></a>
                <h2 class="course__title"> <?= $course['course_name'] ?> </h2>
                <div class="course__authorWrapper">
                    <a href="/channel/?id=<?= $course['id'] ?>" class="course__author"> <?= $course['channel_name'] ?> </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php if ($saved_courses == false): ?>
    <?php include_once VIEWS . '/errors/smile_error.tpl.php';
    showError('=(', 'Сохраните пару курсов'); ?>
<?php endif; ?>


<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>