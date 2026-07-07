<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>

<div class="adminHeader">
    <h2 class="adminHeader__title">Панель администратора</h2>

    <form action="/search" class="search search__admin" id="search_form">
        <input type="text" class="search__input" name="search" placeholder="Поиск" id="search_input">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <button class="search__button" type="submit" style="top: 0px">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor" d="M9.5 16q-2.725 0-4.612-1.888T3 9.5t1.888-4.612T9.5 3t4.613 1.888T16 9.5q0 1.1-.35 2.075T14.7 13.3l5.6 5.6q.275.275.275.7t-.275.7t-.7.275t-.7-.275l-5.6-5.6q-.75.6-1.725.95T9.5 16m0-2q1.875 0 3.188-1.312T14 9.5t-1.312-3.187T9.5 5T6.313 6.313T5 9.5t1.313 3.188T9.5 14" />
            </svg>
        </button>
    </form>

    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-card__value"><?= $total_courses ?? 0 ?></div>
            <div class="stat-card__label">Всего курсов</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__value"><?= $moderation_courses ?? 0 ?></div>
            <div class="stat-card__label">На модерации</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__value"><?= $total_users ?? 0 ?></div>
            <div class="stat-card__label">Пользователей</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__value"><?= $new_users_today ?? 0 ?></div>
            <div class="stat-card__label">За сегодня</div>
        </div>
    </div>

    <div class="admin-tabs">
        <button class="admin-tab active" data-tab="all-courses">Все курсы</button>
        <button class="admin-tab" data-tab="moderation">На модерации</button>
        <button class="admin-tab" data-tab="users">Пользователи</button>
    </div>
</div>

<div class="admin-section active" id="all-courses">
    <div class="admin-section__header">
        <h3 class="admin-section__title">Все курсы</h3>
        <div class="admin-section__filters">
            <select class="admin-section__select" id="filter-status">
                <option value="all">Все статусы</option>
                <option value="public">Публичный</option>
                <option value="moderation">На модерации</option>
                <option value="development">В разработке</option>
                <option value="hidden">Скрытый</option>
            </select>
        </div>
    </div>

    <div class="courses" id="courses">
        <?php foreach ($courses as $course): ?>
            <div class="course" data-category="<?php swith_status($course['status']); ?>" data-status="<?= $course['status'] ?>">
                <a href="/course/show/?id=<?= htmlspecialchars($course['id']) ?>">
                    <div class="course__preview">
                        <img src="<?= htmlspecialchars($course['preview_url'] ?? '') ?>" alt="course_preview">
                    </div>
                </a>
                <h2 class="course__title"> <?= htmlspecialchars($course['course_name']) ?> </h2>
                <div class="course__authorWrapper adminAuthor">
                    <a href="/channel/?id=<?= htmlspecialchars($course['author_id']) ?>" class="course__author">
                        <?= htmlspecialchars($course['channel_name'] ?? '') ?>
                    </a>
                </div>

                <form action="/admin/change/status" method="post" class="change">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <select name="status">
                        <option value="<?= $course['status'] ?>" selected>
                            <?= swith_status($course['status']); ?>
                        </option>
                        <?php foreach ($status_array as $status): ?>
                            <?php if ($status == $course['status']) continue; ?>
                            <option value="<?= $status ?>">
                                <?= swith_status($status); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="adminCourse__button">Изменить статус</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="/main/show/more" method="post" id="show_more" style="margin-top: 53px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <input type="hidden" name="counts_courses" id="count" value="2">
        <input type="hidden" name="search_str" id="search_str">
        <button type="submit" class="moreButton">Показать еще</button>
    </form>
</div>

<div class="admin-section" id="moderation">
    <div class="admin-section__header">
        <h3 class="admin-section__title">Курсы на модерации</h3>
        <span class="admin-section__count"><?= count($moderation_courses_list ?? []) ?></span>
    </div>

    <?php if (!empty($moderation_courses_list)): ?>
        <div class="moderation-list">
            <?php foreach ($moderation_courses_list as $course): ?>
                <div class="moderation-item">
                    <div class="moderation-item__preview">
                        <img src="<?= h($course['preview_url']) ?>" alt="">
                    </div>
                    <div class="moderation-item__info">
                        <a href="/course/show/?id=<?= $course['id'] ?>">
                            <h4 class="moderation-item__title"><?= h($course['course_name']) ?></h4>
                        </a>
                        <a href="/channel/?id=<?= $course['author_id'] ?>">
                            <p class="moderation-item__author">Автор: <?= h($course['channel_name']) ?></p>
                        </a>
                        <p class="moderation-item__date">Создан: <?= date('d.m.Y', strtotime($course['created_at'])) ?></p>
                    </div>
                    <div class="moderation-item__actions">
                        <form action="/admin/change/status" method="post" class="moderation-form approve-form">
                            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="status" value="public">
                            <button class="moderation-btn moderation-btn--approve">Одобрить</button>
                        </form>
                        <button class="moderation-btn moderation-btn--reject" onclick="showCommentForm(<?= $course['id'] ?>, '<?= $_SESSION['csrf_token'] ?>')">На доработку</button>
                        <a href="/course/show/?id=<?= $course['id'] ?>" class="moderation-btn moderation-btn--view">Просмотр</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-message">
            <p class="empty-message__text">Все курсы проверены</p>
        </div>
    <?php endif; ?>
</div>

<div class="admin-section" id="users">
    <div class="admin-section__header">
        <h3 class="admin-section__title">Пользователи</h3>
        <div class="admin-section__filters">
            <input type="text" class="admin-section__search" placeholder="Поиск пользователей..." id="user-search">
        </div>
    </div>

    <div class="users-table">
        <div class="users-table__header">
            <div class="users-table__cell">ID</div>
            <div class="users-table__cell">Аватар</div>
            <div class="users-table__cell">Имя</div>
            <div class="users-table__cell">Email</div>
            <div class="users-table__cell">Роль</div>
            <div class="users-table__cell" style="margin-left: 13px">Действия</div>
        </div>

        <div class="users-table__body" id="users-list">
            <?php foreach ($users as $user): ?>
                <div class="users-table__row" data-user-id="<?= $user['id'] ?>" data-user-status="<?= $user['status'] ?? 'active' ?>">
                    <div class="users-table__cell"><?= $user['id'] ?></div>
                    <div class="users-table__cell">
                        <img src="<?= htmlspecialchars($user['avatar_url'] ?? '/public/assets/images/default-avatar.png') ?>"
                            alt="avatar" class="user-avatar">
                    </div>
                    <div class="users-table__cell"><?= htmlspecialchars($user['channel_name']) ?></div>
                    <div class="users-table__cell"><?= htmlspecialchars($user['email']) ?></div>
                    <div class="users-table__cell">
                        <span class="user-role role-<?= $user['role'] ?>">
                            <?= $user['role'] == 'admin' ? 'Админ' : 'Пользователь' ?>
                        </span>
                    </div>
                    <div class="users-table__cell">
                        <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                            <?php if ($user['status'] != NULL): ?>
                                <button class="user-action__btn user-action__btn--unblock"
                                    onclick="unblockUser(<?= $user['id'] ?>)">
                                    Разблокировать
                                </button>
                            <?php else: ?>
                                <button class="user-action__btn user-action__btn--block"
                                    onclick="showBlockForm(<?= $user['id'] ?>)">
                                    Заблокировать
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="/public/assets/js/main.js?v=<?= time() ?>"></script>
<script src="/public/assets/js/show_message.js?v=<?= time() ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Переключение табов
        const tabs = document.querySelectorAll('.admin-tab');
        const sections = document.querySelectorAll('.admin-section');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetId = this.dataset.tab;
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                sections.forEach(section => {
                    section.classList.remove('active');
                    if (section.id === targetId) section.classList.add('active');
                });
            });
        });

        // Фильтрация курсов по статусу
        const filterSelect = document.getElementById('filter-status');
        if (filterSelect) {
            filterSelect.addEventListener('change', function() {
                const status = this.value;
                document.querySelectorAll('#all-courses .course').forEach(course => {
                    course.style.display = (status === 'all' || course.dataset.status === status) ? 'flex' : 'none';
                });
            });
        }

        // Поиск пользователей
        const userSearch = document.getElementById('user-search');
        if (userSearch) {
            userSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('#users-list .users-table__row').forEach(row => {
                    const name = row.querySelector('.users-table__cell:nth-child(3)').textContent.toLowerCase();
                    const email = row.querySelector('.users-table__cell:nth-child(4)').textContent.toLowerCase();
                    row.style.display = (name.includes(searchTerm) || email.includes(searchTerm)) ? 'flex' : 'none';
                });
            });
        }

        // Обработка форм одобрения
        document.querySelectorAll('.approve-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const data = await response.json();

                    if (data.success) {
                        showMessage('Курс одобрен', 'success');
                        this.closest('.moderation-item').remove();
                        updateModerationCount();
                    } else {
                        showMessage(data.message || 'Ошибка', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    showMessage('Ошибка соединения', 'error');
                }
            });
        });
    });

    // Показать форму комментария для отправки на доработку
    function showCommentForm(courseId) {
        const csrfToken = '<?= $_SESSION['csrf_token'] ?>';
        const container = document.querySelector('.container');

        container.insertAdjacentHTML('beforebegin', `
            <div class="formWrapper">
                <form class="formCertificate" id="commentForm">
                    <h2 class="formCertificate__title">Комментарий к курсу</h2>
                    <p class="formCertificate__description">
                        Укажите, что нужно исправить в курсе перед публикацией
                    </p>
                    <div class="formCertificate__field">
                        <label class="formCertificate__label">Комментарий для автора</label>
                        <textarea name="comment" class="formCertificate__input" required placeholder="Опишите необходимые изменения..." data-mask="text" minlength="20" required></textarea>
                    </div>
                    <input type="hidden" name="course_id" value="${courseId}">
                    <input type="hidden" name="csrf_token" value="${csrfToken}">
                    <input type="hidden" name="status" value="development">
                    <button type="submit" class="formCertificate__button">Отправить на доработку</button>
                </form>
            </div>
        `);

        const wrapper = document.querySelector('.formWrapper');
        const form = document.getElementById('commentForm');

        wrapper.addEventListener('click', e => {
            if (e.target === wrapper) wrapper.remove();
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch('/admin/change/status', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();

                if (data.success) {
                    showMessage('Курс отправлен на доработку', 'success');
                    wrapper.remove();
                    document.querySelector(`.moderation-item:has(input[value="${courseId}"])`)?.remove();
                    updateModerationCount();
                } else {
                    showMessage(data.message || 'Ошибка', 'error');
                }
            } catch (error) {
                console.error(error);
                showMessage('Ошибка соединения', 'error');
            }
        });
    }

    function showBlockForm(userId) {
        const csrfToken = '<?= $_SESSION['csrf_token'] ?>';
        const container = document.querySelector('.container');

        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

        container.insertAdjacentHTML('beforebegin', `
            <div class="formWrapper">
                <form class="formCertificate" id="blockForm">
                    <h2 class="formCertificate__title">Блокировка пользователя</h2>
                    <p class="formCertificate__description">
                        Укажите причину блокировки и срок
                    </p>
                    <div class="formCertificate__field">
                        <label class="formCertificate__label">Заблокировать до</label>
                        <input type="datetime-local" name="banned_before" class="formCertificate__input" required 
                            min="${minDateTime}" data-mask="text">
                        
                        <label class="formCertificate__label">Причина блокировки</label>
                        <textarea name="comment" class="formCertificate__input" required 
                            placeholder="Опишите причину блокировки..." minlength="5" maxlength="1000" data-mask="text"></textarea>
                    </div>
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="csrf_token" value="${csrfToken}">
                    <button type="submit" class="formCertificate__button">Заблокировать пользователя</button>
                    <button type="button" class="formCertificate__button formCertificate__button--cancel" onclick="this.closest('.formWrapper').remove()" style="margin-top: 9px;">Отмена</button>
                </form>
            </div>
        `);

        const wrapper = document.querySelector('.formWrapper');
        const form = document.getElementById('blockForm');

        wrapper.addEventListener('click', e => {
            if (e.target === wrapper) wrapper.remove();
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Показываем индикатор загрузки
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Блокировка...';
            submitBtn.disabled = true;

            const formData = new FormData(this);

            try {
                const response = await fetch('/admin/block', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text.substring(0, 200));
                    throw new Error('Сервер вернул некорректный ответ');
                }

                const data = await response.json();

                if (data.success) {
                    showMessage(data.message || 'Пользователь заблокирован', 'success');
                    wrapper.remove();

                    updateUserBlockStatus(userId, 'blocked');
                } else {
                    showMessage(data.message || 'Ошибка при блокировке', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Ошибка соединения с сервером', 'error');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    async function unblockUser(userId) {
        const csrfToken = '<?= $_SESSION['csrf_token'] ?>';
        const userRow = document.querySelector(`.users-table__row[data-user-id="${userId}"]`);
        const actionBtn = userRow?.querySelector('.user-action__btn');

        // Показываем индикатор загрузки на кнопке
        const originalText = actionBtn.textContent;
        actionBtn.textContent = 'Разблокировка...';
        actionBtn.disabled = true;

        try {
            const response = await fetch('/admin/unblock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    csrf_token: csrfToken
                })
            });

            // Проверяем тип ответа
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text.substring(0, 200));
                throw new Error('Сервер вернул некорректный ответ');
            }

            const data = await response.json();

            if (data.success) {
                showMessage('Пользователь разблокирован', 'success');
                updateUserBlockStatus(userId, 'active');
            } else {
                showMessage(data.message || 'Ошибка при разблокировке', 'error');
                // Возвращаем кнопку в исходное состояние
                actionBtn.textContent = originalText;
                actionBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Ошибка соединения с сервером', 'error');
            actionBtn.textContent = originalText;
            actionBtn.disabled = false;
        }
    }

    // Функция для обновления статуса пользователя в UI
    function updateUserBlockStatus(userId, status) {
        const userRow = document.querySelector(`.users-table__row[data-user-id="${userId}"]`);
        if (!userRow) return;

        const actionBtn = userRow.querySelector('.user-action__btn');
        if (!actionBtn) return;

        // Обновляем data-status на строке пользователя
        userRow.dataset.userStatus = status;

        if (status === 'blocked') {
            // Меняем на кнопку разблокировки
            actionBtn.textContent = 'Разблокировать';
            actionBtn.classList.remove('user-action__btn--block');
            actionBtn.classList.add('user-action__btn--unblock');
            actionBtn.disabled = false;

            // Меняем обработчик клика
            actionBtn.setAttribute('onclick', `unblockUser(${userId})`);
        } else {
            // Меняем на кнопку блокировки
            actionBtn.textContent = 'Заблокировать';
            actionBtn.classList.remove('user-action__btn--unblock');
            actionBtn.classList.add('user-action__btn--block');
            actionBtn.disabled = false;

            // Меняем обработчик клика
            actionBtn.setAttribute('onclick', `showBlockForm(${userId})`);
        }
    }

    // Функция для обновления счетчика модерации
    function updateModerationCount() {
        const countSpan = document.querySelector('.admin-section__count');
        if (countSpan) {
            const currentCount = parseInt(countSpan.textContent) || 0;
            countSpan.textContent = Math.max(0, currentCount - 1);
        }
    }
</script>

<script src="/public/assets/js/mask.js?v=<?= time() ?>"> </script>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>