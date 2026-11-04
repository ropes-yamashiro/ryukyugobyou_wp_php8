const swiper = new Swiper(".gallery__swiper", {
  speed: 500,
  spaceBetween: 20,
  slidesPerView: "auto",
  centeredSlides: false,
  loop: true,
  navigation: {
    nextEl: ".gallery__button--next",
    prevEl: ".gallery__button--prev",
  },
  breakpoints: {
    768: {
      centeredSlides: true,
      spaceBetween: 48,
    },
    1366: {
      centeredSlides: true,
      spaceBetween: 80,
    },
  },
});

var swiperEvents = null;
function initswiperEvents() {
  if (!swiperEvents) {
    swiperEvents = new Swiper(".events__swiper", {
      speed: 500,
      spaceBetween: 20,
      slidesPerView: "auto",
      centeredSlides: false,
      loop: true,

      breakpoints: {
        768: {
          spaceBetween: 48,
        },
        1366: {
          spaceBetween: 72,
        },
      },
    });
  }
}

function destroyswiperEvents() {
  if (swiperEvents) {
    swiperEvents.destroy(true, true);
    swiperEvents = null;
  }
}

const mediaQuery = window.matchMedia("(max-width: 767px)");

function handleScreenSizeChange(e) {
  if (e.matches) {
    initswiperEvents();
  } else {
    destroyswiperEvents();
  }
}

handleScreenSizeChange(mediaQuery);

mediaQuery.addEventListener("change", handleScreenSizeChange);
