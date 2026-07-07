<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>
<?php
$all_errors = $_SESSION['lesson']['errors'] ?? [];
$lessonErrorsForInputs = $all_errors['lesson'] ?? [];
unset($_SESSION['lesson']['errors']);
$lessonFileErrors = $_SESSION['lesson']['file']['errors'] ?? [];
unset($_SESSION['lesson']['file']['errors']);
// Генерируем уникальный ключ для новых вопросов
$newQuestionKey = time();
?>
<div class="lesson-header">
    <div class="lesson-header__titles">
        <h2 class="lesson-header__title"> Изменение урока </h2>
        <p> Урок: <a href="/lesson/show/?id=<?= $id ?>" class="lesson__a"><?= htmlspecialchars($lesson_data['title']) ?></a></p>
        <p> Курс: <a href="/course/show/?id=<?= $lesson_data['course_id'] ?>" class="lesson__a"><?= htmlspecialchars($lesson_data['course_name']) ?></a></p>
    </div>

    <div class="buttons">
        <button class="lesson-header__button" type="submit" form="lessonForm"> Сохранить изменения </button>
    </div>
</div>
<button style="align-self: start;" class="lesson-header__button" id="showInfo"> Правила добавления </button>
<div class="error-container" style="background: #fff8e6; border: 2px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 8px; display: none;" id="questions_info">
    <h3 style="color: #856404; margin-top: 0; display: flex; align-items: center; gap: 10px;">
        ⚠️ Важная информация перед изменением урока
    </h3>

    <div style="color: #856404; line-height: 1.6;">
        <p style="margin: 10px 0; font-weight: bold;">🚨 Внимание! Все данные, не прошедшие проверку, будут утеряны!</p>
        <p style="margin: 10px 0; font-weight: bold;">🚨 Если не хотите изменять превью или видео, то оставьте поля пустыми </p>
        <p style="margin: 10px 0; font-weight: bold;">🚨 После сохранения изменений в уроке, курс будет переведен в статус "В разработке", внесите изменения в других уроках если это нужно и отправьте ваш курс на модерацию</p>

        <div style="background: #fff; padding: 15px; border-radius: 6px; margin: 15px 0;">
            <h4 style="color: #856404; margin-top: 0;">📝 Правила заполнения вопросов:</h4>
            <ul style="color: #856404; margin: 10px 0; padding-left: 20px;">
                <li>✅ Все поля обязательны для заполнения</li>
                <li>✅ Текст вопроса: от 5 до 25 символов</li>
                <li>✅ Каждый вариант ответа: от 1 до 20 символов</li>
                <li>✅ Правильный ответ: число от 1 до 4</li>
                <li>✅ Все 4 варианта ответа должны быть заполнены</li>
            </ul>
        </div>

        <p style="margin: 10px 0; font-style: italic;">💡 Совет: проверьте все поля перед нажатием на кнопку сохранить изменения</p>
        <button id="hide_questions_info" type="button"> Скрыть сообщение </button>
    </div>
</div>
<!-- Блок для отображения общих ошибок -->
<?php if (!empty($all_errors)): ?>
    <div class="error-container" style="background: #fee; border: 1px solid #fcc; padding: 15px; margin: 20px 0; border-radius: 5px;">
        <h3 style="color: #d00; margin-top: 0;">⚠️ Обнаружены ошибки! </h3>
        <?php if (isset($all_errors['questions'])): ?>
            <div class="questions-errors">
                <h4 style="color: #d00;">Ошибки в вопросах:</h4>
                <?php foreach ($all_errors['questions'] as $questionIndex => $questionErrors): ?>
                    <div class="question-error" style="background: #fff5f5; padding: 10px; margin: 10px 0; border-left: 3px solid #d00;">
                        <strong style="color: #d00;">Вопрос <?= $questionIndex + 1 ?>:</strong>
                        <ul style="color: #d00; margin: 5px 0;">
                            <?php if (isset($questionErrors['question_errors']['text'])): ?>
                                <li> <b>Текст вопроса:</b> <?= $questionErrors['question_errors']['text'][0] ?></li>
                            <?php endif; ?>

                            <?php if (isset($questionErrors['question_errors']['correct_answer'])): ?>
                                <li> <b>Правильный ответ:</b> <?= $questionErrors['question_errors']['correct_answer'][0] ?></li>
                            <?php endif; ?>
                            <?php
                            $hasAnswerErrors = false;
                            if (isset($questionErrors['answers_errors'])) {
                                foreach ($questionErrors['answers_errors'] as $answerKey => $answerErrors) {
                                    if (!in_array($answerKey, ['text', 'correct_answer']) && !empty($answerErrors)) {
                                        $hasAnswerErrors = true;
                                        break;
                                    }
                                }
                            }
                            ?>
                            <?php if ($hasAnswerErrors): ?>
                                <li>🗳️ Все варианты ответов должны быть заполнены (минимум 5 символов)</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<!-- Блок для отображения ошибок файлов -->
<?php if (!empty($lessonFileErrors)): ?>
    <div class="error-container" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;">
        <h3 style="color: #856404; margin-top: 0;">⚠️ Ошибки в файлах</h3>
        <ul style="color: #856404; margin: 10px 0; padding-left: 20px;">
            <?php if (isset($lessonFileErrors['lect_length'])): ?>
                <li>📄 <strong>Лекционные файлы:</strong> <?= htmlspecialchars($lessonFileErrors['lect_length']) ?></li>
            <?php endif; ?>

            <?php if (isset($lessonFileErrors['prak_length'])): ?>
                <li>📄 <strong>Практические файлы:</strong> <?= htmlspecialchars($lessonFileErrors['prak_length']) ?></li>
            <?php endif; ?>

            <?php if (isset($lessonFileErrors['lect_files'])): ?>
                <li>📄 <strong>Ошибки в лекционных файлах:</strong>
                    <ul style="margin: 5px 0; padding-left: 20px;">
                        <?php foreach ($lessonFileErrors['lect_files'] as $fileKey => $fileErrors): ?>
                            <li>Файл <?= str_replace('file_', '', $fileKey) + 1 ?>:
                                <?= htmlspecialchars(implode(', ', $fileErrors['file'] ?? [])) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (isset($lessonFileErrors['prak_files'])): ?>
                <li>📄 <strong>Ошибки в практических файлах:</strong>
                    <ul style="margin: 5px 0; padding-left: 20px;">
                        <?php foreach ($lessonFileErrors['prak_files'] as $fileKey => $fileErrors): ?>
                            <li>Файл <?= str_replace('file_', '', $fileKey) + 1 ?>:
                                <?= htmlspecialchars(implode(', ', $fileErrors['file'] ?? [])) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="forms" style="display: flex; flex-wrap: wrap; gap: 24px;">
    <form action="/lesson/edit/?id=<?= $id ?>" method="post" id="lessonForm" enctype="multipart/form-data">
        <div class="lesson-container-create" style="display: flex; width: 100%;">
            <?php $errors = $lessonErrorsForInputs ?>
            <div class="create-lesson">
                <div class="inputs">
                    <div class="input-group">
                        <label class="input-group__label">Название урока</label>
                        <input type="text" class="input-group__input"
                            placeholder="Введите название урока" name="lesson_name" value="<?= htmlspecialchars($lesson_data['title']) ?>">
                        <?php $errorName = 'lesson_name';
                        include VIEWS . '/incs/errors/formError.tpl.php'; ?>
                    </div>
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
                                src="<?= htmlspecialchars($lesson_data['video_url']) ?>"
                                type="video/mp4">
                            Ваш браузер не поддерживает видео тег.
                        </video>
                    </div>
                    <div class="input-group">
                        <label class="input-group__label">Измените видео для урока</label>
                        <input type="file" class="input-group__input" name="lesson_video">
                        <?php $errorName = 'lesson_video';
                        include VIEWS . '/incs/errors/formError.tpl.php'; ?>
                    </div>
                    <div class="input-group">
                        <label class="input-group__label">Текущее превью</label>
                        <img src="<?= htmlspecialchars($lesson_data['preview_url']) ?>" alt="" style="max-width: 300px; max-height: 200px;">
                    </div>
                    <div class="input-group">
                        <label class="input-group__label">Измените превью для урока</label>
                        <input type="file" class="input-group__input" name="lesson_preview">
                        <?php $errorName = 'lesson_preview';
                        include VIEWS . '/incs/errors/formError.tpl.php'; ?>
                    </div>
                    <div class="input-group">
                        <label class="input-group__label">Описание урока</label>
                        <textarea class="input-group__input" placeholder="Расскажите подробнее, поделитесь ссылкой и т.д." name="lesson_description"><?= htmlspecialchars($lesson_data['description']) ?></textarea>
                        <?php $errorName = 'lesson_description';
                        include VIEWS . '/incs/errors/formError.tpl.php'; ?>
                    </div>
                </div>
            </div>
            <?php if (!$test_data): ?>
                <button class="create-lesson__button dynamic-button" type="button" id="add_test" style="max-height: 40px;">Добавить тест</button>
            <?php endif; ?>

            <?php if ($test_data): ?>
                <div class="create-test" id="test_container">
                    <div class="test-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <button type="button" class="remove-test-btn" style="background: #ff4444; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                            Удалить тест полностью
                        </button>
                    </div>
                    <div class="input-group">
                        <label class="input-group__label">Название теста</label>
                        <input type="text" class="input-group__input" placeholder="Название теста"
                            name="test_title" value="<?= htmlspecialchars($test_data['test_title']) ?>" id="test_name">
                    </div>
                    <button class="create-lesson__button dynamic-button question add-question" type="button" style="min-height: 40px;">Добавить вопрос</button>
                    <div id="test_inputs">
                        <?php
                        if (isset($questions_data) && !empty($questions_data)) {
                            foreach ($questions_data as $question) {
                                include VIEWS . '/incs/course/testQuestion.tpl.php';
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="creates" style="display: flex; flex-direction: column; gap: 24px;max-width: 400px;">
                <div class="lesson-container-create" style="width: 100%">
                    <?php $errors = $lessonErrorsForInputs ?? [] ?>
                    <div class="create-lesson" style="width:100%;">
                        <div class="inputs" id="files_div">
                            <div class="input-group">
                                <label class="input-group__label"> Добавить лекционый файл </label>
                                <input type="file" class="input-group__input" name="lesson_lect[]">
                            </div>
                        </div>
                        <button class="create-lesson__button dynamic-button" type="button" id="add_file">Еще файл</button>
                    </div>
                </div>
                <div class="lesson-container-create" style="width: 100%">
                    <?php $errors = $lessonErrorsForInputs ?? [] ?>
                    <div class="create-lesson" style="width:100%;">
                        <div class="inputs" id="files_div_prak">
                            <div class="input-group">
                                <label class="input-group__label"> Добавить практический файл </label>
                                <input type="file" class="input-group__input" name="lesson_prak[]">
                            </div>
                        </div>
                        <button class="create-lesson__button dynamic-button" type="button" id="add_file_prak">Еще файл</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="lesson-files" style="margin: 0;">
        <?php if (!empty($files_data)): ?>
            <?php
            $sections = [
                'lect' => [
                    'title' => 'Лекционные материалы',
                    'icon' => '',
                    'badge' => 'лекция'
                ],
                'prak' => [
                    'title' => 'Практические материалы',
                    'icon' => '',
                    'badge' => 'практика'
                ]
            ];

            foreach ($sections as $type => &$section) {
                $section['files'] = array_filter($files_data, fn($file) => $file['file_type'] === $type);
            }
            unset($section);
            ?>

            <?php foreach ($sections as $section): ?>
                <?php if (!empty($section['files'])): ?>
                    <div class="files-section">
                        <div class="files-section__header">
                            <span class="files-section__icon"><?= $section['icon'] ?></span>
                            <h3 class="files-section__title">
                                <?= $section['title'] ?>
                                <span class="files-count"><?= count($section['files']) ?></span>
                            </h3>
                        </div>

                        <div class="files-list">
                            <?php foreach ($section['files'] as $file): ?>
                                <?php
                                include VIEWS . '/incs/course/fileItem.tpl.php';
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-files">
                <div class="no-files__icon">📭</div>
                <h3 class="no-files__title">Нет материалов</h3>
                <p class="no-files__text">Для этого урока еще нет дополнительных материалов</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="lesson-container-create" style="width: 100%">
        <?php $errors = $lessonErrorsForInputs ?? [] ?>
        <form class="create-lesson" style="width:100%;" method="post" action="https://pronixxx-dev.ru/public/api-proxy.php" id="test_pars">
            <div class="inputs" id="files_div_prak">
                <div class="input-group">
                    <label class="input-group__label"> Добавить тест из файла </label>
                    <input type="file" class="input-group__input" name="file">
                </div>
            </div>
            <a href="/public/assets/files/test_example.txt" class="create-lesson__button" download="пример_теста.txt"> Пример файла</a>
            <button class="create-lesson__button dynamic-button" type="submit" id="detected_button">Распознать</button>
            <button class="create-lesson__button dynamic-button hidden" type="button" id="generate_button">Сгенерировать</button>
        </form>
    </div>
</div>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>


<script>
    let newQuestionCounter = <?= $newQuestionKey ?>;

    document.addEventListener('click', function(e) {
        // Добавление теста
        if (e.target.id === 'add_test') {
            e.preventDefault();
            const lessonContainer = e.target.closest('.lesson-container-create');
            if (!lessonContainer) return;

            const testCount = lessonContainer.querySelectorAll('.create-test').length;
            if (testCount < 1) {
                // Создаем HTML для нового теста
                const newTestHTML = `
                <div class="create-test" id="test_container">
                    <div class="test-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <button type="button" class="remove-test-btn" style="background: #ff4444; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                            Удалить тест полностью
                        </button>
                    </div>
                    <div class="input-group">
                        <label class="input-group__label">Название теста</label>
                        <input type="text" class="input-group__input" placeholder="Название теста"
                               name="test_title" value="" id="test_name">
                    </div>
                    <button class="create-lesson__button dynamic-button question add-question" type="button">Добавить вопрос</button>
                    <div id="test_inputs"></div>
                </div>
            `;

                // Вставляем тест после кнопки "Добавить тест"
                e.target.insertAdjacentHTML('afterend', newTestHTML);
                e.target.style.display = 'none'; // Скрываем кнопку добавления теста
            }
        }

        if (e.target.classList.contains('add-question')) {
            e.preventDefault();
            const testContainer = e.target.closest('.create-test');
            if (!testContainer) return;

            const questionsContainer = testContainer.querySelector('#test_inputs');
            if (!questionsContainer) return;

            newQuestionCounter++;

            const newQuestionHTML = `
        <div class="create-test__question">
            <input type="hidden" name="questions[${newQuestionCounter}][question_id]" value="">
            <div class="input-group">
                <label class="input-group__label">Вопрос</label>
                <input type="text" class="input-group__input" placeholder="Введите вопрос" 
                       name="questions[${newQuestionCounter}][text]" required id="test_name">
            </div>
            
            <label class="input-group__label">Варианты ответов</label>
            <div class="input-group answers">
                <input type="hidden" name="questions[${newQuestionCounter}][answer_ids][]" value="">
                <input type="text" class="input-group__input" placeholder="Ответ 1" 
                       name="questions[${newQuestionCounter}][answers][]" required>
                <input type="hidden" name="questions[${newQuestionCounter}][answer_ids][]" value="">
                <input type="text" class="input-group__input" placeholder="Ответ 2" 
                       name="questions[${newQuestionCounter}][answers][]" required>
                <input type="hidden" name="questions[${newQuestionCounter}][answer_ids][]" value="">
                <input type="text" class="input-group__input" placeholder="Ответ 3" 
                       name="questions[${newQuestionCounter}][answers][]" required>
                <input type="hidden" name="questions[${newQuestionCounter}][answer_ids][]" value="">
                <input type="text" class="input-group__input" placeholder="Ответ 4" 
                       name="questions[${newQuestionCounter}][answers][]" required>
            </div>
            
            <div class="input-group">
                <label class="input-group__label">Правильный ответ (1-4)</label>
                <input type="number" class="input-group__input" min="1" max="4" 
                       name="questions[${newQuestionCounter}][correct_answer]" required>
            </div>
            <button type="button" class="remove-question-btn" style="background: #ff4444; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">Удалить вопрос</button>
        </div>
        `;

            questionsContainer.insertAdjacentHTML('beforeend', newQuestionHTML);
        }

        if (e.target.classList.contains('remove-question-btn')) {
            e.preventDefault();
            if (confirm('Вы уверены, что хотите удалить этот вопрос?')) {
                const questionElement = e.target.closest('.create-test__question');
                if (questionElement) {
                    questionElement.remove();

                    const testContainer = questionElement.closest('.create-test');
                    const remainingQuestions = testContainer.querySelectorAll('.create-test__question');

                    if (remainingQuestions.length === 0) {
                        // Показываем сообщение, что тест будет удален
                        setTimeout(() => {
                            alert('Все вопросы удалены. После сохранения тест будет полностью удален из урока.');
                        }, 100);
                    }
                }
            }
        }

        if (e.target.classList.contains('remove-test-btn')) {
            e.preventDefault();
            if (confirm('Вы уверены, что хотите полностью удалить тест? Все вопросы будут удалены.')) {
                const testContainer = e.target.closest('.create-test');
                const addTestButton = document.querySelector('#add_test');

                if (testContainer) {
                    testContainer.remove();

                    if (addTestButton) {
                        addTestButton.style.display = 'block';
                    }

                    setTimeout(() => {
                        alert('Тест удален. После сохранения тест будет полностью удален из урока.');
                    }, 100);
                }
            }
        }
    });

    document.querySelector('#showInfo').addEventListener('click', function() {
        document.querySelector('#questions_info').style.display = 'block'
        this.style.display = 'none';
    })
    document.querySelector('#hide_questions_info').addEventListener('click', function() {
        document.querySelector('#questions_info').style.display = 'none'
        document.querySelector('#showInfo').style.display = 'block'
    })

    document.querySelector('#lessonForm').addEventListener('submit', function(e) {
        let hasErrors = false;

        // Проверка обязательных полей урока
        const lessonName = document.querySelector('input[name="lesson_name"]');
        const lessonDescription = document.querySelector('textarea[name="lesson_description"]');

        if (!lessonName.value.trim()) {
            alert('Название урока обязательно для заполнения');
            lessonName.focus();
            e.preventDefault();
            return;
        }

        if (!lessonDescription.value.trim()) {
            alert('Описание урока обязательно для заполнения');
            lessonDescription.focus();
            e.preventDefault();
            return;
        }

        // Проверка тестов и вопросов (если есть тест)
        const testContainer = document.querySelector('.create-test');
        if (testContainer) {
            const testTitle = testContainer.querySelector('input[name="test_title"]');
            const questions = testContainer.querySelectorAll('.create-test__question');

            if (!testTitle.value.trim()) {
                alert('Название теста обязательно для заполнения');
                testTitle.focus();
                e.preventDefault();
                return;
            }

            if (questions.length === 0) {
                return;
            }

            // Проверка каждого вопроса
            questions.forEach((question, index) => {
                const questionText = question.querySelector('input[name*="[text]"]');
                const correctAnswer = question.querySelector('input[name*="[correct_answer]"]');
                const answers = question.querySelectorAll('input[name*="[answers]"]');

                if (!questionText.value.trim()) {
                    alert(`Вопрос ${index + 1}: Текст вопроса обязателен`);
                    hasErrors = true;
                    questionText.focus();
                    return;
                }

                if (!correctAnswer.value || correctAnswer.value < 1 || correctAnswer.value > 4) {
                    alert(`Вопрос ${index + 1}: Укажите правильный ответ (от 1 до 4)`);
                    hasErrors = true;
                    correctAnswer.focus();
                    return;
                }

                let allAnswersFilled = true;
                answers.forEach((answer, answerIndex) => {
                    if (!answer.value.trim()) {
                        alert(`Вопрос ${index + 1}: Все варианты ответов должны быть заполнены (Ответ ${answerIndex + 1})`);
                        allAnswersFilled = false;
                        answer.focus();
                        return;
                    }
                });

                if (!allAnswersFilled) {
                    hasErrors = true;
                    return;
                }
            });
        }

        if (hasErrors) {
            e.preventDefault();
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        const detected_button = document.querySelector("#detected_button");
        const test_form = document.querySelector("#test_pars");

        test_form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const originalButtonText = detected_button.textContent;
            detected_button.textContent = "Распознавание...";
            detected_button.disabled = true;

            try {
                const form_data = new FormData(test_form);

                const response = await fetch(test_form.action, {
                    method: "POST",
                    body: form_data,
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
                }

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error("Сервер вернул не JSON:", text.substring(0, 200));
                    throw new Error("Сервер вернул некорректный ответ");
                }

                const data = await response.json();

                if (!data.success || !data.data) {
                    throw new Error("Некорректная структура ответа сервера");
                }

                generate_button = document.querySelector("#generate_button")
                detected_button.classList.add("hidden")
                generate_button.classList.remove("hidden")
                
                console.log(data)
                console.log(data.data.test_name)

                let test_name = document.querySelector("#test_name");
                if (test_name) {
                    test_name.value = data.data.test_name || "";
                }

                const questions = data.data.questions || [];

                generate_button.addEventListener("click", () => {
                    const addTestButton = document.querySelector('#add_test');

                    addTestButton.click();

                    setTimeout(() => {}, 100);

                    let test_name = document.querySelector("#test_name");
                if (test_name) {
                    test_name.value = data.data.test_name || "";
                }

                    const test_inputs = document.querySelector("#test_inputs");

                    questions.forEach((question, index) => {
                        const questionText = question.question || "Вопрос";
                        const answers = question.answers || [];
                        const correctAnswer = question.correct_answer || 1;

                        test_inputs.insertAdjacentHTML("beforeend",
                            `
        <div class="create-test__question">
            <input type="hidden" name="questions[${index+1}][question_id]" value="">
            <div class="input-group">
                <label class="input-group__label">Вопрос</label>
                <input type="text" class="input-group__input" placeholder="Введите вопрос" 
                       name="questions[${index+1}][text]" required value="${questionText}">
            </div>
            
            <label class="input-group__label">Варианты ответов</label>
            <div class="input-group answers">
                <input type="hidden" name="questions[${index+1}][answer_ids][]" value="">
                <input type="text" class="input-group__input" placeholder="Ответ 1" value="${answers[0]}" 
                       name="questions[${index+1}][answers][]" required>
                <input type="hidden" name="questions[${index+1}][answer_ids][]" value="">
                <input type="text" class="input-group__input" placeholder="Ответ 2" value="${answers[1]}"
                       name="questions[${index+1}][answers][]" required>
                <input type="hidden" name="questions[${index+1}][answer_ids][]" value="">
                <input type="text" class="input-group__input" placeholder="Ответ 3" value="${answers[2]}"
                       name="questions[${index+1}][answers][]" required>
                <input type="hidden" name="questions[${index+1}][answer_ids][]" value="">
                <input type="text" class="input-group__input" placeholder="Ответ 4" value="${answers[3]}"
                       name="questions[${index+1}][answers][]" required>
            </div>
            
            <div class="input-group">
                <label class="input-group__label">Правильный ответ (1-4)</label>
                <input type="number" class="input-group__input" min="1" max="4" 
                       name="questions[${index+1}][correct_answer]" required value="${correctAnswer}">
            </div>
            <button type="button" class="remove-question-btn" style="background: #ff4444; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">Удалить вопрос</button>
        </div>
        `
                        );
                    });

                    showMessage("Тест успешно сгенерирован", "success");
                    detected_button.classList.remove("hidden")
                    detected_button.textContent = "Распознать"
                    generate_button.classList.add("hidden")
                });

                showMessage("Тест успешно распознан!", "success");

            } catch (error) {
                showMessage(error.message.message || "Ошибка запроса", "error");
                detected_button.textContent = originalButtonText;
                detected_button.disabled = false;
            } finally {
                detected_button.disabled = false;
            }
        });
    });


    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }
</script>

<script>

</script>

<script src="/public/assets/js/files_more.js"> </script>
<script src="/public/assets/js/show_message.js?v=<?= time() ?>"> </script>
<script src="/public/assets/js/delete_file.js?v=<?= time() ?>"> </script>