<?php require VIEWS . '/incs/headers/formHeader.tpl.php';
$errors = $_SESSION['login']['errors'] ?? [];
unset($_SESSION['login']['errors']);
?>
<div class="background">
    <img src="../../public/assets/images/formBackground.png" alt="" class="background__image">
    <form action="/login" class="form" method="post">
        <h2 class="form__title">Вход</h2>
        <div class="form__inputs" id="form__inputs">
            <div class="form__inputGroup">
                <label for="login" class="form__label">Введите логин</label>
                <input type="text" placeholder="Введите логин" name="login" id="login" required class="form__input" value="<?php echo getOldValue('login','login') ?>" minlength="6" data-mask="login">
            </div>
            <?php $errorName = 'login';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            <div class="form__inputGroup">
                <label for="password" class="form__label">Введите пароль</label>
                <input type="password" placeholder="Введите пароль" name="password" id="password" required class="form__input" minlength="8" data-mask="text">
            </div>
            <?php $errorName = 'password';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
        </div>
        <div class="form__buttons">
            <button type="submit" class="form__button">Войти</button>
            <p class="form__text">Еще нет аккаунта? <a href="/register" class="form__a">Зарегистрироваться</a></p>
        </div>
    </form>
</div>
</body>
</html>

<script src="/public/assets/js/mask.js?v=<?= time() ?>"> </script>