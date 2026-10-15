class VisAnnotations {
  constructor({ el, elDetails, data, xDomain }) {
    this.el = el;
    this.elDetails = elDetails;
    this.data = data;
    this.xDomain = xDomain;
    this.resizeVis = this.resizeVis.bind(this);
    this.initVis();
  }

  initVis() {
    this.margin = {
      top: 12, // The top badge's center is at margin top
      right: 48, // The same as the base chart,
      bottom: 12, // The bottom badge's center is at height - margin bottom
      left: 2, // The same as the base chart
    };
    this.r = 8;

    this.x = d3.scaleTime().domain(this.xDomain);

    this.annotationsWrap = d3.select(this.el);
    this.svg = this.annotationsWrap.append("svg");

    this.tooltip = new VisTooltip({ el: this.el });

    this.annotationsDetails = d3.select(this.elDetails);

    this.resizeVis();
    window.addEventListener("resize", this.resizeVis);

    let dActive;
    const xs = this.data.map((d) => d[0]);
    const formatTime = d3.utcFormat("%B %-d, %Y");
    const moved = (event) => {
      event.preventDefault();
      const pointer = d3.pointer(event, this.el);
      const xm = this.x.invert(pointer[0]);
      const i = d3.bisectCenter(xs, xm);
      const d = this.data[i];
      if (d !== dActive) {
        dActive = d;
        this.svg
          .selectAll(".annotation-g")
          .classed("is-active", (e) => e === dActive);
        this.tooltip.show(`
          <div class="tooltip-date">${formatTime(dActive[0])}</div>
          <div class="tooltip-text">${dActive[1]}</div>
        `);
      }
      if (dActive) {
        this.tooltip.move(this.x(dActive[0]), pointer[1]);
      }
    };

    const left = () => {
      dActive = null;
      this.svg.selectAll(".annotation-g").classed("is-active", false);
      this.tooltip.hide();
    };

    if ("ontouchstart" in document)
      this.svg
        .style("-webkit-tap-highlight-color", "transparent")
        .on("touchmove", moved)
        .on("touchend", left);
    else this.svg.on("mousemove", moved).on("mouseleave", left);
  }

  resizeVis() {
    this.width = this.el.clientWidth;
    this.height = this.el.clientHeight;
    this.x.range([this.margin.left, this.width - this.margin.right]);
    this.svg.attr("width", this.width).attr("height", this.height);

    this.updateVis();
  }

  updateVis() {
    this.svg
      .selectAll(".annotation-g")
      .data(this.data)
      .join((enter) =>
        enter
          .append("g")
          .attr("class", "annotation-g")
          .call((g) =>
            g
              .append("line")
              .attr("class", "annotation-line")
              .attr("y1", this.margin.top)
              .attr("y2", this.height - this.margin.bottom)
              .attr("stroke", "currentColor")
          )
          .call((g) =>
            g
              .selectAll(".annotation-badge")
              .data([this.margin.top, this.height - this.margin.bottom])
              .join("g")
              .attr("class", "annotation-badge")
              .attr("fill", "currentColor")
              .attr("transform", (d) => `translate(0,${d})`)
              .call((g) =>
                g
                  .append("circle")
                  .attr("class", "badge-circle")
                  .attr("r", this.r)
              )
              .call((g) =>
                g
                  .append("text")
                  .attr("class", "badge-text")
                  .attr("text-anchor", "middle")
                  .attr("dy", "0.32em")
              )
          )
      )
      .attr("transform", (d) => `translate(${this.x(d[0])},0)`)
      .each(function (d, i) {
        d3.select(this)
          .selectAll(".annotation-badge")
          .select(".badge-text")
          .text(i + 1);
      });

    this.annotationsDetails
      .selectAll(".annotation-item")
      .data(this.data)
      .join((enter) =>
        enter
          .append("div")
          .attr("class", "annotation-item")
          .call((div) =>
            div
              .append("div")
              .attr("class", "annotation-swatch annotation-badge")
              .append("svg")
              .attr("width", this.r * 2)
              .attr("height", this.r * 2)
              .attr("viewBox", [-this.r, -this.r, this.r * 2, this.r * 2])
              .call((svg) =>
                svg
                  .append("circle")
                  .attr("class", "badge-circle")
                  .attr("r", this.r)
                  .attr("fill", "currentColor")
              )
              .call((svg) =>
                svg
                  .append("text")
                  .attr("class", "badge-text")
                  .attr("text-anchor", "middle")
                  .attr("dy", "0.32em")
                  .attr("fill", "currentColor")
              )
          )
          .call((div) => div.append("div").attr("class", "annotation-text"))
      )
      .call((div) => div.select(".badge-text").text((d, i) => i + 1))
      .call((div) => div.select(".annotation-text").text((d) => d[1]));
  }
}
