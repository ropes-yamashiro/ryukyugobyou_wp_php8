const swiper = new Swiper(".swiper", {
  speed: 500,
  spaceBetween: 20,
  slidesPerView: "auto",
  centeredSlides: false,
  loop: true,
  initialSlide: 3,
  navigation: {
    nextEl: ".gallery__button--next",
    prevEl: ".gallery__button--prev",
  },
  breakpoints: {
    550: {
      centeredSlides: true,
    },

    768: {
      centeredSlides: true,
      spaceBetween: 48,
    },
    1366: {
      centeredSlides: true,
      spaceBetween: 72,
    },
  },
});
