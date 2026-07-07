<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>

<body>
  <div class="container">
    <!-- Основной контент -->
    <main class="certificate-page">
      <div class="certificate-container">
        <!-- Заголовок с действиями -->
        <div class="certificate-header">
          <h1 class="certificate-header__title">Сертификат о прохождении</h1>

          <div class="certificate-actions">
            <button
              class="certificate-actions__btn certificate-actions__btn--secondary"
              onclick="window.print()">
              <span>🖨️</span> Печать
            </button>
            <button class="certificate-actions__btn" id="downloadPNG">
              <span>🖼️</span> Скачать PNG
            </button>
          </div>
        </div>

        <!-- Сертификат -->
        <div id="certificate-content">
          <div class="certificate">
            <div class="certificate__border"></div>
            <div class="certificate__watermark">CERTEFICATE</div>

            <div class="certificate__header">
              <div class="certificate__logo">ВРУБ</div>
              <h1 class="certificate__title">СЕРТИФИКАТ</h1>
              <div class="certificate__subtitle">
                подтверждает успешное прохождение курса
              </div>
            </div>

            <div class="certificate__content">
              <div class="certificate__label">Выдан</div>
              <div class="certificate__recipient" id="recipientName">
                <?= h($sertificate_data['user_fio']) ?>
              </div>
              <br>
              <a href="/channel/?id=<?= $sertificate_data['user_id'] ?>">
                <div class="certificate__label">Id-пользователя <?= $sertificate_data['user_id'] ?></div>
              </a>

              <a href="/course/show/?id=<?= $sertificate_data['course_id'] ?>">
                <div class="certificate__course" id="courseName">
                  <?= h($sertificate_data['course_name']) ?>
                </div>
              </a>


              <div class="certificate__date" id="issueDate">
                Дата выдачи: <span><?= $sertificate_data['received_to'] ?></span>
              </div>

              <div class="certificate__issued">
                <div class="issued-item">
                  <span class="issued-item__label">Уроков</span>
                  <span class="issued-item__value" id="hours"><?= $lessons[0] ?></span>
                </div>
              </div>
            </div>

            <div class="certificate__footer">
              <div class="certificate__signatures">
                <div class="signature">
                  <div class="signature__name"> <img src="<?= IMAGES . '/rospis.png' ?>" alt="" style="max-width: 150px; color: white;"> </div>
                  <div class="signature__line"></div>
                  <div class="signature__name">Илья Шупта</div>
                  <div class="signature__title">Создатель платформы</div>
                </div>
              </div>

              <div class="certificate__seal">
                <div class="seal">
                  <div class="seal__inner">ВРУБ✔</div>
                </div>
                <div class="seal__text">Официальная печать</div>
              </div>
            </div>

            <div class="certificate__id">
              <div class="certificate-id">
                <span class="certificate-id__label">ID:</span>
                <span class="certificate-id__number" id="certificateId"><?= $sertificate_data['id'] ?></span>
                <span class="certificate-id__copy" title="Копировать">📋</span>
              </div>
            </div>
          </div>
        </div>

        <div class="message" style="margin-top: 20px">
          Сертификат действителен и может быть проверен по
          уникальному ID на сайте
        </div>
      </div>
    </main>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script>
    function showMessage(text) {
      const message = document.createElement("div");
      message.className = "message";
      message.style.position = "fixed";
      message.style.top = "20px";
      message.style.right = "20px";
      message.style.zIndex = "9999";
      message.style.background = "#28a745";
      message.style.color = "white";
      message.style.padding = "10px 20px";
      message.style.borderRadius = "5px";
      message.textContent = text;

      document.body.appendChild(message);

      setTimeout(() => {
        message.remove();
      }, 2000);
    }
    document.addEventListener("DOMContentLoaded", function() {
      const urlParams = new URLSearchParams(window.location.search);
      const download = urlParams.get('download');

      if (download == 'png') {
        downloadAsPNG().then(() => {
          setTimeout(() => window.close(), 1000);
        });
      }
    });

    // Скачивание PNG
    // Скачивание PNG (возвращаем Promise)
    function downloadAsPNG() {
      // Возвращаем Promise
      return new Promise((resolve, reject) => {
        const certificate = document.getElementById("certificate-content");
        showMessage("Генерация PNG...");

        const actions = document.querySelector(".certificate-header");
        const message = document.querySelector(".certificate-container .message");

        if (actions) actions.style.visibility = "hidden";
        if (message) message.style.visibility = "hidden";

        html2canvas(certificate, {
            scale: 3,
            backgroundColor: "#ffffff",
            logging: false,
            allowTaint: true,
            useCORS: true,
            windowWidth: 1200,
          })
          .then((canvas) => {
            if (actions) actions.style.visibility = "visible";
            if (message) message.style.visibility = "visible";

            const link = document.createElement("a");
            link.download = `certificate-${new URL(window.location.href).searchParams.get('id')}.png`;
            link.href = canvas.toDataURL("image/png");
            link.click();

            showMessage("PNG успешно создан!");

            // Разрешаем Promise
            resolve();
          })
          .catch((error) => {
            console.error("Ошибка:", error);
            if (actions) actions.style.visibility = "visible";
            if (message) message.style.visibility = "visible";

            // Отклоняем Promise при ошибке
            reject(error);
          });
      });
    }
  </script>
  <?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>