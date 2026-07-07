const CONFIG = {
  statusMap: {
    public: "Публичный",
    moderation: "На модерации",
    hidden: "Скрытый",
    development: "В разработке"
  },
  statuses: ["moderation", "hidden", "public"],
  selectors: {
    showMoreForm: "#show_more",
    searchForm: "#search_form",
    coursesContainer: "#courses",
    countInput: "#count",
    searchInput: "#search_input",
    searchHiddenInput: "#search_str"
  }
};

class CourseManager {
  constructor() {
    this.isAdmin = window.location.pathname === "/admin";
    this.init();
    this.addStyles();
  }

  init() {
    // Форма "Показать еще"
    const showMore = document.querySelector(CONFIG.selectors.showMoreForm);
    if (showMore) showMore.addEventListener("submit", (e) => this.showMore(e));

    // Форма поиска
    const search = document.querySelector(CONFIG.selectors.searchForm);
    if (search) search.addEventListener("submit", (e) => this.search(e));

    // Делегирование для форм изменения статуса
    document.addEventListener("submit", (e) => {
      if (e.target.matches(".change")) {
        e.preventDefault();
        this.changeStatus(e.target);
      }
    });
  }

  async showMore(e) {
    e.preventDefault();
    const form = e.target;
    const container = document.querySelector(CONFIG.selectors.coursesContainer);
    const countInput = document.querySelector(CONFIG.selectors.countInput);
    
    if (countInput && container) {
      countInput.value = container.children.length;
    }

    try {
      const formData = new FormData(form);
      const res = await fetch(form.action, { method: "POST", body: formData });
      const data = await res.json();

      if (data.success && data.courses) {
        data.courses.forEach(course => {
          container.insertAdjacentHTML("beforeend", this.courseHTML(course));
        });
        this.showMessage("Курсы загружены успешно", "success");
        
        if (data.courses.length === 0) form.style.display = "none";
      } else {
        this.showMessage(data.message || "Не удалось загрузить курсы", "error");
      }
    } catch (err) {
      console.error(err);
      this.showMessage("Ошибка при загрузке курсов", "error");
    }
  }

  async search(e) {
    e.preventDefault();
    const form = e.target;
    
    try {
      const formData = new FormData(form);
      const res = await fetch(form.action, { method: "POST", body: formData });
      const data = await res.json();

      if (data.success && data.courses) {
        const container = document.querySelector(CONFIG.selectors.coursesContainer);
        container.innerHTML = "";
        data.courses.forEach(course => {
          container.insertAdjacentHTML("beforeend", this.courseHTML(course));
        });
        this.showMessage(`Найдено ${data.courses.length} курсов`, "success");
      } else {
        this.showMessage(data.message || "Поиск не дал результатов", "error");
      }
    } catch (err) {
      console.error(err);
      this.showMessage("Ошибка при поиске", "error");
    }
  }

  async changeStatus(form) {
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn?.innerHTML || "Изменить статус";
    
    if (btn) {
      btn.innerHTML = '<span class="spinner"></span> Сохранение...';
      btn.disabled = true;
    }

    try {
      const formData = new FormData(form);
      const courseId = formData.get("course_id");
      const selectedStatus = formData.get("status");
      
      // Запрос комментария для статуса development
      if (selectedStatus === "development" && !formData.has("comment")) {
        const comment = await this.showCommentModal(courseId);
        if (!comment) {
          // Пользователь отменил ввод
          if (btn) {
            btn.innerHTML = originalText;
            btn.disabled = false;
          }
          return;
        }
        formData.append("comment", comment);
      }

      const res = await fetch(form.action, {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
      });

      const data = await res.json();

      if (data.success) {
        this.showMessage("Статус успешно изменен", "success");
        
        // Обновляем селект
        const course = document.querySelector(`.course[data-id="${courseId}"]`);
        const select = course?.querySelector('select[name="status"]');
        if (select) {
          select.innerHTML = this.statusOptions(selectedStatus);
        }

        // Для модерации - удаляем элемент
        if (this.isAdmin && selectedStatus === "public") {
          const moderationItem = form.closest(".moderation-item");
          if (moderationItem) {
            moderationItem.remove();
            const count = document.querySelector(".admin-section__count");
            if (count) {
              const current = parseInt(count.textContent) || 0;
              count.textContent = Math.max(0, current - 1);
            }
          }
        }
      } else {
        this.showMessage(data.message || "Ошибка при изменении статуса", "error");
      }
    } catch (err) {
      console.error(err);
      this.showMessage("Ошибка сети или сервера", "error");
    } finally {
      if (btn) {
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    }
  }

  showCommentModal(courseId) {
    return new Promise((resolve) => {
      const modal = document.createElement("div");
      modal.className = "modal-wrapper";
      modal.innerHTML = `
        <div class="modal-content">
          <h3 class="modal-title">Комментарий к изменению статуса</h3>
          <p class="modal-description">Укажите причину отправки курса на доработку</p>
          <div class="modal-field">
            <textarea id="comment-text" class="modal-textarea" 
              placeholder="Опишите, что нужно исправить..." minlength="20" rows="4" data-mask="name"></textarea>
          </div>
          <div class="modal-actions">
            <button class="modal-btn modal-btn-cancel">Отмена</button>
            <button class="modal-btn modal-btn-submit">Отправить</button>
          </div>
        </div>
      `;

      document.body.appendChild(modal);

      const closeModal = (result = null) => {
        document.body.removeChild(modal);
        resolve(result);
      };

      modal.addEventListener("click", (e) => {
        if (e.target === modal) closeModal(null);
      });

      modal.querySelector(".modal-btn-cancel").addEventListener("click", () => closeModal(null));

      modal.querySelector(".modal-btn-submit").addEventListener("click", () => {
        const comment = modal.querySelector("#comment-text").value.trim();
        if (comment.length < 20) {
          this.showMessage("Комментарий должен быть не менее 20 символов", "error");
          return;
        }
        closeModal(comment);
      });
    });
  }

  courseHTML(course) {
    if (this.isAdmin) {
      return `
        <div class="course" data-id="${course.id}" data-status="${course.status}">
          <a href="/course/show/?id=${course.id}">
            <div class="course__preview">
              <img src="${course.preview_url || ''}" alt="course_preview">
            </div>
          </a>
          <h2 class="course__title">${course.course_name}</h2>
          <div class="course__authorWrapper">
            <a href="/channel/?id=${course.author_id}" class="course__author">${course.channel_name || ''}</a>
          </div>
          <form action="/admin/change/status" method="post" class="change">
            <input type="hidden" name="course_id" value="${course.id}">
            <input type="hidden" name="csrf_token" value="${document.querySelector('input[name="csrf_token"]')?.value || ''}">
            <select name="status">${this.statusOptions(course.status)}</select>
            <button type="submit" class="adminCourse__button">Изменить статус</button>
          </form>
        </div>
      `;
    }
    
    return `
      <div class="course" data-id="${course.id}">
        <a href="/course/show/?id=${course.id}">
          <div class="course__preview">
            <img src="${course.preview_url || ''}" alt="course_preview">
          </div>
        </a>
        <h2 class="course__title">${course.course_name}</h2>
        <div class="course__authorWrapper">
          <a href="/channel/?id=${course.author_id}" class="course__author">${course.channel_name || ''}</a>
        </div>
      </div>
    `;
  }

  statusOptions(current) {
    let options = `<option value="${current}" selected>${CONFIG.statusMap[current] || current}</option>`;
    CONFIG.statuses.filter(s => s !== current).forEach(s => {
      options += `<option value="${s}">${CONFIG.statusMap[s]}</option>`;
    });
    return options;
  }

  showMessage(text, type) {
    let msg = document.getElementById("message");
    if (!msg) {
      msg = document.createElement("div");
      msg.id = "message";
      document.body.appendChild(msg);
    }

    const styles = {
      success: {
        backgroundColor: "#d4edda",
        color: "#155724",
        border: "1px solid #c3e6cb"
      },
      error: {
        backgroundColor: "#f8d7da",
        color: "#721c24",
        border: "1px solid #f5c6cb"
      },
      info: {
        backgroundColor: "#d1ecf1",
        color: "#0c5460",
        border: "1px solid #bee5eb"
      }
    };

    Object.assign(msg.style, {
      position: "fixed",
      top: "20px",
      right: "20px",
      padding: "10px 20px",
      borderRadius: "5px",
      zIndex: "9999",
      display: "block",
      ...styles[type]
    });

    msg.textContent = text;

    if (this.messageTimeout) clearTimeout(this.messageTimeout);
    this.messageTimeout = setTimeout(() => {
      msg.style.display = "none";
    }, 5000);
  }

  addStyles() {
    const style = document.createElement("style");
    style.textContent = `
      .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 5px;
        vertical-align: middle;
      }
      
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
      
      .adminCourse__button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }

      .change select {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-right: 8px;
      }

      /* Модальное окно */
      .modal-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        animation: fadeIn 0.3s ease;
      }

      .modal-content {
        background: #2a2a2a;
        padding: 30px;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        animation: slideUp 0.4s ease;
      }

      .modal-title {
        font-size: 24px;
        color: #fff;
        margin-bottom: 10px;
        text-align: center;
      }

      .modal-description {
        color: #aaa;
        margin-bottom: 20px;
        text-align: center;
      }

      .modal-field {
        margin-bottom: 20px;
      }

      .modal-textarea {
        width: 100%;
        padding: 12px;
        background: #3a3a3a;
        border: 1px solid #4a4a4a;
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        resize: vertical;
      }

      .modal-textarea:focus {
        outline: none;
        border-color: #007bff;
      }

      .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
      }

      .modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
      }

      .modal-btn-cancel {
        background: #6c757d;
        color: #fff;
      }

      .modal-btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-2px);
      }

      .modal-btn-submit {
        background: #007bff;
        color: #fff;
      }

      .modal-btn-submit:hover {
        background: #0056b3;
        transform: translateY(-2px);
      }

      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
      }

      @keyframes slideUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
    `;
    document.head.appendChild(style);
  }
}

// Запуск
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => new CourseManager());
} else {
  new CourseManager();
}