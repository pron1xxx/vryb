<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>

<style>
    .results-container {
        display: flex;
        flex-direction: column;
        gap: 30px;
        max-width: 800px;
        margin: 0 auto;
    }

    .result-card {
        background: linear-gradient(135deg, #2196F3 0%, #0D47A1 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(33, 150, 243, 0.2);
        animation: fadeInUp 0.6s ease-out;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .result-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="white" opacity="0.05"/></svg>');
        background-size: cover;
    }

    .medal-container {
        position: relative;
        margin: 20px 0 30px;
        height: 180px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Стили для медалей */
    .medal {
        width: 140px;
        height: 140px;
        position: relative;
        animation: float 3s ease-in-out infinite;
    }

    .medal__body {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .medal__outer-ring {
        width: 100%;
        height: 100%;
        border: 8px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        position: absolute;
    }

    .medal__inner-circle {
        width: 80%;
        height: 80%;
        position: absolute;
        top: 10%;
        left: 10%;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .medal__shine {
        position: absolute;
        width: 40%;
        height: 40%;
        background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%);
        top: 15%;
        left: 15%;
        border-radius: 50%;
        animation: shineRotate 4s linear infinite;
    }

    .medal__ribbon {
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90px;
        height: 35px;
        background: linear-gradient(45deg, #FF5722, #E64A19);
        clip-path: polygon(0% 0%, 100% 0%, 85% 100%, 15% 100%);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .medal__icon {
        font-size: 50px;
        position: relative;
        z-index: 2;
        filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));
    }

    /* Разные типы медалей */
    .gold .medal__inner-circle {
        background: radial-gradient(circle at 30% 30%, #FFD700, #FFA500);
        box-shadow: 
            inset 0 0 20px rgba(255, 215, 0, 0.5),
            0 0 40px rgba(255, 215, 0, 0.3);
    }

    .gold .medal__outer-ring {
        border-color: #FFD700;
        box-shadow: 0 0 25px rgba(255, 215, 0, 0.4);
    }

    .silver .medal__inner-circle {
        background: radial-gradient(circle at 30% 30%, #E0E0E0, #B0B0B0);
        box-shadow: 
            inset 0 0 20px rgba(192, 192, 192, 0.5),
            0 0 40px rgba(192, 192, 192, 0.3);
    }

    .silver .medal__outer-ring {
        border-color: #C0C0C0;
        box-shadow: 0 0 25px rgba(192, 192, 192, 0.4);
    }

    .bronze .medal__inner-circle {
        background: radial-gradient(circle at 30% 30%, #CD7F32, #8B4513);
        box-shadow: 
            inset 0 0 20px rgba(205, 127, 50, 0.5),
            0 0 40px rgba(205, 127, 50, 0.3);
    }

    .bronze .medal__outer-ring {
        border-color: #CD7F32;
        box-shadow: 0 0 25px rgba(205, 127, 50, 0.4);
    }

    .no-medal .medal__inner-circle {
        background: radial-gradient(circle at 30% 30%, #90A4AE, #607D8B);
        box-shadow: 
            inset 0 0 20px rgba(144, 164, 174, 0.5),
            0 0 40px rgba(144, 164, 174, 0.3);
    }

    .no-medal .medal__outer-ring {
        border-color: #90A4AE;
        box-shadow: 0 0 25px rgba(144, 164, 174, 0.4);
    }

    /* Эффект сияния */
    .medal-glow {
        position: absolute;
        width: 160%;
        height: 160%;
        top: -30%;
        left: -30%;
        background: radial-gradient(circle, rgba(255,87,34,0.1) 0%, rgba(255,87,34,0) 70%);
        border-radius: 50%;
        animation: pulseGlow 2s ease-in-out infinite;
    }

    .score-display {
        font-size: 48px;
        font-weight: 800;
        margin: 10px 0;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        color: white;
    }

    .score-fraction {
        font-size: 24px;
        opacity: 0.9;
        margin-bottom: 20px;
        color: rgba(255, 255, 255, 0.9);
    }

    .progress-container {
        width: 100%;
        height: 20px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        overflow: hidden;
        margin: 20px 0;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #FF5722, #FF9800);
        border-radius: 10px;
        transition: width 1s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(255, 255, 255, 0.3) 50%, 
            transparent 100%);
        animation: shine 2s infinite;
    }

    .result-message {
        font-size: 24px;
        font-weight: 600;
        margin: 20px 0;
        min-height: 60px;
        color: white;
    }

    .result-details {
        display: flex;
        justify-content: space-around;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    .detail-icon {
        font-size: 24px;
        margin-bottom: 5px;
        color: #FF5722;
    }

    .detail-value {
        font-size: 18px;
        font-weight: 600;
        color: white;
    }

    .detail-label {
        font-size: 14px;
        opacity: 0.8;
        color: rgba(255, 255, 255, 0.8);
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 20px;
        border: 1px solid #e0e0e0;
    }

    .no-results-icon {
        font-size: 80px;
        color: #2196F3;
        margin-bottom: 20px;
        animation: pulse 2s infinite;
    }

    .no-results-text {
        font-size: 24px;
        color: #333;
        margin-bottom: 30px;
    }

    /* Анимации */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-15px);
        }
    }

    @keyframes shine {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }

    @keyframes shineRotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    @keyframes pulseGlow {
        0%, 100% {
            opacity: 0.5;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.1);
        }
    }

    @keyframes sparkle {
        0%, 100% {
            opacity: 0;
            transform: scale(0) rotate(0deg);
        }
        50% {
            opacity: 1;
            transform: scale(1) rotate(180deg);
        }
    }

    /* Искорки */
    .sparkle {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #FFD700;
        border-radius: 50%;
        animation: sparkle 1.5s infinite;
        box-shadow: 0 0 10px #FFD700;
        z-index: 3;
    }

    .sparkle:nth-child(1) { top: 15px; left: 25px; animation-delay: 0s; }
    .sparkle:nth-child(2) { top: 35px; right: 35px; animation-delay: 0.2s; }
    .sparkle:nth-child(3) { bottom: 25px; left: 45px; animation-delay: 0.4s; }
    .sparkle:nth-child(4) { bottom: 45px; right: 25px; animation-delay: 0.6s; }
    .sparkle:nth-child(5) { top: 50px; left: 15px; animation-delay: 0.8s; }
    .sparkle:nth-child(6) { top: 10px; right: 20px; animation-delay: 1s; }

    /* Конфетти */
    .confetti {
        position: absolute;
        width: 10px;
        height: 20px;
        background: #FF5722;
        animation: confettiFall 3s linear infinite;
        z-index: 10;
    }

    @keyframes confettiFall {
        0% {
            transform: translateY(-100px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(500px) rotate(720deg);
            opacity: 0;
        }
    }

    /* Кнопки в стиле сайта */
    .test-button {
        background: #FF5722;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
    }

    .test-button:hover {
        background: #E64A19;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 87, 34, 0.4);
    }
</style>

<div class="lesson-header">
    <h2 class="lesson-header__title"> Результаты теста </h2>
    <div class="buttons">
        <a href="/lesson/show/?id=<?= $lesson_id ?>"><button class="lesson-header__button"> ← Вернуться к уроку </button></a>
    </div>
</div>

<div class="results-container">
    <?php foreach ($results as $result): 
        $percentage = ($result['score'] / $result['total_questions']) * 100;
        
        // Определяем тип медали и иконку
        if ($percentage == 100) {
            $medalClass = 'gold';
            $message = 'Идеально! Превосходный результат!';
            $icon = '🏆'; // Кубок
        } elseif ($percentage >= 80) {
            $medalClass = 'gold';
            $message = 'Отлично! Почти идеально!';
            $icon = '⭐'; // Звезда
        } elseif ($percentage >= 60) {
            $medalClass = 'silver';
            $message = 'Хорошая работа! Так держать!';
            $icon = '🎖️'; // Медаль
        } elseif ($percentage >= 40) {
            $medalClass = 'bronze';
            $message = 'Неплохо! Есть куда стремиться!';
            $icon = '📚'; // Книга знаний
        } else {
            $medalClass = 'no-medal';
            $message = 'Попробуй еще раз! У тебя получится!';
            $icon = '🎯'; // Цель
        }
    ?>
        <div class="result-card <?= $medalClass ?>">
            <div class="medal-container">
                <div class="medal">
                    <?php if ($percentage == 100): ?>
                        <div class="medal-glow"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                    <?php endif; ?>
                    
                    <div class="medal__body">
                        <div class="medal__outer-ring"></div>
                        <div class="medal__inner-circle">
                            <div class="medal__shine"></div>
                            <div class="medal__icon"><?= $icon ?></div>
                        </div>
                    </div>
                    <div class="medal__ribbon"></div>
                </div>
            </div>

            <div class="score-display"><?= $result['score'] ?></div>
            <div class="score-fraction">из <?= $result['total_questions'] ?> вопросов</div>
            
            <div class="progress-container">
                <div class="progress-bar" style="width: <?= $percentage ?>%"></div>
            </div>

            <div class="result-message"><?= $message ?></div>
            
            <div class="result-details">
                <div class="detail-item">
                    <div class="detail-icon">📊</div>
                    <div class="detail-value"><?= round($percentage) ?>%</div>
                    <div class="detail-label">Процент</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon">✅</div>
                    <div class="detail-value"><?= $result['score'] ?></div>
                    <div class="detail-label">Правильно</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon">📅</div>
                    <div class="detail-value"><?= date('d.m.Y', strtotime($result['completed_at'])) ?></div>
                    <div class="detail-label">Дата</div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if(empty($results)): ?>
        <div class="no-results">
            <div class="no-results-icon">📝</div>
            <div class="no-results-text">Вы еще не проходили этот тест!</div>
            <a href="/lesson/show/?id=<?= $lesson_id ?>"><button class="test-button">Пройти тест сейчас</button></a>
        </div>
    <?php endif; ?>
</div>

<script>
    // Анимация появления прогресс-бара
    document.addEventListener('DOMContentLoaded', function() {
        const progressBars = document.querySelectorAll('.progress-bar');
        
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });

        // Создаем конфетти для золотых медалей с высоким результатом
        const goldCards = document.querySelectorAll('.gold');
        goldCards.forEach(card => {
            const percentage = parseInt(card.querySelector('.detail-value').textContent);
            
            if (percentage >= 80) {
                // Создаем конфетти
                for(let i = 0; i < 15; i++) {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.animationDelay = Math.random() * 2 + 's';
                    confetti.style.animationDuration = (1 + Math.random() * 2) + 's';
                    
                    // Разные цвета конфетти
                    const colors = ['#FF5722', '#2196F3', '#4CAF50', '#FFD700', '#9C27B0'];
                    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                    
                    // Разные формы
                    const shapes = ['square', 'rectangle', 'triangle'];
                    const shape = shapes[Math.floor(Math.random() * shapes.length)];
                    
                    if (shape === 'square') {
                        confetti.style.width = '10px';
                        confetti.style.height = '10px';
                        confetti.style.borderRadius = '2px';
                    } else if (shape === 'triangle') {
                        confetti.style.width = '0';
                        confetti.style.height = '0';
                        confetti.style.borderLeft = '5px solid transparent';
                        confetti.style.borderRight = '5px solid transparent';
                        confetti.style.borderBottom = '10px solid ' + confetti.style.background;
                        confetti.style.background = 'transparent';
                    }
                    
                    card.appendChild(confetti);
                }
            }
        });
    });
</script>

<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>