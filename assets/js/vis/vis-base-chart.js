class VisBaseChart {
  constructor({
    el,
    data,
    series,
    colors,
    xDomain,
    yLogScale,
    xTicksVisible,
    yTitle,
    yUnit,
    yTickFormat,
    showErrorBars,
    errorBarColor,
  }) {
    this.el = el;
    this.data = data;
    this.series = series;
    this.colors = colors;
    this.xDomain = xDomain;
    this.yLogScale = yLogScale;
    this.xTicksVisible = xTicksVisible;
    this.yTitle = yTitle;
    this.yUnit = yUnit;
    this.yTickFormat = yTickFormat;
    this.showErrorBars = showErrorBars;
    this.errorBarColor = errorBarColor;
    this.resizeVis = this.resizeVis.bind(this);
    this.initVis();
  }

  initVis() {
    // When change margin lef or margin right the annotations dimension should be changed according so everything line up.
    this.margin = {
      top: 24,
      right: 48,
      bottom: 24,
      left: 2,
    };
    this.r = 1;

    this.x = d3.scaleTime().domain(this.xDomain);

    let minValue, maxValue;
    if (this.showErrorBars) {
      const sortedValues = this.data
        .reduce(
          (arr, d) => [
            ...arr,
            ...d[1].map((e, i) => e - d[2][i]),
            ...d[1].map((e, i) => e + d[2][i]),
          ],
          []
        )
        .sort(d3.ascending);
      minValue = d3.quantileSorted(sortedValues, 0.001);
      maxValue = d3.quantileSorted(sortedValues, 0.999);
    } else {
      const sortedValues = this.data
        .reduce((arr, d) => (arr = [...arr, ...d[1]]), [])
        .sort(d3.ascending);
      minValue = d3.quantileSorted(sortedValues, 0.002);
      maxValue = d3.quantileSorted(sortedValues, 0.998);
    }

    this.y = this.yLogScale ? d3.scaleLog() : d3.scaleLinear();
    this.y
      .domain([this.yLogScale && minValue === 0 ? 1 : minValue, maxValue])
      .nice();

    const texts = [{ text: this.yTitle, className: "title-text" }];
    if (this.yUnit)
      texts.push({
        text: ` (${this.yUnit})`,
        className: "unit-text",
      });
    if (this.series.length > 1) {
      this.series.forEach((d, i) => {
        texts.push({
          text: ` ${d}`,
          className: `legend-text series--${d.toLowerCase()}`,
          color: (() => {
            const c = d3.color(this.colors[i]);
            c.opacity = 1;
            return c.toString();
          })(),
        });
      });
    }

    this.chartWrap = d3.select(this.el);

    this.canvas = this.chartWrap.append("canvas");
    this.context = this.canvas.node().getContext("2d");
    this.context.save();

    this.svg = this.chartWrap
      .append("svg")
      .call((svg) => svg.append("g").attr("class", "axis-g axis-g--x"))
      .call((svg) => svg.append("g").attr("class", "axis-g axis-g--y"))
      .call((svg) =>
        svg
          .append("g")
          .attr("class", "title-g")
          .append("text")
          .attr("fill", "currentColor")
          .attr("y", this.margin.top - 8)
          .selectAll("tspan")
          .data(texts)
          .join("tspan")
          .attr("class", (d) => d.className)
          .style("color", (d) => (d.color ? d.color : null))
          .text((d) => d.text)
      );

    this.resizeVis();
    window.addEventListener("resize", this.resizeVis);
  }

  resizeVis() {
    this.width = this.el.clientWidth;
    this.height = this.el.clientHeight;
    this.x.range([this.margin.left, this.width - this.margin.right]);
    this.y.range([this.height - this.margin.bottom, this.margin.top]);
    this.svg.attr("width", this.width).attr("height", this.height);

    this.dpr = window.devicePixelRatio || 1;
    this.canvas
      .attr("width", this.width * this.dpr)
      .attr("height", this.height * this.dpr);
    this.context.restore();
    this.context.scale(this.dpr, this.dpr);

    this.updateVis();
  }

  updateVis() {
    this.context.clearRect(0, 0, this.width, this.height);
    this.series.forEach((s, i) => {
      if (this.showErrorBars) {
        this.context.strokeStyle = this.errorBarColor;
        this.data.forEach((d) => {
          const cx = this.x(d[0]);
          const errorBarUpper = this.y(d[1][i] + d[2][i]);
          const errorBarLower = this.y(d[1][i] - d[2][i]);
          this.context.beginPath();
          this.context.moveTo(cx, errorBarUpper);
          this.context.lineTo(cx, errorBarLower);
          this.context.stroke();
        });
      }
      this.context.fillStyle = this.colors[i];
      this.data.forEach((d) => {
        const cx = this.x(d[0]);
        const cy = this.y(d[1][i]);
        this.context.beginPath();
        this.context.moveTo(cx + this.r, cy + this.r);
        this.context.arc(cx, cy, this.r, 0, Math.PI * 2);
        this.context.fill();
      });
    });

    const gx = this.svg
      .select(".axis-g--x")
      .attr("transform", `translate(0,${this.height - this.margin.bottom})`)
      .call(
        d3
          .axisBottom(this.x)
          .tickSizeOuter(0)
          .ticks((this.width - this.margin.left - this.margin.right) / 80)
      );
    if (this.xTicksVisible) {
      gx.selectAll(".tick text").each((d, i, n) => {
        if (i === 0) {
          d3.select(n[i]).attr(
            "text-anchor",
            this.x(d) - n[i].getBBox().width / 2 < 0 ? "start" : null
          );
        }
      });
    } else {
      gx.selectAll(".tick text").remove();
    }

    this.svg
      .select(".axis-g--y")
      .attr("transform", `translate(${this.width - this.margin.right},0)`)
      .call(
        d3
          .axisRight(this.y)
          .ticks((this.height - this.margin.top - this.margin.bottom) / 40)
          .tickFormat(this.yTickFormat)
      )
      .call((g) => g.select(".domain").remove());
  }
}
