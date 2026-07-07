<?php require VIEWS . '/incs/headers/mainheader.tpl.php'; ?>
<?php $errors = $_SESSION['validation']['editProfile'] ?? [];
unset($_SESSION['validation']['profile']); ?>

<div class="lesson-header">
    <h2 class="lesson-header__title"> Редактирование профиля </h2>
    <button class="lesson-header__button" type="submit" form="profile-edit-form"> Сохранить </button>
</div>
<form action="/profile/edit" method="post" id="profile-edit-form" enctype="multipart/form-data" class="course-form editFormProfile">
    <h2 class="profileEdit__title"> Общая информация </h2>
    <div class="inputs profileEdit__inputs">
        <div class="input-group">
            <label for="name" class="input-group__label"> Имя профиля </label>
            <input type="text" class="input-group__input" id="name" name="channel_name" value="<?= htmlspecialchars($_SESSION['user']['channel_name']) ?>" data-mask="cyrillic">
            <?php $errorName = 'channel_name';
            include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
        <div class="input-group">
            <label for="name" class="input-group__label"> Описание профиля </label>
            <textarea type="text" class="input-group__input" id="name" name="channel_description" value="" data-mask="text" minlength="20"><?= htmlspecialchars($_SESSION['user']['channel_description']) ?></textarea>
            <?php $errorName = 'channel_description';
            include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
    </div>
    <div class="input-group">
        <div class="profileEdit__avatar">
            <img src="<?= $_SESSION['user']['avatar_url'] ?>" alt="avatar" class="">
        </div>
        <label for="file" class="input-group__label"> Выберите новый аватар </label>
        <input type="file" class="input-group__input inputEdit" id="file" name="avatar">
        <?php $errorName = 'avatar';
        include VIEWS . '/incs/errors/formError.tpl.php'; ?>
    </div>
    <h2 class="profileEdit__title"> Безопасность </h2>
    <div class="inputs profileEdit__inputs">
        <div class="input-group">
            <label for="name" class="input-group__label"> Ваш логин </label>
            <input type="text" class="input-group__input" id="name" name="login" value="<?= htmlspecialchars($_SESSION['user']['login']) ?>" data-mask="login" minlength="6">
            <?php $errorName = 'login';
            include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
        <div class="input-group">
            <label for="name" class="input-group__label"> Новый пароль </label>
            <input type="text" class="input-group__input" id="name" name="password" value="" data-mask="text" minlength="8">
            <?php $errorName = 'password';
            include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
    </div>
    <p class="input-group__label" style="padding-left: 0px; max-width: 500px;"> Если не хотите менять пароль или аватар, оставьте поля пустыми </p>
</form>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>

<script src="/public/assets/js/mask.js?v=<?= time() ?>"> </script>