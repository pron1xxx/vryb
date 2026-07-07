<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>
<main class="content">
    <div class="certificates-page">
        <!-- Заголовок -->
        <div class="certificates-header">
            <h1 class="certificates-header__title">Мои сертификаты</h1>
            <p class="certificates-header__subtitle">Все полученные сертификаты за пройденные курсы</p>
            <a href="/serteficates/search" class="empty-certificates__btn" style="margin-top: 13px">Поиск сертификатов</a>
        </div>
        <!-- Статистика -->
        <div class="certificates-stats">
            <div class="stat-card">
                <div class="stat-card__value"><?= count($certificates) ?></div>
                <div class="stat-card__label">Всего сертификатов</div>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="certificates-filters">
            <div class="search" style="justify-content: flex-start;">
                <input type="text" class="search__input" placeholder="Поиск по сертификатам..." id="searchCertificates">
                <span class="search__button">🔍</span>
            </div>

            <div class="certificates-filters__sort">
                <select class="certificates-filters__select">
                    <option value="newest">Сначала новые</option>
                    <option value="oldest">Сначала старые</option>
                    <option value="name">По названию</option>
                </select>
            </div>
        </div>

        <!-- Сетка сертификатов -->
        <div class="certificates-grid">
            <?php foreach ($certificates as $certificate): ?>
                <!-- Карточка сертификата -->
                <div class="certificate-card" onclick="window.location.href='/serteficate/?id=<?= $certificate['id'] ?>'">
                    <div class="certificate-card__preview">
                        <div class="certificate-card__mini">
                            <div class="certificate-card__mini-logo">ВРУБ</div>
                            <div class="certificate-card__mini-title">СЕРТИФИКАТ</div>
                            <div class="certificate-card__mini-name"><?= h($certificate['user_fio']) ?></div>
                        </div>
                    </div>

                    <div class="certificate-card__info">
                        <a href="/course/show/?id=<?= $certificate['course_id'] ?>">
                            <h3 class="certificate-card__title"><?= h($certificate['course_name']) ?></h3>
                        </a>

                        <div class="certificate-card__meta">
                            <div class="certificate-card__date">
                                <span class="certificate-card__meta-icon">📅</span>
                                <?= $certificate['received_to'] ?>
                            </div>
                            <div class="certificate-card__id">
                                <span class="certificate-card__meta-icon">🔑</span>
                                <?= $certificate['id'] ?>
                            </div>
                            <a href="/channel/?id=<?= $certificate['user_id'] ?>">
                                <div class="certificate-card__id">
                                    <span class="certificate-card__meta-icon">👤</span>
                                    <?= $certificate['user_id'] ?>
                                </div>
                            </a>
                        </div>

                        <div class="certificate-card__footer">
                            <a href="/serteficate/?id=<?= $certificate['id'] ?>" class="certificate-card__btn">Просмотреть</a>
                            <button class="certificate-card__btn certificate-card__btn--icon" onclick="downloadCertificate(<?= $certificate['id'] ?>, event)" title="Скачать PNG">⬇️</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Пустое состояние (показывать только если нет сертификатов) -->
            <?php if (empty($certificates)): ?>
                <div class="empty-certificates">
                    <div class="empty-certificates__icon">🎓</div>
                    <h3 class="empty-certificates__title">У вас пока нет сертификатов</h3>
                    <p class="empty-certificates__text">Пройдите курсы, чтобы получить свой первый сертификат</p>
                    <a href="/" class="empty-certificates__btn">К курсам</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    // Функция для показа сообщения
    function showMessage(text, isError = false) {
        const message = document.createElement("div");
        message.className = "message";
        message.style.position = "fixed";
        message.style.top = "20px";
        message.style.right = "20px";
        message.style.zIndex = "9999";
        message.style.background = isError ? "#dc3545" : "#28a745";
        message.style.color = "white";
        message.style.padding = "10px 20px";
        message.style.borderRadius = "5px";
        message.style.boxShadow = "0 2px 10px rgba(0,0,0,0.1)";
        message.textContent = text;

        document.body.appendChild(message);

        setTimeout(() => {
            message.remove();
        }, 2000);
    }

    // Скачивание через iframe
    function downloadCertificate(certId, event) {
        if (event) {
            event.stopPropagation();
        }

        showMessage("Перенаправление на страницу сертификата...");

        // Открываем в новой вкладке с параметром download
        window.open(`/serteficate/?id=${certId}&download=png`, '_blank');
    }

    // Поиск по сертификатам
    document.getElementById('searchCertificates')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.certificate-card');

        cards.forEach(card => {
            const title = card.querySelector('.certificate-card__title').textContent.toLowerCase();
            const id = card.querySelector('.certificate-card__id').textContent.toLowerCase();

            if (title.includes(searchTerm) || id.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Сортировка
    document.querySelector('.certificates-filters__select')?.addEventListener('change', function(e) {
        const sortBy = e.target.value;
        const grid = document.querySelector('.certificates-grid');
        const cards = Array.from(document.querySelectorAll('.certificate-card'));

        cards.sort((a, b) => {
            const dateA = a.querySelector('.certificate-card__date').textContent;
            const dateB = b.querySelector('.certificate-card__date').textContent;
            const titleA = a.querySelector('.certificate-card__title').textContent;
            const titleB = b.querySelector('.certificate-card__title').textContent;

            if (sortBy === 'newest') {
                return new Date(dateB) - new Date(dateA);
            } else if (sortBy === 'oldest') {
                return new Date(dateA) - new Date(dateB);
            } else if (sortBy === 'name') {
                return titleA.localeCompare(titleB);
            }
        });

        grid.innerHTML = '';
        cards.forEach(card => grid.appendChild(card));
    });
</script>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>