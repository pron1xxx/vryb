const delete_forms = document.querySelectorAll(".complete");

delete_forms.forEach(function (form) {
  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const formData = new FormData(form);

    const actionUrl = form.action.startsWith("/")
      ? window.location.origin + form.action
      : form.action;

    const response = await fetch(actionUrl, {
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
      showMessage(data.message || "Успешно!", "success");
      document.location.href = `/course/show/?id=${form.id}`
    } else {
      showMessage(data.message || "Произошла ошибка", "error");
    }
  });
});
