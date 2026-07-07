<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вруб - Платформа для создания и прохождения курсов</title>
    <meta name="description" content="Создавайте и проходите курсы в удобном видеоформате. Бесплатно.">
    <link rel="stylesheet" href="<?php echo CSS . '/index.css?dev=' . time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container"> 
        <header class="header"> 
            <img src="<?php echo IMAGES . '/logo_header.png'?>" alt="Вруб" class="header__logo">
            <div class="header__actions">
                <a class="header__link" href="/login">Войти</a>
                <a class="header__button" href="/register">Начать бесплатно</a>
            </div>
        </header>

        <section class="hero"> 
            <div class="container">
                <h1 class="hero__title">Создавайте курсы. Учитесь. Делитесь знаниями.</h1>
                <p class="hero__subtitle">
                    Вруб - это бесплатная платформа, где можно создавать свои курсы в формате видеоуроков 
                    или находить полезные курсы для обучения. Просто и понятно.
                </p>
                
                <div class="cta-buttons">
                    <a href="/register" class="btn btn-primary">
                        <i class="fas fa-rocket"></i> Создать свой курс
                    </a>
                    <a href="/" class="btn btn-secondary">
                        <i class="fas fa-play-circle"></i> Найти курсы
                    </a>
                </div>
            </div>
        </section>
    </div>

    <!-- Для создателей курсов -->
    <div class="wrapper"> 
        <div class="container whom"> 
            <h2 class="section-title">Для кого подходит Вруб?</h2>
            <p class="section-subtitle">Платформа для всех, кто хочет делиться знаниями или учиться</p>
            
            <div class="whom__cards" id="cardsWhom"></div>
        </div>
    </div>

    <!-- Как это работает -->
    <div class="container"> 
        <section class="how"> 
            <h2 class="section-title">Как работает платформа?</h2>
            <p class="section-subtitle">Всё просто: создавайте курсы или учитесь у других</p>
            <div class="how__cards" id="cardsHow"></div>
        </section>
    </div>

    <!-- Преимущества для учеников -->
    <section class="benefits">
        <div class="container">
            <h2 class="section-title">Почему удобно учиться на Врубе?</h2>
            <p class="section-subtitle">Всё сделано для комфортного обучения</p>
            
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Видеоуроки</h3>
                    <p>Смотрите уроки в удобном формате, как на YouTube</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Тесты и задания</h3>
                    <p>Закрепляйте знания с помощью практических заданий</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>На любом устройстве</h3>
                    <p>Учитесь с компьютера, телефона или планшета</p>
                </div>
            
            </div>
        </div>
    </section>

    <!-- Преимущества для создателей -->
    <section class="benefits">
        <div class="container">
            <h2 class="section-title">Почему создавать курсы на Врубе удобно?</h2>
            <p class="section-subtitle">Всё необходимое для запуска своего курса</p>
            
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Бесплатно</h3>
                    <p>Никаких платежей и комиссий. Создавайте сколько угодно курсов</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h3>Простая загрузка</h3>
                    <p>Загружайте видео и добавляйте тесты за несколько минут</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Защита контента</h3>
                    <p>Ваши видео защищены от скачивания и нелегального распространения</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Простые шаги -->
    <div class="container"> 
        <section class="how"> 
            <h2 class="section-title">Как начать?</h2>
            <p class="section-subtitle">Всё очень просто</p>
            <div class="simple-steps">
                <div class="simple-step">
                    <div class="step-number">1</div>
                    <h3>Зарегистрируйтесь</h3>
                    <p>Создайте аккаунт за 1 минуту. Никаких платежных данных не нужно</p>
                </div>
                <div class="simple-step">
                    <div class="step-number">2</div>
                    <h3>Создайте курс или найдите подходящий</h3>
                    <p>Начните создавать свой курс с видеоуроками или выберите курс из каталога</p>
                </div>
                <div class="simple-step">
                    <div class="step-number">3</div>
                    <h3>Начните учить или учиться</h3>
                    <p>Делитесь знаниями с учениками или осваивайте новые навыки</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Отзывы -->
    <section class="testimonial">
        <div class="container">
            <h2 class="section-title">Что говорят пользователи?</h2>
            <p class="section-subtitle">Реальные отзывы от тех, кто уже пользуется Врубом</p>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Создал курс по программированию. Всё очень просто: загрузил видео, добавил тесты. Ученики довольны, я тоже."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">ДП</div>
                        <div>
                            <div class="author-name">Дмитрий Петров</div>
                            <div class="author-role">Программист</div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Нашла отличный курс по маркетингу. Удобно, что можно смотреть с телефона и есть тесты после каждого урока."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">АС</div>
                        <div>
                            <div class="author-name">Анна Смирнова</div>
                            <div class="author-role">Маркетолог</div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Как репетитор, я перевела свои занятия в онлайн-курс. Теперь у меня больше учеников, а работать стало проще."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">ЕК</div>
                        <div>
                            <div class="author-name">Екатерина Козлова</div>
                            <div class="author-role">Преподаватель</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <div class="container">
        <section class="faq">
            <h2 class="section-title">Частые вопросы</h2>
            <div class="faq__list" id="faqList"></div>
        </section>
    </div>

    <!-- Финальный призыв к действию -->
    <section class="final-cta">
        <div class="container">
            <h2>Попробуйте прямо сейчас</h2>
            <p>Бесплатно. Без ограничений. Без сложностей.</p>
            
            <div class="cta-buttons">
                <a href="/register" class="btn btn-secondary" style="background: white; color: #2196F3;">
                    <i class="fas fa-user-plus"></i> Создать аккаунт
                </a>
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-search"></i> Посмотреть курсы
                </a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <img src="<?php echo IMAGES . '/logo_header.png'?>" alt="Вруб" style="height: 40px; margin-bottom: 20px;">
            <p>Бесплатная платформа для создания и прохождения курсов</p>
            <div class="footer__links">
                <a href="/register">Регистрация</a>
                <a href="/login">Вход</a>
                <a href="/">Все курсы</a>
                <a href="/create">Создать курс</a>
            </div>
            <p>&copy; 2025 Вруб. Все права защищены.</p>
        </div>
    </footer>

    <script> 
        // Данные для блока "Для кого подходит"
        let card_data = [
            {
                number: 1,
                title: "Для преподавателей",
                subtitle: "Создайте онлайн-курс по своему предмету. Делитесь знаниями с учениками со всего мира.",
            },
            {
                number: 2,
                title: "Для экспертов",
                subtitle: "Расскажите о своем опыте. Создайте курс по своей профессии или хобби.",
            },
            {
                number: 3,
                title: "Для репетиторов",
                subtitle: "Превратите индивидуальные занятия в готовый курс. Учите больше учеников одновременно.",
            },
            {
                number: 4,
                title: "Для всех, кто хочет учиться",
                subtitle: "Найдите курсы по программированию, маркетингу, дизайну и другим темам. Учитесь в удобное время.",
            }
        ];

        let parentDiv = document.querySelector('#cardsWhom')

        card_data.forEach(card => {
            parentDiv.innerHTML += 
            `
            <div class="whom__cardWrapper"> 
                <div class="whom__number"> ${card.number} </div>
                <div class="whom__card"> 
                    <h3 class="whom__cardTitle"> ${card.title} </h3>
                    <p class="whom__cardSubtitle"> ${card.subtitle} </p>
                </div>
            </div>
            `
        });
        
        // Данные для блока "Как это работает"
        card_data = [
            {
                'img': '<?php echo IMAGES . '/icon_create.png'?>',
                'title': 'Регистрация',
                'subtitle': 'Создайте аккаунт за минуту. Никаких платежных данных не нужно.'
            },
            {
                'img': '<?php echo IMAGES . '/icon_fill.png'?>',
                'title': 'Создание курса',
                'subtitle': 'Загрузите видеоуроки, добавьте описание и тесты. Всё в простом интерфейсе.'
            },
            {
                'img': '<?php echo IMAGES . '/icon_web.png'?>',
                'title': 'Публикация',
                'subtitle': 'Опубликуйте курс и поделитесь ссылкой. Ученики смогут сразу начать обучение.'
            },
        ]

        parentDiv = document.querySelector('#cardsHow')

        card_data.forEach(card => {
            parentDiv.innerHTML += 
            `
            <div class="how__card">
                <img src="${card.img}" alt="" class="how__img">
                <h3 class="how__cardTitle">${card.title}</h3>
                <p class="how__cardSubtitle">${card.subtitle}</p>
            </div>
            `
        });

        // Данные для FAQ
        const faqData = [
            {
                question: 'Это действительно бесплатно?',
                answer: 'Да, полностью бесплатно. Никаких скрытых платежей, комиссий или ограничений.'
            },
            {
                question: 'Можно ли создавать несколько курсов?',
                answer: 'Да, создавайте столько курсов, сколько нужно. Ограничений нет.'
            },
            {
                question: 'Как добавить учеников на курс?',
                answer: 'Просто поделитесь ссылкой на курс. Ученики смогут зайти и начать обучение.'
            },
            {
                question: 'Какие форматы видео поддерживаются?',
                answer: 'Все популярные форматы: MP4, MOV, AVI. Система автоматически оптимизирует видео для просмотра.'
            },
            {
                question: 'Можно ли добавлять тесты к урокам?',
                answer: 'Да, к каждому уроку можно добавить тест с вопросами и вариантами ответов.'
            },
            {
                question: 'Что делать если возникли проблемы?',
                answer: 'Напишите нам в поддержку. Мы поможем решить любые вопросы.'
            }
        ];

        const faqList = document.querySelector('#faqList');
        faqData.forEach((faq, index) => {
            faqList.innerHTML += `
                <div class="faq__item">
                    <div class="faq__question" onclick="toggleFAQ(${index})">
                        ${faq.question}
                        <span>+</span>
                    </div>
                    <div class="faq__answer">
                        ${faq.answer}
                    </div>
                </div>
            `;
        });

        // Функция для FAQ
        function toggleFAQ(index) {
            const items = document.querySelectorAll('.faq__item');
            const item = items[index];
            item.classList.toggle('active');
            
            items.forEach((otherItem, otherIndex) => {
                if (otherIndex !== index && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });
        }

        // Анимация при скролле
        window.addEventListener('scroll', function() {
            const elements = document.querySelectorAll('.benefit-card, .testimonial-card, .whom__cardWrapper, .how__card, .simple-step');
            
            elements.forEach(element => {
                const position = element.getBoundingClientRect();
                
                if(position.top < window.innerHeight - 100) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        });

        // Инициализация анимации
        document.querySelectorAll('.benefit-card, .testimonial-card, .whom__cardWrapper, .how__card, .simple-step').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        });

        // Триггер анимации после загрузки
        setTimeout(() => {
            window.dispatchEvent(new Event('scroll'));
        }, 500);
    </script>
</body>
</html>