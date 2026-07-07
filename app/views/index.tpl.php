<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>

<div class="about-page">
    <!-- Hero секция с анимацией -->
    <section class="about-hero">
        <div class="about-hero__content">
            <h1 class="about-hero__title">
                <span class="gradient-text animate-gradient">Вруб</span>
                делись знаниями
            </h1>
            <p class="about-hero__subtitle">
                Бесплатная платформа для создания и изучения курсов.
                Здесь каждый может стать учителем и учеником одновременно.
            </p>
            <div class="about-hero__buttons">
                <a href="/create" class="btn btn--primary btn--pulse">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Создать курс
                </a>
                <a href="/" class="btn btn--secondary btn--shine">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                    Начать учиться
                </a>
            </div>
        </div>
    </section>
    <!-- Что такое курс? -->
    <section class="about-section about-section--alt">
        <div class="section-header">
            <h2 class="section-title">Как это работает?</h2>
            <p class="section-subtitle">Курс — это сборник уроков, объединенных одной темой</p>
        </div>

        <div class="feature-cards">
            <div class="feature-card feature-card--lesson">
                <div class="feature-card__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 8v8M8 12h8" />
                    </svg>
                </div>
                <h3 class="feature-card__title">Уроки</h3>
                <p class="feature-card__text">Каждый урок содержит видео, вожможно теорию и практические задания</p>
                <div class="feature-card__demo">
                    <div class="demo-video">
                        <div class="demo-video__play"></div>
                    </div>
                </div>
            </div>

            <div class="feature-card feature-card--test">
                <div class="feature-card__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="feature-card__title">Тесты</h3>
                <p class="feature-card__text">Проверяйте знания с помощью тестов</p>
                <div class="feature-card__demo">
                    <div class="demo-test">
                        <div class="demo-test__option"></div>
                        <div class="demo-test__option"></div>
                        <div class="demo-test__option demo-test__option--correct"></div>
                    </div>
                </div>
            </div>

            <div class="feature-card feature-card--file">
                <div class="feature-card__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="feature-card__title">Файлы</h3>
                <p class="feature-card__text">Лекционные материалы и практические задания</p>
                <div class="feature-card__demo">
                    <div class="demo-files">
                        <span class="demo-file"></span>
                        <span class="demo-file"></span>
                        <span class="demo-file"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Почему круто для учеников -->
    <section class="about-section">
        <div class="section-header">
            <h2 class="section-title">Для учеников</h2>
            <p class="section-subtitle">Всё для эффективного обучения</p>
        </div>

        <div class="benefits-grid">
            <div class="benefit-card benefit-card--ai">
                <div class="benefit-card__icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4M12 8h.01" />
                    </svg>
                </div>
                <h3 class="benefit-card__title">AI пересказ файлов</h3>
                <p class="benefit-card__text">Устал читать? — получи краткий пересказ от искусственного интеллекта</p>
                <div class="benefit-card__demo">
                    <div class="ai-demo">
                        <div class="ai-demo__bubble">✨ Краткое содержание...</div>
                    </div>
                </div>
            </div>

            <div class="benefit-card benefit-card--test">
                <div class="benefit-card__icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="benefit-card__title">Контроль знаний</h3>
                <p class="benefit-card__text">Тесты после каждого урока помогают закрепить материал</p>
                <div class="benefit-card__demo">
                    <div class="progress-demo">
                        <div class="progress-bar" style="width: 75%"></div>
                    </div>
                </div>
            </div>

            <div class="benefit-card benefit-card--cert">
                <div class="benefit-card__icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M19 11a3 3 0 0 1 2 5.236v4.955a.5.5 0 0 1-.724.447L19 21l-1.276.638a.5.5 0 0 1-.724-.447v-4.955A3 3 0 0 1 19 11m1-7a2 2 0 0 1 2 2v4a5 5 0 0 0-7 7v3H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                    </svg>
                </div>
                <h3 class="benefit-card__title">Сертификаты</h3>
                <p class="benefit-card__text">После прохождения курса получите именной сертификат</p>
                <div class="benefit-card__demo">
                    <div class="cert-demo"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Почему круто для преподавателей -->
    <section class="about-section about-section--alt">
        <div class="section-header">
            <h2 class="section-title">Для преподавателей</h2>
            <p class="section-subtitle">Удобные инструменты для создания курсов</p>
        </div>

        <div class="features-grid">
            <div class="feature-row">
                <div class="feature-row__content">
                    <h3 class="feature-row__title">Всё в одном месте</h3>
                    <p class="feature-row__text">Собирайте видео, тесты и файлы в структурированные уроки. Больше не нужно искать материалы по разным папкам.</p>
                    <ul class="feature-list">
                        <li class="feature-list__item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                            Видео уроки
                        </li>
                        <li class="feature-list__item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                            Тесты
                        </li>
                        <li class="feature-list__item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                            Файлы для скачивания
                        </li>
                    </ul>
                </div>
                <div class="feature-row__visual">
                    <div class="folder-animation">
                        <div class="folder">
                            <div class="folder__tab"></div>
                            <div class="folder__body">
                                <div class="folder__file"></div>
                                <div class="folder__file"></div>
                                <div class="folder__file"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="feature-row feature-row--reverse">
                <div class="feature-row__content">
                    <h3 class="feature-row__title">Создание тестов</h3>
                    <p class="feature-row__text">Простой конструктор тестов. Проверяйте знания учеников автоматически.</p>
                </div>
                <div class="test-preview" style="width: 100%;">
                    <div class="test-question">
                        <div class="test-question__text"></div>
                        <div class="test-options">
                            <span class="test-option"></span>
                            <span class="test-option test-option--correct"></span>
                            <span class="test-option"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Преимущества платформы -->
    <section class="about-section">
        <div class="section-header">
            <h2 class="section-title">Почему выбирают Вруб</h2>
        </div>

        <div class="advantages-grid">
            <div class="advantage-card advantage-card--free">
                <div class="advantage-card__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                </div>
                <h3 class="advantage-card__title">Полностью бесплатно</h3>
                <p class="advantage-card__text">Никакой монетизации — только знания. Все функции доступны без подписок.</p>
            </div>

            <div class="advantage-card advantage-card--devices">
                <div class="advantage-card__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="2" y="3" width="20" height="14" rx="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                    </svg>
                </div>
                <h3 class="advantage-card__title">На всех устройствах</h3>
                <p class="advantage-card__text">Учитесь где угодно — на компьютере, планшете или телефоне. Весь прогресс синхронизируется.</p>
            </div>

            <div class="advantage-card advantage-card--interface">
                <div class="advantage-card__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        <circle cx="12" cy="12" r="2" />
                    </svg>
                </div>
                <h3 class="advantage-card__title">Удобный интерфейс</h3>
                <p class="advantage-card__text">Интуитивно понятный дизайн, который не отвлекает от обучения.</p>
            </div>
        </div>
    </section>

    <!-- Сообщество -->
    <section class="about-section about-section--community">
        <div class="community-content">
            <h2 class="community-title">Стань частью сообщества</h2>
            <p class="community-text">Зарегестрируйся и получи доступ ко всем функциям!</p>
            <a href="/register" class="btn btn--primary btn--large btn--pulse">
                Начать
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Запускаем анимацию при появлении в viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        counters.forEach(counter => observer.observe(counter));

        // Параллакс эффект для карточек
        document.addEventListener('mousemove', (e) => {
            const cards = document.querySelectorAll('.feature-card');
            const mouseX = e.clientX / window.innerWidth - 0.5;
            const mouseY = e.clientY / window.innerHeight - 0.5;

            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                const cardX = (rect.left + rect.width / 2) / window.innerWidth - 0.5;
                const cardY = (rect.top + rect.height / 2) / window.innerHeight - 0.5;

                const rotateX = (mouseY - cardY) * 10;
                const rotateY = (mouseX - cardX) * 10;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
            });
        });

        // Анимация появления элементов при скролле
        const fadeElements = document.querySelectorAll('.feature-card, .benefit-card, .advantage-card, .feature-row');

        const fadeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    fadeObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        fadeElements.forEach(el => fadeObserver.observe(el));
    });
</script>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>