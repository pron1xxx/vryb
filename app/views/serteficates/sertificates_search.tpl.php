<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>
<main class="content">
    <div class="certificates-search-page">
        <!-- Заголовок -->
        <div class="certificates-search-header">
            <h1 class="certificates-search-header__title">Поиск сертификатов</h1>
            <p class="certificates-search-header__subtitle">Найдите сертификат по имени пользователя или уникальному ID</p>
        </div>

        <!-- Форма поиска -->
        <div class="search-section">
            <form class="search-form" id="searchForm">
                <input type="hidden" id="csrf" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="search-form__tabs">
                    <button type="button" class="search-form__tab active" data-tab="name">По имени</button>
                    <button type="button" class="search-form__tab" data-tab="id">По ID</button>
                </div>
                <div class="search-form__content">
                    <!-- Поиск по имени -->
                    <div class="search-form__field active" id="searchByName">
                        <label for="userName" class="search-form__label">Введите имя и фамилию пользователя</label>
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                class="search-form__input"
                                id="userName"
                                placeholder="Иван Иванов"
                                autocomplete="off">
                            <button type="submit" class="search-form__submit">
                                <span>🔍</span> Поиск
                            </button>
                        </div>
                        <div class="search-form__hint">Например: Иван Иванов, Анна Смирнова</div>
                    </div>

                    <!-- Поиск по ID -->
                    <div class="search-form__field" id="searchById">
                        <label for="certId" class="search-form__label">Введите ID сертификата</label>
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                class="search-form__input"
                                id="certId"
                                placeholder="1"
                                autocomplete="off">
                            <button type="submit" class="search-form__submit">
                                <span>🔍</span> Поиск
                            </button>
                        </div>
                        <div class="search-form__hint">Например: 1</div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Результаты поиска -->
        <div class="search-results" id="searchResults">
            <!-- Здесь будут отображаться результаты -->
        </div>

        <!-- История поиска (опционально) -->
        <div class="search-history" id="searchHistory">
            <div class="search-history__header">
                <h3 class="search-history__title">Недавние запросы</h3>
                <button class="search-history__clear" id="clearHistory">Очистить</button>
            </div>
            <div class="search-history__list" id="historyList">
                <!-- Сюда будут добавляться недавние запросы -->
            </div>
        </div>
    </div>
</main>

<!-- Шаблон для результата поиска (скрыт) -->
<template id="certificateResultTemplate">
    <div class="certificate-result">
        <div class="certificate-result__preview">
            <div class="certificate-result__mini">
                <div class="certificate-result__mini-logo">ВРУБ</div>
                <div class="certificate-result__mini-title">СЕРТИФИКАТ</div>
                <div class="certificate-result__mini-name">{user_fio}</div>
            </div>
        </div>

        <div class="certificate-result__info">
            <div class="certificate-result__header">
                <h3 class="certificate-result__course">{course_name}</h3>
                <span class="certificate-result__badge">Активен</span>
            </div>

            <div class="certificate-result__details">
                <div class="certificate-result__detail">
                    <span class="certificate-result__detail-icon">👤</span>
                    <div class="certificate-result__detail-text">
                        <span class="certificate-result__detail-label">Владелец</span>
                        <span class="certificate-result__detail-value">{user_fio}</span>
                    </div>
                </div>

                <div class="certificate-result__detail">
                    <span class="certificate-result__detail-icon">🔑</span>
                    <div class="certificate-result__detail-text">
                        <span class="certificate-result__detail-label">ID сертификата</span>
                        <span class="certificate-result__detail-value">{cert_id}</span>
                    </div>
                </div>

                <div class="certificate-result__detail">
                    <span class="certificate-result__detail-icon">📅</span>
                    <div class="certificate-result__detail-text">
                        <span class="certificate-result__detail-label">Дата выдачи</span>
                        <span class="certificate-result__detail-value">{received_to}</span>
                    </div>
                </div>

                <div class="certificate-result__detail">
                    <span class="certificate-result__detail-icon">🎓</span>
                    <div class="certificate-result__detail-text">
                        <span class="certificate-result__detail-label">Курс</span>
                        <span class="certificate-result__detail-value">{course_name}</span>
                    </div>
                </div>
            </div>

            <div class="certificate-result__actions">
                <a href="/serteficate/?id={cert_id}" class="certificate-result__btn certificate-result__btn--primary">
                    Просмотреть
                </a>
                <button class="certificate-result__btn certificate-result__btn--icon" onclick="downloadCertificate({cert_id}, event)" title="Скачать PNG">
                    ⬇️
                </button>
                <button class="certificate-result__btn certificate-result__btn--icon" onclick="copyCertificateId('{cert_id}')" title="Копировать ID">
                    📋
                </button>
            </div>
        </div>
    </div>
</template>

<!-- Шаблон для пустого результата -->
<template id="emptyResultTemplate">
    <div class="search-results__empty">
        <div class="search-results__empty-icon">🔍</div>
        <h3 class="search-results__empty-title">Ничего не найдено</h3>
        <p class="search-results__empty-text">
            Попробуйте изменить поисковый запрос или проверить правильность введенных данных
        </p>
    </div>
</template>

<!-- Шаблон для ошибки -->
<template id="errorResultTemplate">
    <div class="search-results__error">
        <div class="search-results__error-icon">⚠️</div>
        <h3 class="search-results__error-title">Ошибка при поиске</h3>
        <p class="search-results__error-text">{error_message}</p>
    </div>
</template>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>

<script>
    let currentTab = 'name';
    let searchHistory = JSON.parse(localStorage.getItem('certSearchHistory') || '[]');

    // Инициализация
    document.addEventListener('DOMContentLoaded', function() {
        initTabs();
        initSearch();
        initHistory();
    });

    // Переключение табов
    function initTabs() {
        const tabs = document.querySelectorAll('.search-form__tab');
        const fields = document.querySelectorAll('.search-form__field');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.dataset.tab;

                // Обновляем активный таб
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Обновляем поле поиска
                fields.forEach(f => f.classList.remove('active'));
                document.getElementById(`searchBy${tabName === 'name' ? 'Name' : 'Id'}`).classList.add('active');

                currentTab = tabName;
            });
        });
    }

    // Поиск
    function initSearch() {
        const form = document.getElementById('searchForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const query = currentTab === 'name' ?
                document.getElementById('userName').value.trim() :
                document.getElementById('certId').value.trim();

            if (!query) {
                showMessage('Введите данные для поиска', true);
                return;
            }

            // Добавляем в историю
            addToHistory(query);

            // Показываем загрузку
            showLoader();

            try {
                // Отправляем запрос
                const response = await fetch('/serteficates/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: currentTab,
                        query: query,
                        csrf_token: document.getElementById('csrf').value
                    })
                });
                
                console.log(document.getElementById('csrf').value)

                const data = await response.json();

                if (data.success) {
                    displayResults(data.results);
                } else {
                    displayError(data.message || 'Ошибка при поиске');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                displayError('Ошибка соединения с сервером');
            }
        });
    }

    // Отображение результатов
    function displayResults(results) {
        const container = document.getElementById('searchResults');
        container.innerHTML = '';

        if (!results || results.length === 0) {
            const template = document.getElementById('emptyResultTemplate');
            container.appendChild(template.content.cloneNode(true));
            return;
        }

        const resultTemplate = document.getElementById('certificateResultTemplate');

        results.forEach(cert => {
            const clone = resultTemplate.content.cloneNode(true);

            // Заполняем данными
            clone.querySelector('.certificate-result__mini-name').textContent = cert.user_fio;
            clone.querySelector('.certificate-result__course').textContent = cert.course_name;
            clone.querySelectorAll('.certificate-result__detail-value')[0].textContent = cert.user_fio;
            clone.querySelectorAll('.certificate-result__detail-value')[1].textContent = cert.id;
            clone.querySelectorAll('.certificate-result__detail-value')[2].textContent = cert.received_to;
            clone.querySelectorAll('.certificate-result__detail-value')[3].textContent = cert.course_name;

            // Обновляем ссылки
            const viewLink = clone.querySelector('.certificate-result__btn--primary');
            viewLink.href = `/serteficate/?id=${cert.id}`;

            const downloadBtn = clone.querySelector('.certificate-result__btn--icon');
            downloadBtn.setAttribute('onclick', `downloadCertificate(${cert.id}, event)`);

            const copyBtn = clone.querySelectorAll('.certificate-result__btn--icon')[1];
            copyBtn.setAttribute('onclick', `copyCertificateId('${cert.id}')`);

            container.appendChild(clone);
        });
    }

    // Показать ошибку
    function displayError(message) {
        const container = document.getElementById('searchResults');
        const template = document.getElementById('errorResultTemplate');
        const clone = template.content.cloneNode(true);

        clone.querySelector('.search-results__error-text').textContent = message;

        container.innerHTML = '';
        container.appendChild(clone);
    }

    // Показать загрузку
    function showLoader() {
        const container = document.getElementById('searchResults');
        container.innerHTML = `
            <div class="search-results__loader">
                <div class="loader"></div>
                <p>Поиск сертификатов...</p>
            </div>
        `;
    }

    // История поиска
    function initHistory() {
        updateHistoryList();
    }

    function addToHistory(query) {
        if (!query) return;

        // Убираем дубликаты
        searchHistory = [query, ...searchHistory.filter(q => q !== query)].slice(0, 5);
        localStorage.setItem('certSearchHistory', JSON.stringify(searchHistory));
        updateHistoryList();
    }

    function updateHistoryList() {
        const list = document.getElementById('historyList');
        list.innerHTML = '';

        if (searchHistory.length === 0) {
            list.innerHTML = '<div class="search-history__empty">История пуста</div>';
            return;
        }

        searchHistory.forEach(query => {
            const item = document.createElement('div');
            item.className = 'search-history__item';
            item.textContent = query;
            item.onclick = () => {
                if (currentTab === 'name') {
                    document.getElementById('userName').value = query;
                } else {
                    document.getElementById('certId').value = query;
                }
                document.getElementById('searchForm').dispatchEvent(new Event('submit'));
            };
            list.appendChild(item);
        });
    }

    // Очистить историю
    document.getElementById('clearHistory')?.addEventListener('click', function() {
        searchHistory = [];
        localStorage.removeItem('certSearchHistory');
        updateHistoryList();
    });

    // Копирование ID
    function copyCertificateId(id) {
        navigator.clipboard.writeText(id).then(() => {
            showMessage('ID скопирован!');
        });
    }

    // Скачивание сертификата
    function downloadCertificate(id, event) {
        if (event) event.stopPropagation();
        window.open(`/serteficate/?id=${id}&download=png`, '_blank');
    }
</script>