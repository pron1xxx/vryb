const categories_buttons = document.querySelectorAll(".categories__category");
const reset_categories = document.querySelector("#reset_categories");

categories_buttons.forEach((element) => {
  element.addEventListener("click", function () {
    categories_buttons.forEach((button) => {
      button.classList.remove("active");
    });
    this.classList.add("active");

    let courses = document.querySelectorAll(".course");

    courses.forEach((course) => {
      if (course.dataset.category == this.textContent) {
        course.classList.remove("hidden");
      } else {
        course.classList.add("hidden");
      }
    });
  });
});

reset_categories.addEventListener("click", function () {
  let courses = document.querySelectorAll(".course");

  courses.forEach((course) => {
    course.classList.remove("hidden");
  });

  categories_buttons.forEach((button) => {
      button.classList.remove("active");
    });
});
