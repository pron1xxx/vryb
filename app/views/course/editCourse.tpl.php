<?php require VIEWS . '/incs/headers/mainheader.tpl.php'; ?>
<?php $errors = $_SESSION['validation']['create'] ?? [];
unset($_SESSION['validation']['create']); ?>
<div class="lesson-header">
    <h2 class="lesson-header__title"> Изменение курса </h2>
    <button class="lesson-header__button" type="submit" form="course-edit-form"> Опубликовать </button>
</div>
<p class="input-group__label" style="padding-left: 0px; max-width: 500px;"> После нажатия на кнопку опубликовать, курс отправляется на рассмотрение админиматрации, после проверки курс будет опубликован </p>
<form action="/course/edit/?id=<?= $_GET['id'] ?>" method="post" id="course-edit-form" enctype="multipart/form-data" class="course-form">
    <div class="inputs">
        <div class="input-group">
            <label for="name" class="input-group__label"> Введите название </label>
            <input type="text" class="input-group__input" id="name" placeholder="Как назавете курс?" name="course_name" value="<?php echo htmlspecialchars($course_data['course_name']) ?>" required data-mask="text" minlength="5">
            <?php $errorName = 'course_name';
            include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
        <div class="input-group">
            <label for="file" class="input-group__label"> Выберите новую обложку </label>
            <input type="file" class="input-group__input" id="file" name="course_preview">
            <?php $errorName = 'course_preview';
            include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
        <div class="input-group">
            <label for="description" class="input-group__label"> Введите описание </label>
            <textarea class="input-group__input" id="description" placeholder=" Расскажите подробнее" name="course_description" required data-mask="text" minlength="20"><?php echo htmlspecialchars($course_data['course_description']) ?></textarea>
            <?php $errorName = 'course_description';
            include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
        <div class="input-group">
            <label for="description" class="input-group__label"> Выберите категорию </label>
            <select class="input-group__input" id="description" name="course_category">
                <option value="<?= $category['category_name'] ?>"><?= $course_data['category'] ?> </option>
                <?php foreach ($categories as $category): if ($category['category_name'] === $course_data['category']) continue; ?>
                    <option value="<?= $category['category_name'] ?>"><?= $category['category_name'] ?> </option>
                <?php endforeach; ?>
            </select>
            <?php $errorName = 'course_category';
            include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
    </div>
    <div class="preview">
        <p class="input-group__label" style="padding-left: 0px; max-width: 500px;"> Превью курса </p>
        <div class="course-form__preview"> <img src="<?php echo htmlspecialchars($course_data['preview_url']) ?>" alt=""> </div>
    </div>
    <div class="buttons__checkgroup">
        <?php if ($course_data['status'] == 'hidden'): ?>
            <div class="checkgroup">
                <input type="checkbox" class="checkgroup__input" name="public"> </input>
                <label for="" class="checkgroup__label"> Сделать публичным </label>
            </div>
        <?php endif; ?>
        <?php if ($course_data['status'] == 'public'): ?>
            <div class="checkgroup">
                <input type="checkbox" class="checkgroup__input" name="hidden"> </input>
                <label for="" class="checkgroup__label"> Сделать скрытым </label>
            </div>
        <?php endif; ?>

        <?php if ($course_data['status'] == 'public' || $course_data['status'] == 'hidden'): ?>
            <p class="input-group__label" style="padding-left: 0px; max-width: 500px;"> Если поставить галочку будет изменен только статус курса! Изменения в других полях учитываться не будут</p>
        <?php endif; ?>
    </div>
</form>


<div class="lesson-header">
    <h2 class="lesson-header__title"> Уроки курса </h2>
    <a href="/lesson/create/?id=<?= $_GET['id'] ?>">
        <div class="lesson-header__button"> Добавить урок </div>
    </a>
</div>

<div class="course-container__videos" style="gap: 24px;">
    <?php foreach ($lessons as $lesson) : ?>
        <div class="course course-r">
            <a href="/lesson/show/?id=<?= $lesson['id'] ?>">
                <div class="course__preview"><img src="<?= $lesson['preview_url'] ?>" alt="lesson_preview"></div>
            </a>
            <div class="course__title"> <?= $lesson['title'] ?> </div>
            <a href="/lesson/edit/?id=<?= $lesson['id'] ?>" class="lesson__a"> Изменить урок </a>
        </div>
    <?php endforeach; ?>
</div>


<script src="/public/assets/js/mask.js?v=<?= time() ?>"> </script>
<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>