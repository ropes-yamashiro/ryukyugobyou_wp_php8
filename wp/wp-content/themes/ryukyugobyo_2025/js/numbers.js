const data = [
  { period: "2-3月", value: 900, year: "2022" },
  { period: "4-6月", value: 1000, year: "2022" },
  { period: "7-9月", value: 1100, year: "2022" },
  { period: "10-12月", value: 1200, year: "2022" },
  { period: "1-3月", value: 1300, year: "2023" },
  { period: "4-6月", value: 1400, year: "2023" },
  { period: "7-9月", value: 1500, year: "2023" },
  { period: "10-12月", value: 1600, year: "2023" },
  { period: "1-3月", value: 1700, year: "2024" },
  { period: "4-6月", value: 1800, year: "2024" },
  { period: "7-9月", value: 1900, year: "2024" },
  { period: "10-12月", value: 2000, year: "2024" },
  { period: "1-3月", value: 2100, year: "2025" },
];

function createChart() {
  const chart = document.getElementById("chart");
  chart.innerHTML = "";

  const years = ["2022", "2023", "2024", "2025"];
  const yearGroups = {
    2022: data.slice(0, 4),
    2023: data.slice(4, 8),
    2024: data.slice(8, 12),
    2025: data.slice(12, 13),
  };

  years.forEach((year) => {
    const yearGroup = document.createElement("div");
    yearGroup.className = "year-group";
    yearGroup.style.position = "relative";

    const yearLabel = document.createElement("div");
    yearLabel.className = "year-label";
    yearLabel.textContent = year + "年";
    yearGroup.appendChild(yearLabel);

    yearGroups[year].forEach((item) => {
      const bar = document.createElement("div");
      bar.className = "bar";
      bar.style.height = `${(item.value / 2200) * 333}px`;

      if (year === "2022") {
        bar.style.background = "#FFE16A";
      } else if (year === "2023") {
        bar.style.background = "#FFC771";
      } else if (year === "2024") {
        bar.style.background = "#FF9E74";
      } else if (year === "2025") {
        bar.style.background = "#F17C7C";
      }

      const label = document.createElement("div");
      label.className = "bar-label";
      label.textContent = item.period;

      const value = document.createElement("div");
      value.className = "bar-value";
      value.textContent = item.value + "件";

      bar.appendChild(label);
      bar.appendChild(value);
      yearGroup.appendChild(bar);
    });

    chart.appendChild(yearGroup);
  });
}

function animateCounter() {
  const counter = document.getElementById("counter");
  const startValue = 900;
  const endValue = 2200;
  const duration = 3000;
  const startTime = performance.now();

  function updateCounter(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const easeOutQuart = 1 - Math.pow(1 - progress, 4);
    const currentValue = Math.round(
      startValue + (endValue - startValue) * easeOutQuart
    );

    counter.innerHTML = `${currentValue.toLocaleString()}<span class="counter-unit">件</span>`;

    if (progress < 1) {
      requestAnimationFrame(updateCounter);
    }
  }

  requestAnimationFrame(updateCounter);
}

function startAnimation() {
  createChart();

  animateCounter();

  const bars = document.querySelectorAll(".bar");
  const yearLabels = document.querySelectorAll(".year-label");

  bars.forEach((bar, index) => {
    setTimeout(() => {
      bar.classList.add("animate");
    }, index * 150);
  });

  yearLabels.forEach((label, index) => {
    setTimeout(() => {
      label.classList.add("show");
    }, index * 400 + 800);
  });

  setTimeout(() => {
    new ScrollHint(".js-scrollable", {
      scrollHintIconAppendClass: "scroll-hint-icon-white",
      applyToParents: true,
      i18n: {
        scrollable: "横スクロール",
      },
    });
  }, 100);
}

function resetAnimation() {
  const counter = document.getElementById("counter");
  counter.innerHTML = '900<span class="counter-unit">件</span>';

  const bars = document.querySelectorAll(".bar");
  const yearLabels = document.querySelectorAll(".year-label");
  bars.forEach((bar) => {
    bar.classList.remove("animate");
  });
  yearLabels.forEach((label) => {
    label.classList.remove("show");
  });
}

document.addEventListener("DOMContentLoaded", () => {
  let chartAnimationStarted = false;
  let counterAnimationStarted = false;

  function isElementVisible(el, threshold = 100) {
    if (!el) return false;
    const rect = el.getBoundingClientRect();
    return rect.top < window.innerHeight - threshold && rect.bottom > threshold;
  }

  function checkAnimations() {
    const chart = document.getElementById("chart");
    const counter = document.getElementById("counter");

    if (!counterAnimationStarted && isElementVisible(counter, 50)) {
      counterAnimationStarted = true;
      animateCounter();
    }

    if (!chartAnimationStarted && isElementVisible(chart, 150)) {
      chartAnimationStarted = true;
      startChartAnimation();
    }
  }

  function startChartAnimation() {
    createChart();

    const bars = document.querySelectorAll(".bar");
    const yearLabels = document.querySelectorAll(".year-label");

    bars.forEach((bar, index) => {
      setTimeout(() => {
        bar.classList.add("animate");
      }, index * 150);
    });

    yearLabels.forEach((label, index) => {
      setTimeout(() => {
        label.classList.add("show");
      }, index * 600 + 800);
    });

    setTimeout(() => {
      if (typeof ScrollHint !== "undefined") {
        new ScrollHint(".js-scrollable", {
          scrollHintIconAppendClass: "scroll-hint-icon-white",
          applyToParents: true,
          i18n: {
            scrollable: "横スクロール",
          },
        });
      }
    }, 100);
  }

  setTimeout(checkAnimations, 100);

  let ticking = false;
  window.addEventListener("scroll", () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        checkAnimations();
        ticking = false;
      });
      ticking = true;
    }
  });

  window.addEventListener("resize", checkAnimations);
});
