document.addEventListener("DOMContentLoaded", function () {
  const mobileDropdown = document.querySelector(".mobile-dropdown");
  const dropdownHeader = document.querySelector(".dropdown-header");
  const dropdownTitle = document.querySelector(".dropdown-title");
  const navItems = document.querySelectorAll(".nav-item");
  const sections = document.querySelectorAll(".content-section");

  dropdownHeader.addEventListener("click", function () {
    mobileDropdown.classList.toggle("open");
  });

  document.addEventListener("click", function (event) {
    if (!mobileDropdown.contains(event.target)) {
      mobileDropdown.classList.remove("open");
    }
  });

  navItems.forEach((item) => {
    item.addEventListener("click", function () {
      dropdownTitle.textContent = this.textContent;
      mobileDropdown.classList.remove("open");
    });
  });

  window.addEventListener("scroll", function () {
    let current = "";
    sections.forEach((section) => {
      const sectionTop = section.offsetTop;
      if (window.scrollY >= sectionTop - 100) {
        current = section.getAttribute("id");
      }
    });
  });
});
