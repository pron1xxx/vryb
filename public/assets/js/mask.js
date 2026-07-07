class InputMask {
  constructor() {
    this.init();
  }

  init() {
    document.addEventListener("focusin", (e) => {
      if (e.target.matches("[data-mask]")) {
        this.setupInput(e.target);
      }
    });

    document.addEventListener("input", (e) => {
      if (e.target.matches("[data-mask]")) {
        this.applyMask(e.target);
      }
    });

    document.addEventListener(
      "blur",
      (e) => {
        if (e.target.matches("[data-mask]")) {
          this.validateField(e.target);
        }
      },
      true,
    );

    this.scanForMasks();
  }

  scanForMasks() {
    document.querySelectorAll("[data-mask]").forEach((input) => {
      this.setupInput(input);
    });
  }

  setupInput(input) {
    if (input.hasAttribute("data-mask-setup")) return;

    const maskType = input.dataset.mask;

    // Добавляем подсказку о формате
    const placeholders = {
      name: "Иван Иванов",
      phone: "+7 (999) 999-99-99",
      date: "ДД.ММ.ГГГГ",
      time: "ЧЧ:ММ",
      email: "example@mail.com",
      login: "username123",
      text: "Текст...",
    };

    if (placeholders[maskType]) {
      input.placeholder = input.placeholder || placeholders[maskType];
    }

    // Для date и time используем нативные контролы если нужно
    if (maskType === "date" && input.type !== "date") {
      // Оставляем текстовый ввод с маской
    }

    input.setAttribute("data-mask-setup", "true");
  }

  applyMask(input) {
    const maskType = input.dataset.mask;
    let value = input.value;
    let cursorPos = input.selectionStart;

    this.removeError(input);

    // Сохраняем позицию курсора для некоторых масок
    const oldLength = value.length;

    switch (maskType) {
      case "name":
        value = this.nameMask(value);
        break;
      case "phone":
        value = this.phoneMask(value);
        break;
      case "date":
        value = this.dateMask(value);
        break;
      case "time":
        value = this.timeMask(value);
        break;
      case "email":
        value = value.replace(/\s/g, "");
        break;
      case "number":
        value = value.replace(/[^\d]/g, "");
        break;
      case "float":
        value = value.replace(/[^\d.,]/g, "").replace(",", ".");
        const parts = value.split(".");
        if (parts.length > 2) {
          value = parts[0] + "." + parts.slice(1).join("");
        }
        break;
      case "cyrillic":
        value = value.replace(/[^а-яА-ЯёЁ\s]/g, "");
        break;
      case "text":
        value = value;
        break;
      case "login":
        value = value.replace(/[^a-zA-Z0-9_]/g, "");
        break;
    }

    if (value !== input.value) {
      input.value = value;

      // Восстанавливаем позицию курсора
      const newLength = value.length;
      const diff = newLength - oldLength;
      if (cursorPos + diff >= 0) {
        input.setSelectionRange(cursorPos + diff, cursorPos + diff);
      }
    }
  }

  validateField(input) {
    const maskType = input.dataset.mask;
    const value = input.value.trim();

    // Определяем минимальную длину в зависимости от типа
    let minLength = parseInt(input.getAttribute("minlength")) || 0;
    if (!minLength) {
      switch (maskType) {
        case "name":
          minLength = 3;
          break;
        case "phone":
          minLength = 18;
          break;
        case "date":
          minLength = 10;
          break;
        case "email":
          minLength = 5;
          break;
        case "login":
          minLength = 3;
          break;
        case "text":
          minLength = 1;
          break;
        default:
          minLength = 1;
      }
    }

    // Максимальная длина
    let maxLength = parseInt(input.getAttribute("maxlength")) || 999;
    if (!input.getAttribute("maxlength")) {
      switch (maskType) {
        case "name":
          maxLength = 100;
          break;
        case "phone":
          maxLength = 18;
          break;
        case "date":
          maxLength = 10;
          break;
        case "login":
          maxLength = 50;
          break;
        case "text":
          maxLength = 1000;
          break;
      }
    }

    // Проверка на пустое значение для required
    if (input.hasAttribute("required") && !value) {
      this.showError(input, "Поле обязательно для заполнения");
      return false;
    }

    // Проверка длины (только если есть значение)
    if (value) {
      if (value.length < minLength) {
        this.showError(input, `Минимум ${minLength} символов`);
        return false;
      }

      if (value.length > maxLength) {
        this.showError(input, `Максимум ${maxLength} символов`);
        return false;
      }
    }

    // Специфичные проверки
    if (value) {
      switch (maskType) {
        case "email":
          if (!this.isValidEmail(value)) {
            this.showError(input, "Введите корректный email");
            return false;
          }
          break;
        case "phone":
          const digits = value.replace(/\D/g, "");
          if (digits.length < 11) {
            this.showError(input, "Введите полный номер телефона (11 цифр)");
            return false;
          }
          break;
        case "date":
          if (input.type !== "date" && !this.isValidDate(value)) {
            this.showError(input, "Введите корректную дату (ДД.ММ.ГГГГ)");
            return false;
          }
          break;
        case "time":
          if (!this.isValidTime(value)) {
            this.showError(input, "Введите корректное время (ЧЧ:ММ)");
            return false;
          }
          break;
        case "login":
          if (!this.isValidLogin(value)) {
            this.showError(input, "Только латинские буквы и цифры");
            return false;
          }
          break;
      }
    }

    this.removeError(input);
    return true;
  }

  // Маски
  nameMask(value) {
    // Только буквы, пробелы, дефисы
    return value
      .replace(/[^a-zA-Zа-яА-ЯёЁ\s\-]/g, "")
      .replace(/\s+/g, " ")
      .replace(/^-+/, "")
      .replace(/-+/g, "-")
      .trim();
  }

  phoneMask(value) {
    // +7 (999) 999-99-99
    let numbers = value.replace(/\D/g, "");
    if (numbers.length === 0) return "";

    // Если начинается с 8, заменяем на 7
    if (numbers[0] === "8") numbers = "7" + numbers.slice(1);
    // Если не начинается с 7, добавляем 7
    if (numbers[0] !== "7") numbers = "7" + numbers;

    // Ограничиваем длину
    numbers = numbers.slice(0, 11);

    let result = "+" + numbers[0];

    if (numbers.length > 1) {
      result += " (" + numbers.slice(1, 4);
    }
    if (numbers.length >= 5) {
      result += ") " + numbers.slice(4, 7);
    }
    if (numbers.length >= 8) {
      result += "-" + numbers.slice(7, 9);
    }
    if (numbers.length >= 10) {
      result += "-" + numbers.slice(9, 11);
    }

    return result;
  }

  dateMask(value) {
    // Если это нативный date input, не применяем маску
    if (this.isNativeDateInput(value)) return value;

    // ДД.ММ.ГГГГ
    value = value.replace(/\D/g, "");
    if (value.length > 8) value = value.slice(0, 8);

    let result = "";
    for (let i = 0; i < value.length; i++) {
      if (i === 2 || i === 4) result += ".";
      result += value[i];
    }

    return result;
  }

  timeMask(value) {
    // ЧЧ:ММ
    value = value.replace(/\D/g, "");
    if (value.length > 4) value = value.slice(0, 4);

    let result = "";
    for (let i = 0; i < value.length; i++) {
      if (i === 2) result += ":";
      result += value[i];
    }

    return result;
  }

  // Валидаторы
  isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  isValidDate(date) {
    // Для нативного date input
    if (this.isNativeDateInput(date)) return true;

    const parts = date.split(".");
    if (parts.length !== 3) return false;

    const day = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10);
    const year = parseInt(parts[2], 10);

    if (isNaN(day) || isNaN(month) || isNaN(year)) return false;
    if (month < 1 || month > 12) return false;
    if (day < 1 || day > 31) return false;
    if (year < 1900 || year > 2100) return false;

    // Проверка на реальное существование даты
    const dateObj = new Date(year, month - 1, day);
    return (
      dateObj.getFullYear() === year &&
      dateObj.getMonth() === month - 1 &&
      dateObj.getDate() === day
    );
  }

  isValidTime(time) {
    const parts = time.split(":");
    if (parts.length !== 2) return false;

    const hours = parseInt(parts[0], 10);
    const minutes = parseInt(parts[1], 10);

    if (isNaN(hours) || isNaN(minutes)) return false;
    if (hours < 0 || hours > 23) return false;
    if (minutes < 0 || minutes > 59) return false;

    return true;
  }

  isValidLogin(login) {
    return /^[a-zA-Z0-9_]+$/.test(login);
  }

  isNativeDateInput(value) {
    return value && value.match(/^\d{4}-\d{2}-\d{2}$/);
  }

  // Обработка ошибок
  showError(input, message) {
    input.classList.add("error");

    let errorElement = input.parentNode.querySelector(".field-error");
    if (!errorElement) {
      errorElement = document.createElement("span");
      errorElement.className = "field-error";
      input.parentNode.appendChild(errorElement);
    }

    errorElement.textContent = message;
  }

  removeError(input) {
    input.classList.remove("error");
    const errorElement = input.parentNode.querySelector(".field-error");
    if (errorElement) {
      errorElement.remove();
    }
  }
}

// Инициализация
document.addEventListener("DOMContentLoaded", () => {
  window.inputMask = new InputMask();
});
