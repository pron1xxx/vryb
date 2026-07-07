<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>
<div class="lesson-container">
    <div class="lesson-header">
        <h2 class="lesson-header__title"><?= htmlspecialchars($lessonData['title']) ?></h2>
        <div class="buttons" style="display:flex;gap:13px;flex-wrap:wrap;">
            <a href="/course/show/?id=<?= $lessonData['course_id'] ?>"><button class="lesson-header__button">К курсу</button></a>
            <?php if (isset($_SESSION['user'])): ?>
                <?php if (!$hasCompleteLesson): ?>
                    <form action="/lesson/complete/" method="post" class="complete" id=<?= $lessonData['course_id'] ?>>
                        <input type="hidden" name="lesson_id" value="<?= $lessonData['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button class="lesson-header__button">Пометить как пройденный</button>
                    </form>
                <?php else: ?>
                    <div class="course-flag"> Изучен </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="lesson">
        <video
            controls
            controlsList="nodownload"
            oncontextmenu="return false;"
            poster="<?= htmlspecialchars($lessonData['preview_url'] ?? '') ?>"
            width="100%"
            preload="none">
            <source src="<?= h($lessonData['video_url']) ?>" type="video/mp4">
            Ваш браузер не поддерживает встроенные видео :(
        </video>

        <p class="lesson__subtitle"><?= htmlspecialchars($lessonData['description'] ?? 'Описание урока') ?></p>

        <?php if (isset($_SESSION['user'])): ?>
            <div class="lesson-files">
                <?php if (!empty($files_data)): ?>
                    <?php
                    // Подготовка данных для рендеринга
                    $sections = [
                        'lect' => [
                            'title' => 'Лекционные материалы',
                            'icon' => '📚',
                            'badge' => 'лекция'
                        ],
                        'prak' => [
                            'title' => 'Практические материалы',
                            'icon' => '🔧',
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
                                        <span class="files-count">(<?= count($section['files']) ?>)</span>
                                    </h3>
                                </div>

                                <div class="files-list">
                                    <?php foreach ($section['files'] as $file): ?>
                                        <div class="file-item" data-file-id="<?= $file['id'] ?>">
                                            <div class="file-icon">
                                                <?= getFileEmoji($file['file_extension']) ?>
                                            </div>
                                            <div class="file-info">
                                                <div class="file-header">
                                                    <span class="file-name"><?= htmlspecialchars($file['original_name']) ?></span>
                                                    <span class="file-badge"><?= $section['badge'] ?></span>
                                                </div>
                                                <div class="file-meta">
                                                    <span class="file-meta-item">📦 <?= formatFileSize($file['size']) ?></span>
                                                    <span class="file-meta-item">📄 <?= strtoupper($file['file_extension']) ?></span>
                                                </div>

                                                <!-- Кнопка для пересказа -->
                                                <button class="file-action file-action--summarize"
                                                    onclick="summarizeFile(<?= $file['id'] ?>, this)"
                                                    title="Получить краткий пересказ">
                                                    🤖 Краткий пересказ
                                                </button>

                                                <!-- Контейнер для результата пересказа -->
                                                <div class="file-summary-container" id="summary-<?= $file['id'] ?>" style="display: none;">
                                                    <div class="file-summary-loader" id="loader-<?= $file['id'] ?>">
                                                        ⏳ Генерация пересказа...
                                                    </div>
                                                    <div class="file-summary-content" id="content-<?= $file['id'] ?>"></div>
                                                </div>
                                            </div>

                                            <div class="file-actions">
                                                <a href="<?= htmlspecialchars($file['file_url']) ?>" class="file-action file-action--download" download>⬇️</a>
                                                <!-- Кнопка просмотра файла (возвращаем) -->
                                                <a href="<?= htmlspecialchars($file['file_url']) ?>" class="file-action file-action--preview" target="_blank">👁️</a>
                                            </div>
                                        </div>
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
        <?php else: ?>
            <h2 class="logiAction"> Чтобы просматривать файлы, <a href="/login" class="logiAction__a"> Войдите </a></h2>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) && $test != false && isset($questionsJson)): ?>
            <a href="/test/result/?id=<?= h($test['id']) ?>" style="align-self: start;">
                <button class="lesson-header__button test-container__button" style="max-width: 300px;">
                    Результаты прохождения тестов
                </button>
            </a>
            <div class="test-container">
                <h2 class="lesson-header__title"><?= h($test['test_title']) ?></h2>
                <p class="test-container__subtitle">Посмотрите урок и пройдите тест, чтобы закрепить полученные знания</p>
                <button class="lesson-header__button test-container__button" id="start_test">Начать тест</button>
            </div>

            <!-- Форма для сбора ответов (сразу создаем) -->
            <form id="answers_form" method="POST" action="/test/complete/?id=<?= $test['id'] ?>" style="display: none;">
                <!-- Сюда будут добавляться скрытые поля -->
            </form>
        <?php else: ?>
            <?php if (!isset($_SESSION['user'])): ?>
                <h2 class="logiAction"> Чтобы проходить тесты, <a href="/login" class="logiAction__a"> Войдите </a></h2>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.querySelector('video');

        // Базовая защита
        video.addEventListener('contextmenu', (e) => e.preventDefault());
        video.addEventListener('dragstart', (e) => e.preventDefault());

        document.addEventListener('keydown', (e) => {
            // Блокировка Ctrl+S, F12, PrintScreen
            if ((e.ctrlKey && e.key === 's') || e.key === 'F12' || e.key === 'PrintScreen') {
                e.preventDefault();
            }
        });

        const start_button = document.querySelector("#start_test");
        const testContainer = document.querySelector('.test-container');
        const answersForm = document.getElementById('answers_form');

        if (start_button) {
            start_button.addEventListener('click', function() {
                const questionsData = <?= $questionsJson ?>;

                // Проверка данных
                if (!questionsData || !Array.isArray(questionsData) || questionsData.length === 0) {
                    alert('Нет данных для теста');
                    return;
                }

                console.log('Questions data loaded:', questionsData);

                // Удаляем кнопку и подзаголовок
                start_button.remove();
                const subtitle = document.querySelector('.test-container__subtitle');
                if (subtitle) subtitle.remove();

                // Очищаем форму перед началом теста
                answersForm.innerHTML = '';

                // Рендерим первый вопрос
                renderQuestion(0, questionsData);
            });
        }

        // Добавляем ответ в форму
        function addAnswerToForm(questionId, answerId) {
            console.log('Adding answer to form:', questionId, answerId);

            // Удаляем старый ответ для этого вопроса (если есть)
            const oldInput = answersForm.querySelector(`input[name="question-${questionId}"]`);
            if (oldInput) {
                console.log('Removing old input:', oldInput.name, oldInput.value);
                oldInput.remove();
            }

            // Добавляем новый ответ
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'question-' + questionId;
            input.value = answerId;
            input.id = 'answer-' + questionId;
            answersForm.appendChild(input);

            // Лог для отладки
            console.log('Added input:', {
                name: input.name,
                value: input.value,
                form: answersForm.innerHTML
            });

            return input;
        }

        function renderQuestion(currentIndex, questionsData) {
            const question = questionsData[currentIndex];

            // Определяем структуру данных вопроса
            let questionData, questionId, questionText;

            if (question.question_data) {
                // Формат с question_data
                questionData = question.question_data;
                questionId = questionData.question_id || questionData.id;
                questionText = questionData.question_text;
            } else {
                // Прямой формат
                questionData = question;
                questionId = question.id || question.question_id;
                questionText = question.question_text;
            }

            // Проверяем, что есть ответы
            if (!question.answers || question.answers.length === 0) {
                console.error('No answers for question:', question);
                alert('Ошибка: нет ответов для вопроса');
                return;
            }

            // Проверяем, что есть ID вопроса
            if (!questionId) {
                console.error('No question ID:', question);
                alert('Ошибка: отсутствует ID вопроса');
                return;
            }

            console.log('Rendering question:', {
                index: currentIndex,
                id: questionId,
                text: questionText
            });

            // Создаем HTML для ответов
            let answersHTML = '';
            question.answers.forEach((answer, i) => {
                const answerId = answer.order_index || (i + 1);
                const answerText = answer.answer_text || `Ответ ${i + 1}`;

                answersHTML += `
                        <div class="test__answer" 
                             data-answer-id="${answerId}" 
                             data-question-id="${questionId}">
                            ${answerText}
                        </div>
                    `;
            });

            testContainer.innerHTML = `
                    <div class="test">
                        <div class="test__header">
                            <h2 class="test__title">Вопрос ${currentIndex + 1}</h2>
                            <p class="test__number">
                                <progress value="${currentIndex + 1}" max="${questionsData.length}"></progress>
                                <span>${currentIndex + 1}/${questionsData.length}</span>
                            </p>
                        </div>
                        <p class="test__question">${questionText || 'Вопрос не найден'}</p>
                        <div class="test__answers">
                            ${answersHTML}
                        </div>
                        ${currentIndex === questionsData.length - 1 
                            ? '<button type="button" class="lesson-header__button test__button" id="complete_btn">Завершить тест</button>'
                            : '<button type="button" class="lesson-header__button test__button" id="next_btn">Далее</button>'
                        }
                    </div>
                `;

            // Добавляем обработчики для ответов
            const answerButtons = testContainer.querySelectorAll('.test__answer');
            console.log('Found answer buttons:', answerButtons.length);

            answerButtons.forEach(button => {
                button.addEventListener('click', function() {
                    console.log('Answer clicked:', this);

                    // Снимаем выделение со всех ответов
                    answerButtons.forEach(btn => {
                        btn.classList.remove('active');
                    });

                    // Выделяем выбранный ответ
                    this.classList.add('active');

                    // Сохраняем ответ в форму
                    const questionId = this.dataset.questionId;
                    const answerId = this.dataset.answerId;

                    console.log('Selected:', {
                        questionId,
                        answerId
                    });
                    addAnswerToForm(questionId, answerId);

                    // Проверяем, что input добавился
                    const checkInput = answersForm.querySelector(`#answer-${questionId}`);
                    console.log('Input in form:', checkInput ? 'YES' : 'NO');
                    if (checkInput) {
                        console.log('Input value:', checkInput.value);
                    }
                });
            });

            // Обработчик для кнопки "Далее"
            const nextBtn = testContainer.querySelector('#next_btn');
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    const selectedAnswer = testContainer.querySelector('.test__answer.active');
                    if (!selectedAnswer) {
                        alert('Выберите ответ');
                        return;
                    }

                    // Проверяем, что ответ сохранен в форме
                    const questionId = selectedAnswer.dataset.questionId;
                    const formInput = answersForm.querySelector(`#answer-${questionId}`);
                    if (!formInput) {
                        console.error('Answer not saved in form!');
                        alert('Ошибка: ответ не сохранен');
                        return;
                    }

                    console.log('Moving to next question, current answers:', getFormAnswers());

                    // Переходим к следующему вопросу
                    renderQuestion(currentIndex + 1, questionsData);
                });
            }

            // Обработчик для кнопки "Завершить"
            const completeBtn = testContainer.querySelector('#complete_btn');
            if (completeBtn) {
                completeBtn.addEventListener('click', function() {
                    const selectedAnswer = testContainer.querySelector('.test__answer.active');
                    if (!selectedAnswer) {
                        alert('Выберите ответ');
                        return;
                    }

                    // Проверяем, что ответ сохранен в форме
                    const questionId = selectedAnswer.dataset.questionId;
                    const formInput = answersForm.querySelector(`#answer-${questionId}`);
                    if (!formInput) {
                        console.error('Answer not saved in form!');
                        alert('Ошибка: ответ не сохранен');
                        return;
                    }

                    // Показываем все сохраненные ответы
                    console.log('All answers before submit:', getFormAnswers());

                    // Проверяем количество ответов
                    const totalAnswers = answersForm.querySelectorAll('input').length;
                    console.log('Total answers in form:', totalAnswers);

                    if (totalAnswers < questionsData.length) {
                        if (!confirm(`Вы ответили на ${totalAnswers} из ${questionsData.length} вопросов. Завершить тест?`)) {
                            return;
                        }
                    }

                    // Отправляем форму
                    console.log('Submitting form...');
                    answersForm.submit();
                });
            }
        }

        // Вспомогательная функция для отладки
        function getFormAnswers() {
            const inputs = answersForm.querySelectorAll('input[type="hidden"]');
            const answers = {};
            inputs.forEach(input => {
                answers[input.name] = input.value;
            });
            return answers;
        }
    });
</script>
<script>
    async function summarizeFile(fileId, button) {
        const container = document.getElementById(`summary-${fileId}`);
        const loader = document.getElementById(`loader-${fileId}`);
        const content = document.getElementById(`content-${fileId}`);

        if (container.style.display === 'none') {
            container.style.display = 'block';
            content.innerHTML = '';
            loader.style.display = 'block';
            loader.innerHTML = '⏳ Извлечение текста из файла...';
            button.disabled = true;
            button.textContent = '⏳ Обработка...';

            try {
                // Создаем меню выбора типа пересказа
                const typeMenu = document.createElement('div');
                typeMenu.className = 'summary-type-menu';
                typeMenu.innerHTML = `
                    <div class="summary-type-menu-content">
                        <button onclick="generateSummary(${fileId}, 'short')">Короткий</button>
                        <button onclick="generateSummary(${fileId}, 'medium')" class="active">Средний</button>
                        <button onclick="generateSummary(${fileId}, 'detailed')">Подробный</button>
                    </div>
                `;

                loader.style.display = 'none';
                content.appendChild(typeMenu);

            } catch (error) {
                loader.style.display = 'none';
                content.innerHTML = `<div class="summary-error">❌ Ошибка: ${error.message}</div>`;
            }

            button.disabled = false;
            button.textContent = '🤖 Краткий пересказ';
        } else {
            container.style.display = 'none';
            button.textContent = '🤖 Краткий пересказ';
        }
    }

    // Функция для генерации пересказа
    async function generateSummary(fileId, type) {
        const container = document.getElementById(`summary-${fileId}`);
        const content = document.getElementById(`content-${fileId}`);
        const loader = document.getElementById(`loader-${fileId}`);

        // Показываем загрузку
        content.innerHTML = '';
        loader.style.display = 'block';
        loader.innerHTML = '⏳ Генерация пересказа...';

        try {
            const formData = new FormData();
            formData.append('file_id', fileId);
            formData.append('summary_type', type);
            formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');

            const response = await fetch('/retelling', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            loader.style.display = 'none';

            if (data.success) {
                displaySummary(fileId, data, type);
            } else {
                throw new Error(data.message || 'Ошибка генерации');
            }
        } catch (error) {
            loader.style.display = 'none';
            content.innerHTML = `
                <div class="summary-error">
                    ❌ Ошибка: ${error.message}
                    <button onclick="generateSummary(${fileId}, '${type}')">Повторить</button>
                    <button onclick="closeSummary(${fileId})">Закрыть</button>
                </div>
            `;
        }
    }

    // Функция отображения результата
    function displaySummary(fileId, data, type) {
        const content = document.getElementById(`content-${fileId}`);

        const typeNames = {
            'short': 'Короткий',
            'medium': 'Средний',
            'detailed': 'Подробный'
        };

        content.innerHTML = `
            <div class="summary-result">
                <div class="summary-result-header">
                    <span>📝 ${typeNames[type] || type} пересказ</span>
                    ${data.cached ? '<span class="summary-cached">из кэша</span>' : ''}
                    <button class="summary-close" onclick="closeSummary(${fileId})">✖</button>
                </div>
                <div class="summary-result-content">
                    ${data.summary.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
    }

    function closeSummary(fileId) {
        const container = document.getElementById(`summary-${fileId}`);
        container.style.display = 'none';

        const button = document.querySelector(`button[onclick^="summarizeFile(${fileId}"]`);
        if (button) {
            button.textContent = '🤖 Краткий пересказ';
        }
    }
</script>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>

<script src="/public/assets/js/show_message.js?v=<?= time() ?>"></script>
<script src="/public/assets/js/complete_lesson.js?v=<?= time() ?>"></script>