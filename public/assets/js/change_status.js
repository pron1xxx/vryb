const forms = document.querySelectorAll(".change");
const messageDiv = document.getElementById("ajax-message");

if (!messageDiv) {
  const msgDiv = document.createElement("div");
  msgDiv.id = "ajax-message";
  msgDiv.style.cssText =
    "display: none; padding: 10px; margin: 10px 0; border-radius: 5px; position: fixed; top: 20px; right: 20px; z-index: 1000;";
  document.body.appendChild(msgDiv);
}

forms.forEach(function (form) {
  form.addEventListener("submit", async function (event) {
    // ✅ Добавил async
    event.preventDefault();

    try {
      const formData = new FormData(this);

      const response = await fetch(this.action, {
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
        showMessage("Статус успешно изменен", "success");
      } else {
        showMessage(data.message || "Произошла ошибка", "error");
        console.log(data.message)
      }
    } catch (e) {
      console.error("Ошибка:", e);
      console.log("URL запроса:", this.action);
      showMessage("Ошибка сети или сервера", "error");
    }
  });
});
