document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Находим элементы 
    const saveForm = document.getElementById('save-course-form');   
    const saveBtn = saveForm.querySelector('button[type="submit"]');
    const saveText = document.getElementById('save-text');
    const saveLoading = document.getElementById('save-loading');
    const heartIcon = document.querySelector('.save__course svg');
        
    // обработчик отправки формы
    saveForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        // Показываем загрузку
        if (saveBtn) saveBtn.disabled = true;
        if (saveText) saveText.style.display = 'none';
        if (saveLoading) saveLoading.style.display = 'block';
        
        // Скрываем предыдущие сообщения
        const currentMsgDiv = document.getElementById('ajax-message');
        if (currentMsgDiv) {
            currentMsgDiv.style.display = 'none';
        }
        
        try {
            // Собираем данные формы
            const formData = new FormData(saveForm);
            
            // Используем абсолютный URL для избежания 404
            const actionUrl = saveForm.action.startsWith('/') 
                ? window.location.origin + saveForm.action 
                : saveForm.action;
            
            // Отправляем запрос
            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData,
            });
            
            // Проверяем статус ответа
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Пытаемся распарсить JSON
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
                showMessage(data.message || 'Успешно!', 'success');
                
                // Меняем текст кнопки
                if (saveText) {
                    saveText.textContent = data.saved ? 'Удалить из сохраненых' : 'Сохранить курс';
                    saveText.style.display = 'inline';

                    data.saved ? updateHeartIcon(heartIcon, true) : updateHeartIcon(heartIcon, false);
                }
                
            } else {
                showMessage(data.message || 'Произошла ошибка', 'error');
            }
            
        } catch (error) {
            console.error('Ошибка при сохранении курса:', error);
            
            let errorMessage = 'Ошибка сети. Проверьте подключение к интернету';
            
            if (error.message.includes('404')) {
                errorMessage = 'Страница не найдена (404). Проверьте URL.';
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Ошибка обработки ответа сервера';
            }
            
            showMessage(errorMessage, 'error');
            
        } finally {
            // Всегда сбрасываем состояние
            if (saveBtn) saveBtn.disabled = false;
            if (saveText) saveText.style.display = 'inline';
            if (saveLoading) saveLoading.style.display = 'none';
        }
    });
    
    // 5. Функция обновления иконки сердца
    function updateHeartIcon(icon, isSaved) {
        if (!icon) return;
        
        const path = icon.querySelector('path');
        if (!path) return;
        
        if (isSaved) {
            // Заполненное сердце
            path.setAttribute('fill', '#ff0000');
        } else {
            // Пустое сердце
            path.setAttribute('fill', 'currentColor');
        }
    }
});