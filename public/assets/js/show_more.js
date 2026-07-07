const form = document.querySelector("#show_more");
const coursesDiv = document.querySelector("#courses");
const countInput = document.querySelector("#count");
let msgDiv = document.querySelector("#message");
const searchInput = document.querySelector("#search_input");
const searchHiddenInput = document.querySelector("#search_str");

form.addEventListener("submit", async function (event) {
  // Прерываем отправку формы
  event.preventDefault();

  if (!msgDiv) {
    const massageDiv = document.createElement("div");
    massageDiv.id = "message";
    massageDiv.style.cssText =
      "display: none; padding: 10px; margin: 10px 0; border-radius: 5px; position: fixed; top: 20px; right: 20px; z-index: 1000;";
    document.body.appendChild(massageDiv);
  }
  msgDiv = document.querySelector("#message");

  try {
    countInput.value = coursesDiv.children.length;
  } catch {
    throw new Error("Возникла непредвиденная ошибка");
  }

  try {
    if (searchInput.value != "") {
      searchHiddenInput.value = searchInput.value;
    }
    const formData = new FormData(form);

    const response = await fetch(form.action, {
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
      if (location.pathname === "/admin") {
       console.log(location.pathname, 1)
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
        <a href="/course/show/?id=${course.id}">
            <div class="course__preview">
                <img src="${course.preview_url}" alt="course_preview">
            </div>
        </a>
        <h2 class="course__title">${course.course_name}</h2>
        <div class="course__authorWrapper">
            <a href="/channel/?id=${course.author_id}" class="course__author">
                ${course.channel_name}
            </a>
        </div>
        <form action="/admin/change/status" method="post" class="change">
            <input type="hidden" name="course_id" value="${course.id}">
            <select name="status">
                ${generateStatusOptions(course.status)}
            </select>
            <button type="submit" class="adminCourse__button">
                Изменить статус
            </button>
        </form>
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

      showMessage("Курсы загружены успешно", "success");
    } else {
      showMessage(data.message, "error");
    }
  } catch {}
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

function switchStatus(status) {
    switch (status) {
        case "public":
            return "Публичный";
        case "moderation":
            return "На модерации";
        case "hidden":
            return "Скрытый";
        default:
            return status;
    }
}

function generateStatusOptions(currentStatus) {
  const statusArray = ["moderation", "hidden", "public"];
  let options = "";

  // Текущий статус (selected)
  options += `
        <option value="${currentStatus}" selected>
            ${switchStatus(currentStatus)}
        </option>`;

  // Остальные статусы
  statusArray.forEach((status) => {
    if (status !== currentStatus) {
      options += `
                <option value="${status}">
                    ${switchStatus(status)}
                </option>`;
    }
  });

  return options;
}

