function showMessage(text, type) {
  let msgDiv = document.getElementById("ajax-message");
  if (!msgDiv) {
    const msgDiv = document.createElement("div");
    msgDiv.id = "ajax-message";
    msgDiv.style.cssText =
      "display: none; padding: 10px; margin: 10px 0; border-radius: 5px; position: fixed; top: 20px; right: 20px; z-index: 1005;";
    document.body.appendChild(msgDiv);
  }
  msgDiv = document.getElementById("ajax-message");

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
