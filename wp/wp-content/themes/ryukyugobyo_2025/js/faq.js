function toggleFAQ(element) {
  const faqItem = element.parentElement;
  const answer = faqItem.querySelector(".faq-answer");
  const question = faqItem.querySelector(".faq-question");

  const isActive = question.classList.contains("active");

  if (isActive) {
    question.classList.remove("active");
    answer.classList.remove("active");
  } else {
    document
      .querySelectorAll(".faq-question.active")
      .forEach((activeQuestion) => {
        activeQuestion.classList.remove("active");
        activeQuestion.parentElement
          .querySelector(".faq-answer")
          .classList.remove("active");
      });

    question.classList.add("active");
    answer.classList.add("active");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".faq-question").forEach((question) => {
    question.setAttribute("tabindex", "0");
    question.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        toggleFAQ(this);
      }
    });
  });

  document.querySelectorAll(".faq-question").forEach((question) => {
    question.addEventListener("click", function () {
      setTimeout(() => {
        if (this.classList.contains("active")) {
          this.scrollIntoView({
            behavior: "smooth",
            block: "nearest",
          });
        }
      }, 300);
    });
  });
});
