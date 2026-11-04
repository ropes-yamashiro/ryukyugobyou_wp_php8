var swiperProblems = null;
var swiperBenefits = null;

function initswiperProblems() {
  if (!swiperProblems) {
    swiperProblems = new Swiper(".problems__swiper", {
      slidesPerView: "auto",
      spaceBetween: 24,
      centeredSlides: false,
      breakpoints: {
        768: {
          spaceBetween: 30,
        },
      },
    });
  }
}

function destroyswiperProblems() {
  if (swiperProblems) {
    swiperProblems.destroy(true, true);
    swiperProblems = null;
  }
}

function initswiperBenefits() {
  if (!swiperBenefits) {
    swiperBenefits = new Swiper(".benefits__swiper", {
      slidesPerView: "auto",
      spaceBetween: 20,
      centeredSlides: false,
      breakpoints: {
        768: {
          spaceBetween: 30,
        },
      },
    });
  }
}

function destroyswiperBenefits() {
  if (swiperBenefits) {
    swiperBenefits.destroy(true, true);
    swiperBenefits = null;
  }
}

const swiperFloor = new Swiper(".floor__swiper", {
  slidesPerView: "auto",
  spaceBetween: 20,
  centeredSlides: true,

  navigation: {
    nextEl: ".floor__button--next",
    prevEl: ".floor__button--prev",
  },
  breakpoints: {
    540: { loop: true },
    768: {
      loop: false,
    },
  },
});

const mediaQuery = window.matchMedia("(max-width: 767px)");

function handleScreenSizeChange(e) {
  if (e.matches) {
    initswiperProblems();
    initswiperBenefits();
  } else {
    destroyswiperProblems();
    destroyswiperBenefits();
  }
}

handleScreenSizeChange(mediaQuery);

mediaQuery.addEventListener("change", handleScreenSizeChange);

$(function () {
  var urlHash = location.hash;
  var headerHeight = $(".header").outerHeight() + 10;

  if (urlHash) {
    $("body,html").stop().scrollTop(0);
    setTimeout(function () {
      var target = $(urlHash);
      if (target.length) {
        var position = target.offset().top - headerHeight;
        $("body,html").stop().animate({ scrollTop: position }, 600);
      }
    }, 100);
  }

  $('a[href^="#"]').click(function () {
    var href = $(this).attr("href");
    var target = $(href);
    if (target.length) {
      var position = target.offset().top - headerHeight;
      $("body,html").stop().animate({ scrollTop: position }, 600);
    }
  });
});
