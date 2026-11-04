class PieChart {
  constructor(element, data, options = {}) {
    this.element =
      typeof element === "string" ? document.querySelector(element) : element;
    this.data = data;
    this.options = {
      segmentShowStroke: true,
      segmentStrokeColor: "#FFFFFF",
      segmentStrokeWidth: 0,
      baseColor: "rgba(0,0,0,0.1)",
      baseOffset: 0,
      edgeOffset: 0,
      percentageInnerCutout: 68,
      animation: true,
      animationSteps: 60,
      animationEasing: "easeInOutExpo",
      animateRotate: true,
      tipOffsetX: -8,
      tipOffsetY: -45,
      tipClass: "pieTip",
      titleClass: "pieTitle",
      title: "Chart",
      titleTextClass: "pieTitleText",
      legendId: null,
      beforeDraw: () => {},
      afterDrawed: () => {},
      onPathEnter: () => {},
      onPathLeave: () => {},
      ...options,
    };

    this.resizeHandler = null;
    this.init();
    this.setupResize();
  }

  init() {
    this.W = this.element.offsetWidth;
    this.H = this.element.offsetHeight;
    this.centerX = this.W / 2;
    this.centerY = this.H / 2;
    this.PI = Math.PI;

    this.animationOptions = {
      linear: (t) => t,
      easeInOutExpo: (t) => {
        const v = t < 0.5 ? 8 * t * t * t * t : 1 - 8 * --t * t * t * t;
        return v > 1 ? 1 : v;
      },
    };

    this.pieRadius = Math.min(this.H / 2, this.W / 2) - this.options.edgeOffset;
    this.cutoutRadius =
      this.pieRadius * (this.options.percentageInnerCutout / 100);
    this.segmentTotal = this.data.reduce((sum, item) => sum + item.value, 0);

    this.setupElements();
    this.createLegend();
    this.draw();
  }

  setupResize() {
    this.resizeHandler = () => {
      clearTimeout(this.resizeTimeout);
      this.resizeTimeout = setTimeout(() => {
        this.redraw();
      }, 100);
    };

    window.addEventListener("resize", this.resizeHandler);
  }

  redraw() {
    const newW = this.element.offsetWidth;
    const newH = this.element.offsetHeight;

    if (newW !== this.W || newH !== this.H) {
      this.W = newW;
      this.H = newH;
      this.centerX = this.W / 2;
      this.centerY = this.H / 2;

      this.pieRadius =
        Math.min(this.H / 2, this.W / 2) - this.options.edgeOffset;
      this.cutoutRadius =
        this.pieRadius * (this.options.percentageInnerCutout / 100);

      this.svg.setAttribute("width", this.W);
      this.svg.setAttribute("height", this.H);
      this.svg.setAttribute("viewBox", `0 0 ${this.W} ${this.H}`);

      this.svg.innerHTML = "";
      this.setupElements();
      this.createLegend();
      this.draw();
    }
  }

  setupElements() {
    if (!this.svg) {
      this.element.innerHTML = "";
      this.svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      this.element.appendChild(this.svg);

      if (this.tooltip) {
        this.tooltip.remove();
      }
      this.tooltip = document.createElement("div");
      this.tooltip.className = this.options.tipClass;
      document.body.appendChild(this.tooltip);

      if (this.titleElement) {
        this.titleElement.remove();
      }
      this.createTitle();
    }

    this.svg.setAttribute("width", this.W);
    this.svg.setAttribute("height", this.H);
    this.svg.setAttribute("viewBox", `0 0 ${this.W} ${this.H}`);

    this.createBasePie();

    this.pathGroup = document.createElementNS(
      "http://www.w3.org/2000/svg",
      "g"
    );
    this.pathGroup.setAttribute("opacity", "0");
    this.svg.appendChild(this.pathGroup);

    this.paths = [];
    this.data.forEach((item, i) => {
      const path = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "path"
      );
      path.setAttribute("stroke-width", this.options.segmentStrokeWidth);
      path.setAttribute("stroke", this.options.segmentStrokeColor);
      path.setAttribute("fill", item.color);
      path.setAttribute("data-order", i);
      path.style.cursor = "pointer";

      path.addEventListener("mouseenter", (e) => this.onPathEnter(e, i));
      path.addEventListener("mouseleave", (e) => this.onPathLeave(e, i));
      path.addEventListener("mousemove", (e) => this.onPathMove(e, i));

      this.pathGroup.appendChild(path);
      this.paths.push(path);
    });
  }

  createBasePie() {
    const basePieRadius = this.pieRadius + this.options.baseOffset;
    const baseCutoutRadius = this.cutoutRadius - this.options.baseOffset;

    const basePath = document.createElementNS(
      "http://www.w3.org/2000/svg",
      "path"
    );
    basePath.setAttribute(
      "d",
      this.getHollowCirclePath(basePieRadius, baseCutoutRadius)
    );
    basePath.setAttribute("fill", this.options.baseColor);
    this.svg.appendChild(basePath);
  }

  createTitle() {
    this.titleElement = document.createElement("div");
    this.titleElement.className = this.options.titleClass;

    this.titleText = document.createElement("p");
    this.titleText.className = this.options.titleTextClass;
    this.titleText.textContent = this.options.title;
    this.titleText.style.opacity = "0";
    this.titleElement.appendChild(this.titleText);

    this.element.appendChild(this.titleElement);
  }

  getHollowCirclePath(pieRadius, cutoutRadius) {
    const startRadius = -1.57;
    const segmentAngle = 6.2831;
    const endRadius = 4.7131;

    const startX = this.centerX + Math.cos(startRadius) * pieRadius;
    const startY = this.centerY + Math.sin(startRadius) * pieRadius;
    const endX2 = this.centerX + Math.cos(startRadius) * cutoutRadius;
    const endY2 = this.centerY + Math.sin(startRadius) * cutoutRadius;
    const endX = this.centerX + Math.cos(endRadius) * pieRadius;
    const endY = this.centerY + Math.sin(endRadius) * pieRadius;
    const startX2 = this.centerX + Math.cos(endRadius) * cutoutRadius;
    const startY2 = this.centerY + Math.sin(endRadius) * cutoutRadius;

    return [
      "M",
      startX,
      startY,
      "A",
      pieRadius,
      pieRadius,
      0,
      1,
      1,
      endX,
      endY,
      "Z",
      "M",
      startX2,
      startY2,
      "A",
      cutoutRadius,
      cutoutRadius,
      0,
      1,
      0,
      endX2,
      endY2,
      "Z",
    ].join(" ");
  }

  onPathEnter(e, index) {
    const item = this.data[index];
    this.tooltip.textContent = `${item.title}: ${item.value}`;
    this.tooltip.classList.add("show");
    this.options.onPathEnter(e, this.data);
  }

  onPathLeave(e, index) {
    this.tooltip.classList.remove("show");
    this.options.onPathLeave(e, this.data);
  }

  onPathMove(e, index) {
    const rect = this.tooltip.getBoundingClientRect();
    this.tooltip.style.top = e.pageY + this.options.tipOffsetY + "px";
    this.tooltip.style.left =
      e.pageX - rect.width / 2 + this.options.tipOffsetX + "px";
  }

  draw() {
    this.options.beforeDraw();
    this.animate();
  }

  animate() {
    const easingFunction = this.animationOptions[this.options.animationEasing];
    const animFrameAmount = this.options.animation
      ? 1 / Math.max(this.options.animationSteps, 1)
      : 1;
    let cnt = this.options.animation ? 0 : 1;

    const animateFrame = () => {
      cnt += animFrameAmount;
      const easeAdjustedAnimationPercent = this.options.animation
        ? Math.min(Math.max(easingFunction(cnt), 0), 1)
        : 1;

      this.drawPieSegments(easeAdjustedAnimationPercent);

      if (cnt <= 1) {
        requestAnimationFrame(animateFrame);
      } else {
        this.options.afterDrawed();
      }
    };

    requestAnimationFrame(animateFrame);
  }

  drawPieSegments(animationDecimal) {
    let startRadius = -this.PI / 2;
    const rotateAnimation =
      this.options.animation && this.options.animateRotate
        ? animationDecimal
        : 1;

    this.showTitle(animationDecimal);
    this.pathGroup.setAttribute("opacity", animationDecimal);

    if (
      this.data.length === 1 &&
      4.7122 <
        rotateAnimation *
          ((this.data[0].value / this.segmentTotal) * (this.PI * 2)) +
          startRadius
    ) {
      this.paths[0].setAttribute(
        "d",
        this.getHollowCirclePath(this.pieRadius, this.cutoutRadius)
      );
      return;
    }

    this.data.forEach((item, i) => {
      const segmentAngle =
        rotateAnimation * ((item.value / this.segmentTotal) * (this.PI * 2));
      const endRadius = startRadius + segmentAngle;
      const largeArc =
        (endRadius - startRadius) % (this.PI * 2) > this.PI ? 1 : 0;

      const startX = this.centerX + Math.cos(startRadius) * this.pieRadius;
      const startY = this.centerY + Math.sin(startRadius) * this.pieRadius;
      const endX2 = this.centerX + Math.cos(startRadius) * this.cutoutRadius;
      const endY2 = this.centerY + Math.sin(startRadius) * this.cutoutRadius;
      const endX = this.centerX + Math.cos(endRadius) * this.pieRadius;
      const endY = this.centerY + Math.sin(endRadius) * this.pieRadius;
      const startX2 = this.centerX + Math.cos(endRadius) * this.cutoutRadius;
      const startY2 = this.centerY + Math.sin(endRadius) * this.cutoutRadius;

      const cmd = [
        "M",
        startX,
        startY,
        "A",
        this.pieRadius,
        this.pieRadius,
        0,
        largeArc,
        1,
        endX,
        endY,
        "L",
        startX2,
        startY2,
        "A",
        this.cutoutRadius,
        this.cutoutRadius,
        0,
        largeArc,
        0,
        endX2,
        endY2,
        "Z",
      ].join(" ");

      this.paths[i].setAttribute("d", cmd);
      startRadius += segmentAngle;
    });
  }

  showTitle(animationDecimal) {
    this.titleText.style.opacity = animationDecimal;
  }

  createLegend() {
    if (!this.options.legendId) return;

    const legend = document.getElementById(this.options.legendId);
    if (!legend) return;

    legend.innerHTML = "";

    this.data.forEach((item) => {
      const legendItem = document.createElement("div");
      legendItem.className = "legend-item";

      const color = document.createElement("div");
      color.className = "legend-color";
      color.style.backgroundColor = item.color;

      const label = document.createElement("div");
      const percentage = ((item.value / this.segmentTotal) * 100).toFixed(1);
      label.className = "legend-text";
      label.innerHTML = `<span class="legend-title">${item.title}</span><br><span class="legend-value">${percentage}</span><span class="legend-unit">%</span>`;

      legendItem.appendChild(color);
      legendItem.appendChild(label);
      legend.appendChild(legendItem);
    });
  }

  destroy() {
    if (this.resizeHandler) {
      window.removeEventListener("resize", this.resizeHandler);
    }
    if (this.resizeTimeout) {
      clearTimeout(this.resizeTimeout);
    }
    if (this.tooltip && this.tooltip.parentNode) {
      this.tooltip.parentNode.removeChild(this.tooltip);
    }
    this.element.innerHTML = "";
  }
}

const needsData = [
  { title: "遺骨所持", value: 60, color: "#DB6A50" },
  { title: "改葬", value: 30, color: "#C19A5F" },
  { title: "生前", value: 10, color: "#DED8BA" },
];

const factorsData = [
  { title: "●●", value: 60, color: "#DB6A50" },
  { title: "●●", value: 30, color: "#C19A5F" },
  { title: "●●", value: 10, color: "#DED8BA" },
];

const satisfactionData = [
  { title: "●●", value: 60, color: "#DB6A50" },
  { title: "●●", value: 30, color: "#C19A5F" },
  { title: "●●", value: 10, color: "#DED8BA" },
];

const staffData = [
  { title: "●●", value: 60, color: "#DB6A50" },
  { title: "●●", value: 30, color: "#C19A5F" },
  { title: "●●", value: 10, color: "#DED8BA" },
];

class ScrollAnimationManager {
  constructor() {
    this.charts = new Map();
    this.chartInstances = new Map();
    this.observer = null;
    this.init();
  }

  init() {
    this.observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const chartId = entry.target.id;
            this.animateChart(chartId);
            this.observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.3,
        rootMargin: "-50px",
      }
    );
  }

  registerChart(chartId, data, options) {
    this.charts.set(chartId, { data, options, animated: false });
    const element = document.getElementById(chartId);
    if (element) {
      this.observer.observe(element);
    }
  }

  animateChart(chartId) {
    const chartInfo = this.charts.get(chartId);
    if (chartInfo && !chartInfo.animated) {
      chartInfo.animated = true;

      const element = document.getElementById(chartId);
      element.classList.add("animate-in");

      setTimeout(() => {
        const chartInstance = new PieChart(
          `#${chartId}`,
          chartInfo.data,
          chartInfo.options
        );
        this.chartInstances.set(chartId, chartInstance);
      }, 300);
    }
  }

  destroy() {
    this.chartInstances.forEach((chart) => {
      chart.destroy();
    });
    this.chartInstances.clear();

    if (this.observer) {
      this.observer.disconnect();
    }
  }
}

let animationManager;

document.addEventListener("DOMContentLoaded", () => {
  animationManager = new ScrollAnimationManager();

  animationManager.registerChart("needsChart", needsData, {
    title: "ニーズ別",
    legendId: "needs",
  });

  animationManager.registerChart("factorsChart", factorsData, {
    title: "ご契約の決め手",
    legendId: "factors",
  });

  animationManager.registerChart("satisfactionChart", satisfactionData, {
    title: "ご利用中の満足度",
    legendId: "satisfaction",
  });

  animationManager.registerChart("staffChart", satisfactionData, {
    title: "スタッフの対応",
    legendId: "staff",
  });
});

window.addEventListener("beforeunload", () => {
  if (animationManager) {
    animationManager.destroy();
  }
});
