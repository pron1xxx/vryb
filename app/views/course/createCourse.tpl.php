<?php require VIEWS . '/incs/headers/mainheader.tpl.php'; ?>
<?php 
$errors = $_SESSION['validation']['create'] ?? []; 
$old = $_SESSION['old']['create'] ?? [];
unset($_SESSION['validation']['create']);
unset($_SESSION['old']['create']);
?>
<div class="lesson-header">
    <h2 class="lesson-header__title"> Создание курса </h2>
</div>

<?php if (isset($_POST)): ?>
<script>
    console.log('POST данные:', <?= json_encode($_POST) ?>);
</script>
<?php endif; ?>

<form action="/create" method="post" id="create-course" enctype="multipart/form-data"> 
<div class="inputs">
    <div class="input-group">
        <label for="name" class="input-group__label"> Введите название </label>
        <input type="text" class="input-group__input" id="name" placeholder="Как назовете курс?" name="course_name" value="<?= htmlspecialchars($old['course_name'] ?? '') ?>" required data-mask="text" minlength="6">
        <?php $errorName = 'course_name';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
    </div>
    <div class="input-group">
        <label for="file" class="input-group__label"> Добавьте обложку </label>
        <input type="file" class="input-group__input" id="file" name="course_preview" required>
        <?php $errorName = 'course_preview';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
    </div>
    <div class="input-group">
        <label for="description" class="input-group__label"> Введите описание </label>
        <textarea class="input-group__input" id="description" placeholder="Расскажите подробнее" name="course_description" required data-mask="text" minlength="20"><?= htmlspecialchars($old['course_description'] ?? '') ?></textarea>
        <?php $errorName = 'course_description';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
    </div>
    <div class="input-group">
        <label for="category" class="input-group__label"> Выберите категорию </label>
        <select class="input-group__input" id="category" name="course_category" required>
            <?php foreach($categories as $category): ?>
            <option value="<?= $category['category_name'] ?>" <?= isset($old['course_category']) && $old['course_category'] == $category['category_name'] ? 'selected' : '' ?>>
                <?= $category['category_name'] ?>
            </option>
            <?php endforeach; ?>
        </select>
        <?php $errorName = 'course_category';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
    </div>
    
    <!-- Добавьте скрытое поле для проверки -->
    <input type="hidden" name="form_submitted" value="1">
    
    <!-- Кнопка теперь внутри формы -->
    <div class="input-group">
        <button class="lesson-header__button" type="submit" name="submit_button"> Создать курс </button>
    </div>
</div>
</form>

<div class="lesson-header">
    <h2 class="lesson-header__title"> Добавление уроков </h2>
    <p class="input-group__label"> Перед добавлением уроков заполните информацию о курсе </p>
</div>

<script src="/public/assets/js/mask.js?v=<?= time() ?>"> </script>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>