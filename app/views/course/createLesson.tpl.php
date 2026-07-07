<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>
<?php
$all_errors = $_SESSION['lesson']['errors'] ?? [];
$lessonErrorsForInputs = $all_errors['lesson'] ?? [];
unset($_SESSION['lesson']['errors']);
?>
<div class="lesson-header">
    <div class="lesson-header__titles">
        <h2 class="lesson-header__title"> Добавление урока </h2>
        <p> К курсу <?= $course['course_name'] ?></p>
    </div>

    <div class="buttons">
        <button class="lesson-header__button" type="submit" form="lessonForm"> Добавить урок </button>
        <a class="lesson-header__button" href="/course/edit/?id=<?= $_GET['id'] ?>"> К курсу </a>
    </div>
</div>
<button style="align-self: start;" class="lesson-header__button" id="showInfo"> Правила добавления </button>
<div class="error-container" style="background: #fff8e6; border: 2px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 8px; display: none" id="questions_info">
    <h3 style="color: #856404; margin-top: 0; display: flex; align-items: center; gap: 10px;">
        ⚠️ Важная информация перед добавлением вопросов
    </h3>

    <div style="color: #856404; line-height: 1.6;">
        <p style="margin: 10px 0; font-weight: bold;">🚨 Внимание! Все данные, не прошедшие проверку, будут утеряны!</p>

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

        <p style="margin: 10px 0; font-style: italic;">💡 Совет: проверьте все поля перед нажатием на кнопку добавить к курсу</p>
        <button id="hide_questions_info"> Скрыть сообщение </button>
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

<form action="/lesson/create/?id=<?= $course['id'] ?>" method="post" id="lessonForm" enctype="multipart/form-data" style="display:flex; gap: 24px; flex-wrap: wrap;">
    <?php include VIEWS . '/incs/course/lessonContainer.tpl.php' ?>
    <div class="lesson-container-create">
        <?php $errors = $lessonErrorsForInputs ?? [] ?>
        <div class="create-lesson" style="width:100%;">
            <div class="inputs" id="files_div">
                <div class="input-group">
                    <label class="input-group__label"> Добавить лекционые файлы </label>
                    <input type="file" class="input-group__input" name="lesson_lect[]">
                </div>
            </div>
            <button class="create-lesson__button dynamic-button" type="button" id="add_file">Добавить еще</button>
        </div>
    </div>
    <div class="lesson-container-create">
        <?php $errors = $lessonErrorsForInputs ?? [] ?>
        <div class="create-lesson" style="width:100%;">
            <div class="inputs" id="files_div_prak">
                <div class="input-group">
                    <label class="input-group__label"> Добавить практические файлы </label>
                    <input type="file" class="input-group__input" name="lesson_prak[]">
                </div>
            </div>
            <button class="create-lesson__button dynamic-button" type="button" id="add_file_prak">Добавить еще</button>
        </div>
    </div>
</form>
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
<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>

<script>
    document.addEventListener('click', function(e) {
        if (e.target.id === 'add_test' || e.target.classList.contains('add-test-btn')) {
            const lessonContainer = e.target.closest('.lesson-container-create');
            if (!lessonContainer) return;

            const testCount = lessonContainer.querySelectorAll('.create-test').length;
            if (testCount < 1) {
                lessonContainer.insertAdjacentHTML('beforeend', `<?php include VIEWS . '/incs/course/lessonTestContainer.tpl.php' ?>`);
            } else {
                showMessage("Максимум 1 тест на урок", error)
            }
        }

        if (e.target.classList.contains('add-question') || e.target.classList.contains('question')) {
            const testContainer = e.target.closest('.create-test');
            if (!testContainer) return;

            const questionsContainer = testContainer.querySelector('#test_inputs');
            if (!questionsContainer) return;

            // Считаем текущее количество вопросов
            const questionCount = questionsContainer.querySelectorAll('.create-test__question').length;

            // Создаем HTML с правильным индексом
            const newQuestionHTML = `
            <div class="create-test__question">
                <div class="input-group">
                    <label class="input-group__label">Вопрос</label>
                    <input type="text" class="input-group__input" placeholder="Введите вопрос" 
                           name="questions[${questionCount}][text]" id="test_name">
                </div>
                
                <label class="input-group__label">Варианты ответов</label>
                <div class="input-group answers">
                    <input type="text" class="input-group__input" placeholder="Ответ 1" 
                           name="questions[${questionCount}][answers][]">
                    <input type="text" class="input-group__input" placeholder="Ответ 2" 
                           name="questions[${questionCount}][answers][]">
                    <input type="text" class="input-group__input" placeholder="Ответ 3" 
                           name="questions[${questionCount}][answers][]">
                    <input type="text" class="input-group__input" placeholder="Ответ 4" 
                           name="questions[${questionCount}][answers][]">
                </div>
                
                <div class="input-group">
                    <label class="input-group__label">Правильный ответ (1-4)</label>
                    <input type="number" class="input-group__input" min="1" max="4" 
                           name="questions[${questionCount}][correct_answer]">
                </div>
            </div>
        `;

            questionsContainer.insertAdjacentHTML('beforeend', newQuestionHTML);
        }
    });
</script>

<script>
    document.querySelector('#showInfo').addEventListener('click', function() {
        document.querySelector('#questions_info').style.display = 'block'
        this.style.display = 'none';
    })
    document.querySelector('#hide_questions_info').addEventListener('click', function() {
        document.querySelector('#questions_info').style.display = 'none'
        document.querySelector('#showInfo').style.display = 'block'
    })
</script>

<script>
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

                const test_name = document.querySelector("#test_name");
                if (test_name) {
                    test_name.value = data.data.test_name || "";
                }

                const questions = data.data.questions || [];

                generate_button.addEventListener("click", () => {
                    const addTestButton = document.querySelector('#add_test');

                    addTestButton.click();

                    setTimeout(() => {}, 100);

                    const test_name = document.querySelector("#test_name");
                if (test_name) {
                    test_name.value = data.data.test_name || "";
                }
                
                    const test_inputs = document.querySelector("#test_inputs");
                    
                    console.log(data)
                    questions.forEach((question, index) => {
                        const questionText = question.test_name || "Вопрос";
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
</script>

<script src="/public/assets/js/mask.js?v=<?= time() ?>"> </script>
<script src="/public/assets/js/files_more.js"> </script>
<script src="/public/assets/js/show_message.js?v=<?= time() ?>"> </script>