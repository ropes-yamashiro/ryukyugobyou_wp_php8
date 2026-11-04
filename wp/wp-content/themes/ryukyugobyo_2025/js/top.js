document.addEventListener("DOMContentLoaded", function () {
  const video = document.getElementById("mainVideo");
  const loader = document.getElementById("videoLoader");
  const fvSection = document.querySelector(".fv");
  const loaderImage = document.querySelector(".fv__loader-image");

  const CONFIG = {
    minShowTime: 400,
    maxWaitTime: 5000,
    checkInterval: 100,
  };

  let loaderStartTime = null;
  let isLoaderVisible = false;
  let hideTimeout = null;

  function showLoader() {
    if (isLoaderVisible) return;

    loaderStartTime = Date.now();
    isLoaderVisible = true;

    loader.classList.remove("hidden");
    video.classList.remove("loaded");
    fvSection.classList.remove("content-loaded");

    console.log("ローダーが表示されました");
  }

  function hideLoader() {
    if (!isLoaderVisible) return;

    const elapsed = Date.now() - loaderStartTime;
    const remainingTime = Math.max(0, CONFIG.minShowTime - elapsed);

    if (hideTimeout) {
      clearTimeout(hideTimeout);
    }

    hideTimeout = setTimeout(() => {
      isLoaderVisible = false;

      loader.classList.add("hidden");
      video.classList.add("loaded");

      setTimeout(() => {
        fvSection.classList.add("content-loaded");
      }, 300);

      console.log(
        `ローダーが${Date.now() - loaderStartTime}ミリ秒後に非表示になりました`
      );
    }, remainingTime);
  }

  function checkVideoReady() {
    if (video.readyState >= 3) {
      hideLoader();
      return true;
    }
    return false;
  }

  let needsLoader = video.readyState < 3;

  if (needsLoader) {
    showLoader();
  } else {
    console.log("動画はすでにキャッシュから準備完了、ローダーは不要です");
    isLoaderVisible = false;
    loader.classList.add("hidden");
  }

  video.addEventListener("loadstart", function () {
    console.log("動画の読み込み開始");
    showLoader();
  });

  video.addEventListener("loadeddata", function () {
    console.log("動画データが読み込まれました");
    if (isLoaderVisible) {
      hideLoader();
    }
  });

  video.addEventListener("canplay", function () {
    console.log("動画が再生可能になりました");
    if (isLoaderVisible) {
      hideLoader();
    }
  });

  video.addEventListener("canplaythrough", function () {
    console.log("動画が完全に準備完了");
    if (isLoaderVisible) {
      hideLoader();
    }
  });

  video.addEventListener("waiting", function () {
    console.log("動画がバッファリング中");

    if (!video.paused) {
      showLoader();
    }
  });

  video.addEventListener("playing", function () {
    console.log("動画が再生中");

    if (isLoaderVisible) {
      hideLoader();
    }
  });

  video.addEventListener("error", function (e) {
    console.error("動画読み込みエラー:", e);
    hideLoader();
  });

  video.addEventListener("emptied", function () {
    console.log("動画がクリアされました");
    showLoader();
  });

  video.addEventListener("stalled", function () {
    console.log("読み込みが停止しました");
    showLoader();
  });

  const readinessCheck = setInterval(() => {
    if (isLoaderVisible && checkVideoReady()) {
      clearInterval(readinessCheck);
    }
  }, CONFIG.checkInterval);

  setTimeout(() => {
    if (isLoaderVisible) {
      console.warn("タイムアウトによりローダーを強制非表示");
      hideLoader();
    }
    clearInterval(readinessCheck);
  }, CONFIG.maxWaitTime);

  window.changeVideoSource = function (newSrc) {
    console.log("動画ソースの変更:", newSrc);
    showLoader();
    video.src = newSrc;
    video.load();
  };

  document.addEventListener("visibilitychange", function () {
    if (!document.hidden && video.paused && isLoaderVisible) {
      setTimeout(checkVideoReady, 100);
    }
  });

  loaderImage.classList.add("breathe");

  window.videoDebug = {
    getReadyState: () => video.readyState,
    isLoaderVisible: () => isLoaderVisible,
    getElapsedTime: () => (loaderStartTime ? Date.now() - loaderStartTime : 0),
    forceHide: () => hideLoader(),
    forceShow: () => showLoader(),
  };
});
var swiperIntro = null;

function initswiperIntro() {
  if (!swiperIntro) {
    swiperIntro = new Swiper(".intro__gallery", {
      loop: true,

      slidesPerView: "auto",
      spaceBetween: 10,
      centeredSlides: false,
      speed: 800,
      autoplay: true,
      breakpoints: {
        361: {
          spaceBetween: 15,
        },
      },
    });
  }
}

function destroyswiperIntro() {
  if (swiperIntro) {
    swiperIntro.destroy(true, true);
    swiperIntro = null;
  }
}
const mediaQuery = window.matchMedia("(max-width: 649px)");

function handleScreenSizeChange(e) {
  if (e.matches) {
    initswiperIntro();
  } else {
    destroyswiperIntro();
  }
}

handleScreenSizeChange(mediaQuery);

mediaQuery.addEventListener("change", handleScreenSizeChange);
