const swiper = new Swiper(".tejun__swiper", {
  speed: 500,
  spaceBetween: 20,
  slidesPerView: "auto",
  centeredSlides: false,
  loop: true,
  navigation: {
    nextEl: ".tejun__button--next",
    prevEl: ".tejun__button--prev",
  },
  breakpoints: {
    768: {
      spaceBetween: 30,
      initialSlide: 0,
    },
    1366: {
      spaceBetween: 40,
      initialSlide: 0,
    },
  },
});
