//popup menu

document.addEventListener("DOMContentLoaded", function () {
  var menuButton = document.getElementById("btn_spmenu");
  var menu = document.querySelector(".popup");
  var closeButton = document.querySelector(".popup__close");

  menuButton.addEventListener("click", function () {
    menuButton.classList.toggle("open");
    menuButton.classList.toggle("hidden");
    menu.classList.toggle("popup--open");
    closeButton.classList.remove("hidden");
  });

  document.querySelectorAll(".menu-item").forEach(function (item) {
    item.addEventListener("click", function () {
      menuButton.classList.remove("open");
      menu.classList.remove("popup--open");
      menuButton.classList.remove("hidden");
      closeButton.classList.add("hidden");
    });
  });

  var closeButton = document.querySelector(".popup__close");
  closeButton.addEventListener("click", function () {
    menuButton.classList.remove("open");
    menuButton.classList.remove("hidden");
    menu.classList.remove("popup--open");
    closeButton.classList.add("hidden");
  });
});

document.addEventListener("DOMContentLoaded", function () {
  function handleScrollAnimations() {
    var elements = document.querySelectorAll(".fadeIn, .fadeInUp");
    var animated = "animated";
    var scrollPos = window.pageYOffset || document.documentElement.scrollTop;
    var wh = window.innerHeight;

    elements.forEach(function (element) {
      var rect = element.getBoundingClientRect();
      var elementOffset = rect.top + scrollPos;
      var elementHeight = rect.height;

      var triggerOffset = elementHeight < 350 ? 100 : 200;

      if (scrollPos > elementOffset - wh + triggerOffset) {
        element.classList.add(animated);
      }
    });
  }

  handleScrollAnimations();

  window.addEventListener("scroll", handleScrollAnimations);

  window.addEventListener("resize", handleScrollAnimations);
});

/* 画面幅での画像の切り替え */

jQuery(function ($) {
  var $setElem = $('.switch');
  var pcName = '-pc';
  var spName = '-sp';
  var replaceWidth = 767;

  function imageSwitch() {
    var windowWidth = parseInt($(window).width(), 10);

    $setElem.each(function () {
      var $this = $(this);

      if (windowWidth < replaceWidth) {
        $this.attr('src', $this.attr('src').replace(pcName, spName)).css({
          visibility: 'visible'
        });
      } else {
        $this.attr('src', $this.attr('src').replace(spName, pcName)).css({
          visibility: 'visible'
        });
      }
    });
  }

  imageSwitch();

  var resizeTimer;
  $(window).on('load resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      imageSwitch();
    }, 200);
  });
});