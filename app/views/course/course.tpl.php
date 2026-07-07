<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>
<div class="course-header">
    <div class="course-header__texts">
        <h2 class="course-header__title"> Курс - <?= $courseData['course_name'] ?></h2>
        <p class="course-header__subtitle"> <?= $courseData['course_description'] ?> </p>
        <a href="/channel/?id=<?= h($courseData['author_id']) ?>">
            <div class="course-header__author">
                <div class="course-header__avatar"> <img src="<?= $courseData['avatar_url'] ?>" alt="avatar"> </div>
                <h2 class="course-header__name"> <?= $courseData['channel_name'] ?> </h2>
            </div>
        </a>
        <?php if (isset($_SESSION['user'])): ?>
            <div class="save__course">
                <form id="save-course-form" action="/course/save" method="post">
                    <!-- Скрытые поля для передачи данных -->
                    <input type="hidden" name="course_id" value="<?= $courseData['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">


                    <button type="submit" id="save-course-btn" style="display: flex; align-items:center; gap:10px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                            <path <?php if (!$hasSaved) {
                                        echo "fill='CurrentColor'";
                                    } else {
                                        echo "fill='#ff0000'";
                                    } ?> fill-rule="evenodd" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                        </svg>
                        <span id="save-text"> <?php if (!$hasSaved) {
                                                    echo "Сохранить курс";
                                                } else {
                                                    echo "Удалить из сохраненых";
                                                } ?> </span>
                        <span id="save-loading" style="display: none;">Сохранение...</span>
                    </button>
                </form>
            </div>
            <div class="course-progress">
                <span>Пройдено уроков: <?= h($completedCount) ?>/<?= h($lessonsCount) ?> </span>
                <progress max="<?= h($lessonsCount) ?>" value="<?= h($completedCount) ?>"></progress>
            </div>
            <?php if ($completedCount == $lessonsCount && !$hasSerteficate): ?>
                <button class="lesson-header__button" style="max-width: 250px" id="get_button"> Получить сертификат </button>
            <?php elseif ($hasSerteficate): ?>
                <a href="/serteficate/?id=<?= $hasSerteficate['id'] ?>"><button class="lesson-header__button" style="max-width: 250px" id="show_button"> К сертификату </button></a>
            <?php endif; ?>
        <?php else: ?>
            <h2 class="logiAction"> Чтобы сохранять прогресс и курсы, <a href="/login" class="logiAction__a"> Войдите </a></h2>
        <?php endif; ?>
    </div>
    <div class="course-header__preview">
        <img src="<?= $courseData['preview_url'] ?>" alt="course-preview">
    </div>
</div>
<?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['id'] == $courseData['author_id'])): ?>
    <div class="course-status-history">
        <div class="status-history__header" id="statusHistoryHeader">
            <h3 class="status-history__title">История изменений статуса</h3>
            <svg class="status-history__arrow" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
            <span class="status-history__badge"><?= count($status_history ?? []) ?></span>
        </div>

        <div class="status-history__content" id="statusHistoryContent">
            <?php if (!empty($status_history)): ?>
                <div class="status-timeline">
                    <?php foreach ($status_history as $index => $record): ?>
                        <div class="timeline-item <?= $index === 0 ? 'timeline-item--latest' : '' ?>">
                            <div class="timeline-item__dot" style="background-color: <?= getStatusColor($record['new_status']) ?>"></div>
                            <div class="timeline-item__content">
                                <div class="timeline-item__header">
                                    <span class="timeline-item__date"><?= date('d.m.Y H:i', strtotime($record['created_at'])) ?></span>
                                    <?php if ($record['admin_name']): ?>
                                        <div class="timeline-item__admin">
                                            <img src="<?= h($record['admin_avatar'] ?? '/public/assets/images/default-avatar.png') ?>" alt="" class="timeline-item__admin-avatar">
                                            <span><?= h($record['admin_name']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="timeline-item__statuses">
                                    <?php if ($record['old_status']): ?>
                                        <span class="status-badge status-badge--old" style="background-color: <?= getStatusColor($record['old_status']) ?>">
                                            <?= swith_status($record['old_status']) ?>
                                        </span>
                                        <svg class="status-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                    <?php endif; ?>

                                    <span class="status-badge status-badge--new" style="background-color: <?= getStatusColor($record['new_status']) ?>">
                                        <?= swith_status($record['new_status']) ?>
                                    </span>
                                </div>

                                <?php if (!empty($record['comment'])): ?>
                                    <div class="timeline-item__comment">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                        </svg>
                                        <span><?= nl2br(h($record['comment'])) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="status-history__empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <p>История изменений статуса пока пуста</p>
                    <p class="status-history__empty-hint">При изменении статуса курса здесь будет появляться запись</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<div class="course-container">
    <h2 class="course-container__title"> Уроки </h2>
    <div class="course-container__videos">
        <?php foreach ($lessons as $lesson): ?>
            <a href="/lesson/show/?id=<?= $lesson['id'] ?>">
                <div class="course course-r">
                    <?php if (isset($_SESSION['user']) && $completedLessons[$lesson['id']] != NULL): ?>
                        <div class="course-flag__wrapper">
                            <div class="course-flag"> Изучен </div>
                        </div>
                    <?php endif; ?>
                    <div class="course__preview"><img src="<?= $lesson['preview_url'] ?>" alt="lesson_preview"></div>
                    <div class="course__title"> <?= $lesson['title'] ?> </div>
                </div>
            </a>
        <?php endforeach; ?>
        <?php if (empty($lessons)): ?>
            <?php require VIEWS . '/errors/smile_error.tpl.php';
            showerror('=(', 'Уроков нет');
            ?>
        <?php endif; ?>
    </div>
</div>

<script src="/public/assets/js/save_course.js?v=<?= time() ?>"> </script>
<script src="/public/assets/js/show_message.js?v=<?= time() ?>"> </script>
<script>
    function toggleStatusHistory(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const content = document.getElementById('statusHistoryContent');
        const arrow = document.querySelector('.status-history__arrow');

        if (content && arrow) {
            content.classList.toggle('expanded');
            arrow.classList.toggle('rotated');

            // Сохраняем состояние
            try {
                localStorage.setItem('courseStatusHistoryExpanded', content.classList.contains('expanded'));
            } catch (e) {
                console.warn('localStorage not available');
            }
        }
    }

    // Добавляем обработчик через JS вместо onclick в HTML
    document.addEventListener('DOMContentLoaded', function() {
        // Навешиваем обработчик на заголовок истории
        const header = document.getElementById('statusHistoryHeader');
        if (header) {
            header.addEventListener('click', toggleStatusHistory);
        }

        // Инициализация состояния истории
        const content = document.getElementById('statusHistoryContent');
        const arrow = document.querySelector('.status-history__arrow');

        if (content && arrow) {
            try {
                const savedState = localStorage.getItem('courseStatusHistoryExpanded');

                if (savedState === 'true') {
                    content.classList.add('expanded');
                    arrow.classList.add('rotated');
                } else if (savedState === null && content.children.length > 0) {
                    // Проверяем, есть ли реальные записи (не пустой блок)
                    const hasRecords = content.querySelector('.timeline-item') !== null;
                    if (hasRecords) {
                        content.classList.add('expanded');
                        arrow.classList.add('rotated');
                    }
                }
            } catch (e) {
                console.warn('localStorage not available');
            }
        }

        // Код для получения сертификата
        const get_button = document.querySelector('#get_button');
        const main_container = document.querySelector('.container');

        if (get_button && main_container) {
            get_button.addEventListener('click', function() {
                // Вставляем форму
                main_container.insertAdjacentHTML('beforebegin', `
            <div class="formWrapper">
                <form action="/sertificate/get" class="formCertificate" method="POST">
                    <h2 class="formCertificate__title">Получение сертификата</h2>
                    <p class="formCertificate__description">
                    Для получения сертификата необходимо ввести имя и фамилию, которые будут на нем отображаться, но он все равно будет привязан к вашему аккаунту ВруБ
                    </p>
        
                    <div class="formCertificate__field">
                        <label for="certeficate_name" class="formCertificate__label">Введите имя и фамилию</label>
                        <input type="text" placeholder="Иван Иванов" id="certeficate_name" class="formCertificate__input" name="name" min-length="6" required data-mask="cyrillic">
                        <input type="hidden" name="course_id" value="${new URL(window.location.href).searchParams.get('id')}">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    </div>
                    <button type="submit" id="submitCertificate" class="formCertificate__button">Получить сертификат</button>
                </form>
            </div>
        `);

                const form_wrapper = document.querySelector('.formWrapper');
                const form = document.querySelector('.formCertificate');

                // Закрытие при клике на wrapper (фон), но не на форму
                form_wrapper.addEventListener('click', function(event) {
                    if (event.target === form_wrapper) {
                        form_wrapper.remove();
                    }
                });

                form.addEventListener('submit', async function(event) {
                    event.preventDefault();

                    try {
                        const form_data = new FormData(form);

                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: form_data,
                        });

                        // Проверяем статус ответа
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            // Сервер вернул не JSON, вероятно, ошибка 404 или 500
                            const text = await response.text();
                            console.error('Сервер вернул не JSON:', text.substring(0, 200));
                            throw new Error('Сервер вернул некорректный ответ');
                        }

                        const data = await response.json();

                        // Обрабатываем ответ
                        if (data.success) {
                            if (typeof showMessage === 'function') {
                                showMessage(data.message || 'Успешно!', 'success');
                            }
                            window.location.href = `/sertificate/?id=${data.sertificate_id}`;
                        } else {
                            if (typeof showMessage === 'function') {
                                showMessage(data.message || 'Произошла ошибка', 'error');
                            }
                        }
                    } catch (error) {
                        let errorMessage = 'Произошла ошибка';
                        if (error.message.includes('404')) {
                            errorMessage = 'Страница не найдена (404). Проверьте URL.';
                        } else if (error.message.includes('JSON')) {
                            errorMessage = 'Ошибка обработки ответа сервера';
                        }

                        if (typeof showMessage === 'function') {
                            showMessage(errorMessage, 'error');
                        } else {
                            alert(errorMessage);
                        }
                    }
                });
            });
        }
    });
</script>
<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>