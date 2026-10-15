class VisTooltip {
  constructor({ el }) {
    this.el = el;
    this.tooltip = d3
      .select(this.el)
      .append("div")
      .attr("class", "vis-tooltip");
    this.padding = 8;
    this.show = this.show.bind(this);
    this.hide = this.hide.bind(this);
    this.move = this.move.bind(this);
  }

  show(content) {
    this.tooltip.html(content).classed("is-visible", true);
    this.elBox = this.el.getBoundingClientRect();
    this.tooltipBox = this.tooltip.node().getBoundingClientRect();
  }

  hide() {
    this.tooltip.classed("is-visible", false);
  }

  move(x, y) {
    let tX = x + this.padding;
    if (tX + this.tooltipBox.width > this.elBox.width) {
      tX = x - this.padding - this.tooltipBox.width;
    }
    if (tX < 0) {
      tX = 0;
    }
    let tY = y;
    if (tY + this.tooltipBox.height > this.elBox.height) {
      tY = this.elBox.height - this.tooltipBox.height;
    }
    this.tooltip.style("transform", `translate(${tX}px,${tY}px)`);
  }
}
