const coursesDivSearh = document.querySelector("#courses");
const searchForm = document.querySelector("#search_form");

searchForm.addEventListener("submit", async function (event) {
  // Прерываем отправку формы
  event.preventDefault();
  
  try {
    const formData = new FormData(searchForm);

    const response = await fetch(searchForm.action, {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      const text = await response.text();
      console.error("Сервер вернул не JSON:", text.substring(0, 200));
      throw new Error("Сервер вернул некорректный ответ");
    }

    const data = await response.json();

    if (data.success) {
      coursesDivSearh.replaceChildren();

      if (location.pathname == "/admin") {
        data.courses.forEach(function (course) {
          switch (course.status) {
            case "public":
              courseStatus = "Публичный";
              break;
            case "moderation":
              courseStatus = "На модерации";
              break;
            case "hidden":
              courseStatus = "Скрытый";
              break;
          }
          coursesDiv.insertAdjacentHTML(
            "beforeend",
            `
        <div class="course" data-category="${courseStatus}">
                    <a href="/course/show/?id=${course.id}"> <div class="course__preview"> <img src="${course.preview_url}" alt="course_preview"> </div> <a>
                    <h2 class="course__title"> ${course.course_name} </h2>
                    <div class="course__authorWrapper">
                        <a href="/channel/?id=${course.author_id}" class="course__author"> ${course.channel_name} </a>
                    </div>
            </div>
        `
          );
        });
      } else {
        data.courses.forEach(function (course) {
          coursesDiv.insertAdjacentHTML(
            "beforeend",
            `
        <div class="course" data-category="${course.category}">
                    <a href="/course/show/?id=${course.id}"> <div class="course__preview"> <img src="${course.preview_url}" alt="course_preview"> </div> <a>
                    <h2 class="course__title"> ${course.course_name} </h2>
                    <div class="course__authorWrapper">
                        <a href="/channel/?id=${course.author_id}" class="course__author"> ${course.channel_name} </a>
                    </div>
            </div>
        `
          );
        });
      }

      showMessage("Поиск выполнен", "success");
    } else {
      showMessage(data.message, "error");
    }
  } catch {
    showMessage("Ошибка запроса", "error");
  }
});

function showMessage(text, type) {
  msgDiv = document.querySelector("#message");
  if (!msgDiv) return;

  msgDiv.textContent = text;
  msgDiv.className = "";

  // Стили в зависимости от типа
  const styles = {
    success: {
      backgroundColor: "#d4edda",
      color: "#155724",
      border: "1px solid #c3e6cb",
    },
    error: {
      backgroundColor: "#f8d7da",
      color: "#721c24",
      border: "1px solid #f5c6cb",
    },
    info: {
      backgroundColor: "#d1ecf1",
      color: "#0c5460",
      border: "1px solid #bee5eb",
    },
  };

  Object.assign(msgDiv.style, styles[type] || styles.info);
  msgDiv.style.display = "block";

  // Автоматически скрываем через 5 секунд
  setTimeout(() => {
    msgDiv.style.display = "none";
  }, 5000);
}
