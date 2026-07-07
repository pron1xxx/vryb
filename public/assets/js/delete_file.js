const delete_forms = document.querySelectorAll(".delete-button");

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
    } else {
      showMessage(data.message || "Произошла ошибка", "error");
    }

    file_div = form.parentElement.parentElement
    files_div = file_div.parentElement.parentElement
    count_files = files_div.childNodes['1'].childNodes['3'].childNodes['1']
    new_count = parseInt(count_files.textContent) - 1
    count_files.textContent = new_count;
    file_div.remove()
  });
});
