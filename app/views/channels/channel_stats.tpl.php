<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>

<div class="channel-stats-page">
    <!-- Шапка канала -->
    <div class="channel-header">
        <div class="channel-header__main">
            <div class="channel-header__avatar">
                <img src="<?= h($channel['avatar_url'] ?? '/public/assets/images/default-avatar.png') ?>" alt="avatar">
            </div>
            <div class="channel-header__info">
                <h1 class="channel-header__name"><?= h($channel['channel_name']) ?></h1>
                <p class="channel-header__username">@<?= h($channel['id']) ?></p>
                <div class="channel-header__meta">
                    <span class="channel-meta__item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                        </svg>
                        На платформе с <?= date('d.m.Y', strtotime($channel['created_at'] ?? 'now')) ?>
                    </span>
                    <span class="channel-meta__item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <?= $subscribers_count ?? 0 ?> сохранили ваш профиль
                    </span>
                </div>
            </div>
        </div>

        <!-- Быстрая статистика -->
        <div class="channel-header__stats">
            <div class="stat-badge">
                <div class="stat-badge__value"><?= $total_courses ?? 0 ?></div>
                <div class="stat-badge__label">курсов</div>
            </div>
            <div class="stat-badge">
                <div class="stat-badge__value"><?= $total_lessons ?? 0 ?></div>
                <div class="stat-badge__label">уроков</div>
            </div>
            <div class="stat-badge">
                <div class="stat-badge__value"><?= $total_files ?? 0 ?></div>
                <div class="stat-badge__label">файлов</div>
            </div>
        </div>
    </div>

    <!-- Основная статистика -->
    <div class="stats-grid">
        <!-- Карточка сохранений -->
        <div class="stat-card stat-card--saved">
            <div class="stat-card__header">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <h3 class="stat-card__title">Сохранения</h3>
            </div>
            <div class="stat-card__value-wrapper">
                <span class="stat-card__value counter" data-target="<?= $total_saved ?? 0 ?>">0</span>
            </div>
            <p class="stat-card__description">пользователей сохранили ваши курсы</p>
        </div>

        <div class="stat-card stat-card--views">
            <div class="stat-card__header">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <h3 class="stat-card__title">Прохождения</h3>
            </div>
            <div class="stat-card__value-wrapper">
                <span class="stat-card__value counter" data-target="<?= $total_finish ?? 0 ?>">0</span>
            </div>
            <p class="stat-card__description">всего прохождений уроков</p>
        </div>

        <!-- Карточка сертификатов -->
        <div class="stat-card stat-card--certificates">
            <div class="stat-card__header">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M19 11a3 3 0 0 1 2 5.236v4.955a.5.5 0 0 1-.724.447L19 21l-1.276.638a.5.5 0 0 1-.724-.447v-4.955A3 3 0 0 1 19 11m1-7a2 2 0 0 1 2 2v4a5 5 0 0 0-7 7v3H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"></path>
                </svg>
                <h3 class="stat-card__title">Сертификаты</h3>
            </div>
            <div class="stat-card__value-wrapper">
                <span class="stat-card__value counter" data-target="<?= $total_certificates ?? 0 ?>">0</span>
            </div>
            <p class="stat-card__description">выдано сертификатов</p>
        </div>
    </div>

    <section class="courses-section">
        <div class="section-header">
            <h2 class="section-title">Ваши курсы</h2>
            <div class="section-filters">
                <select class="filter-select" id="courseFilter">
                    <option value="all">Все курсы</option>
                    <option value="popular">По популярности</option>
                    <option value="newest">Сначала новые</option>
                </select>
            </div>
        </div>

        <div class="courses-analytics">
            <?php if (empty($courses)): ?>
                <div class="empty-state">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <h3>У вас пока нет курсов</h3>
                    <p>Создайте свой первый курс и начните делиться знаниями</p>
                    <a href="/create" class="btn btn--primary">Создать курс</a>
                </div>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-analytics-card" data-course-id="<?= $course['id'] ?>">
                        <!-- Заголовок курса -->
                        <div class="course-analytics__header">
                            <div class="course-analytics__preview">
                                <img src="<?= h($course['preview_url'] ?? '/public/assets/images/default-course.jpg') ?>" alt="">
                            </div>
                            <div class="course-analytics__info">
                                <h3 class="course-analytics__title">
                                    <a href="/course/show/?id=<?= $course['id'] ?>"><?= h($course['course_name']) ?></a>
                                </h3>
                                <p class="course-analytics__date">Создан: <?= $course['created_at'] ?></p>
                                <span class="course-analytics__status status-<?= $course['status'] ?>">
                                    <?= swith_status($course['status']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Краткая статистика курса -->
                        <div class="course-analytics__stats">
                            <div class="course-stat">
                                <span class="course-stat__label">Уроков</span>
                                <span class="course-stat__value"><?= $course['lessons_count'] ?? 0 ?></span>
                            </div>
                            <div class="course-stat">
                                <span class="course-stat__label">Сохранений</span>
                                <span class="course-stat__value"><?= $course['saved_count'] ?? 0 ?></span>
                            </div>
                            <div class="course-stat">
                                <span class="course-stat__label">Сертификатов</span>
                                <span class="course-stat__value course-stat__value--highlight">
                                    <?= $course['certificates_count'] ?? 0 ?>
                                </span>
                            </div>
                        </div>

                        <!-- Детальная статистика по урокам -->
                        <?php if (!empty($course['lessons'])): ?>
                            <div class="course-lessons-detailed">
                                <h4 class="lessons-title">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                                    </svg>
                                    Уроки курса
                                </h4>

                                <div class="lessons-table">
                                    <div class="lessons-table__header">
                                        <div class="lessons-table__cell">Название урока</div>
                                        <div class="lessons-table__cell">Прохождений</div>
                                    </div>

                                    <?php foreach ($course['lessons'] as $lesson): ?>
                                        <div class="lessons-table__row">
                                            <div class="lessons-table__cell lessons-table__cell--title" data-label="Название урока">
                                                <a href="/lesson/show/?id=<?= $lesson['id'] ?>">
                                                    <?= h($lesson['title']) ?>
                                                </a>
                                            </div>

                                            <div class="lessons-table__cell" data-label="Прохождений">
                                                <span class="completion-badge">
                                                    <strong><?= $lesson['completed_count'] ?? 0 ?></strong>
                                                    <small> раз прошли </small>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="no-lessons">
                                <p>В этом курсе пока нет уроков</p>
                                <a href="/lesson/create/?course_id=<?= $course['id'] ?>" class="btn-add-lesson">
                                    + Добавить урок
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Анимация счетчиков
        const counters = document.querySelectorAll('.counter');

        const animateCounter = (counter) => {
            const target = parseInt(counter.dataset.target);
            let current = 0;
            const increment = target / 50;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.round(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };

            updateCounter();
        };

        // Запускаем анимацию при появлении
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.querySelectorAll('.counter').forEach(animateCounter);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.3
        });

        document.querySelectorAll('.stats-grid').forEach(grid => observer.observe(grid));

        // Фильтрация курсов
        const filterSelect = document.getElementById('courseFilter');
        const coursesContainer = document.querySelector('.courses-analytics');

        filterSelect?.addEventListener('change', function() {
            const filter = this.value;
            const courses = Array.from(document.querySelectorAll('.course-analytics-card'));

            if (filter === 'popular') {
                courses.sort((a, b) => {
                    const savedA = parseInt(a.querySelector('.course-stat:nth-child(2) .course-stat__value').textContent);
                    const savedB = parseInt(b.querySelector('.course-stat:nth-child(2) .course-stat__value').textContent);
                    return savedB - savedA;
                });

                courses.forEach(course => coursesContainer.appendChild(course));
            } else if (filter === 'newest') {
                courses.sort((a, b) => {
                    // Сортировка по ID (чем новее, тем больше ID)
                    const idA = parseInt(a.dataset.courseId);
                    const idB = parseInt(b.dataset.courseId);
                    return idB - idA;
                });

                courses.forEach(course => coursesContainer.appendChild(course));
            }
        });
    });

    function showCourseDetails(courseId) {
        // Здесь можно добавить AJAX запрос за детальной статистикой
        alert('Детальная статистика для курса #' + courseId + ' будет доступна в следующем обновлении');
    }
</script>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>