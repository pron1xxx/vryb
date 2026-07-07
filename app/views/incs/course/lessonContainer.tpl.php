<div class="lesson-container-create" id="lesson">
    <?php $errors = $lessonErrorsForInputs ?? [] ?>
    <div class="create-lesson">
        <div class="inputs">
            <div class="input-group">
                <label class="input-group__label">Название урока</label>
                <input type="text" class="input-group__input"
                    placeholder="Введите название урока" name="lesson_name" 
                    value="<?= isset($lesson_data['title']) ? htmlspecialchars($lesson_data['title']) : '' ?>"
                    required
                    minlength="5"
                    data-mask="text">
                <?php $errorName = 'lesson_name';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
            
            <?php if (isset($lesson_data['video_url'])): ?>
            <div class="input-group">
                <label class="input-group__label">Текущее видео</label>
                <video
                    controls
                    controlsList="nodownload"
                    oncontextmenu="return false;"
                    poster="<?= htmlspecialchars($lesson_data['preview_url'] ?? '') ?>"
                    width="100%"
                    preload="none">
                    <source
                        src="<?= h($lesson_data['video_url']) ?>"
                        type="video/mp4">
                    Ваш браузер не поддерживает видео тег.
                </video>
            </div>
            <?php endif; ?>
            
            <div class="input-group">
                <label class="input-group__label"><?= isset($lesson_data['video_url']) ? 'Изменить видео для урока' : 'Добавить видео для урока' ?></label>
                <input type="file" class="input-group__input" name="lesson_video">
                <?php $errorName = 'lesson_video';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
            
            <?php if (isset($lesson_data['preview_url'])): ?>
            <div class="input-group">
                <label class="input-group__label">Текущее превью</label>
                <img src="<?= htmlspecialchars($lesson_data['preview_url']) ?>" alt="">
            </div>
            <?php endif; ?>
            
            <div class="input-group">
                <label class="input-group__label"><?= isset($lesson_data['preview_url']) ? 'Изменить превью для урока' : 'Добавить превью для урока' ?></label>
                <input type="file" class="input-group__input" name="lesson_preview">
                <?php $errorName = 'lesson_preview';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
            
            <div class="input-group">
                <label class="input-group__label">Описание урока</label>
                <textarea class="input-group__input" placeholder="Расскажите подробнее, поделитесь ссылкой и т.д." name="lesson_description" required minlength="20" data-mask="text"><?= isset($lesson_data['description']) ? htmlspecialchars($lesson_data['description']) : '' ?></textarea>
                <?php $errorName = 'lesson_description';
                include VIEWS . '/incs/errors/formError.tpl.php'; ?>
            </div>
        </div>
        <?php if (!isset($test_data) || !$test_data): ?>
            <button class="create-lesson__button dynamic-button" type="button" id="add_test">Добавить тест</button>
        <?php endif; ?>
    </div>

    <?php if (isset($test_data) && $test_data): ?>
        <?php include VIEWS . '/incs/course/lessonTestContainer.tpl.php'; ?>
    <?php endif; ?>
</div>