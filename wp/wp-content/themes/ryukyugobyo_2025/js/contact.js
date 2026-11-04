document.addEventListener("DOMContentLoaded", function () {
  const selects = document.querySelectorAll(".contact__select");

  selects.forEach(function (select) {
    updateColor(select);

    select.addEventListener("change", function () {
      updateColor(this);
    });
  });
});

function updateColor(selectElement) {
  if (selectElement.value === "") {
    selectElement.style.color = "#C7C7C7";
    selectElement.style.fontWeight = "700";
  } else {
    selectElement.style.color = "#000";
    selectElement.style.fontWeight = "400";
  }
}



document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("qty");

  if (select) {

    updateColor(select);

    select.addEventListener("change", function () {
      updateColor(this);
    });
  }
});

function updateColor(selectElement) {
  if (selectElement.value === "") {
    selectElement.style.color = "#c7c7c7";
  } else {
    selectElement.style.color = "#000";
  }
}