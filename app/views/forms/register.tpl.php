<?php require VIEWS . '/incs/headers/formHeader.tpl.php' ?>
<?php
$errors = $_SESSION['register']['errors'] ?? [];
$validator1 = $validator ?? '';
?>
<div class="background">
    <img src="../../public/assets/images/formBackground.png" alt="" class="background__image">
    <form action="/register" class="form" method="post">
        <?php showFlashAlert('register') ?>
        <h2 class="form__title">Регистрация</h2>
        <div class="form__inputs" id="form__inputs">
            <div class="form__inputGroup">
                <label for="login" class="form__label">Введите логин</label>
                <input type="text" placeholder="Латинские буквы минимум 5, можно использовать цифры" name="login" minlength="6" data-mask="login" id="login" value="<?php echo getOldValue('register', 'login'); ?>" required class="form__input">
                <?php $errorName = 'login';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
            <div class="form__inputGroup">
                <label for="channel_name" class="form__label">Введите название канала</label>
                <input type="text" placeholder="Буквы мминимум 5, можно использовать цифры" name="channel_name" id="channel_name" minlength="6" data-mask="cyrillic" value="<?php echo getOldValue('register','channel_name'); ?>" required class="form__input">
                <?php $errorName = 'channel_name';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
            <div class="form__inputGroup">
                <label for="email" class="form__label">Введите адрес эл. почты</label>
                <input type="email" placeholder="Введите адрес эл. почты" name="email" id="email" value="<?php echo getOldValue('register','email'); ?>" minlength="3" data-mask="email"  required class="form__input">
                <?php $errorName = 'email';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
            <div class="form__inputGroup">
                <label for="password" class="form__label">Введите пароль</label>
                <input type="password" placeholder="Введите пароль" name="password" id="password" value="<?php echo getOldValue('register','password'); ?>" minlength="8" data-mask="text"  required class="form__input">
                <?php $errorName = 'password';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
            <div class="form__inputGroup">
                <label for="password_confirmation" class="form__label">Повторите пароль</label>
                <input type="password" placeholder="Потвердите пароль" name="password_confirmation" id="password_confirmation" minlength="8" data-mask="text" value="<?php echo getOldValue('register','password_confirmation'); ?>" required class="form__input">
                <?php $errorName = 'password_confirmation';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
        </div>
        <div class="form__buttons">
            <button type="submit" class="form__button">Зарегистрироваться</button>
            <p class="form__text">Уже есть аккаунт? <a href="/login" class="form__a">Войти</a></p>
        </div>

    </form>
</div>
</body>

</html>

<script src="/public/assets/js/mask.js?v=<?= time() ?>"> </script>