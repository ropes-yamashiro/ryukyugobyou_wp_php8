document.addEventListener("DOMContentLoaded", function () {
  const types = document.querySelectorAll(".filter-type");
  const indicator = document.querySelector(".filter-type-indicator");

  positionIndicator(0);

  types.forEach((type) => {
    type.addEventListener("click", function () {
      types.forEach((t) => t.classList.remove("filter-type--active"));

      this.classList.add("filter-type--active");

      const index = parseInt(this.getAttribute("data-index"));
      positionIndicator(index);
    });
  });

  function positionIndicator(index) {
    const activeTab = document.querySelector(
      `.filter-type[data-index="${index}"]`
    );
    if (activeTab) {
      const tabWidth = activeTab.offsetWidth;
      const tabLeft = activeTab.offsetLeft;

      indicator.style.width = `${tabWidth}px`;
      indicator.style.left = `${tabLeft}px`;
    }
  }

  window.positionIndicator = positionIndicator;

  window.addEventListener("resize", function () {
    const activeTab = document.querySelector(".filter-type--active");
    if (activeTab) {
      const index = parseInt(activeTab.getAttribute("data-index"));
      positionIndicator(index);
    }
  });
});
