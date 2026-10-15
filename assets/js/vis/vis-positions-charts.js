class VisPositionsCharts {
  constructor({
    el,
    title,
    positionsDataUrl,
    commentsDataUrl,
    footerContent,
    showErrorBars,
  }) {
    this.el = el;
    this.title = title;
    this.positionsDataUrl = positionsDataUrl;
    this.commentsDataUrl = commentsDataUrl;
    this.footerContent = footerContent;
    this.showErrorBars = showErrorBars;
    this.loadData();
  }

  loadData() {
    const fetches = [d3.csv(this.positionsDataUrl)];
    if (this.commentsDataUrl) {
      fetches.push(d3.csv(this.commentsDataUrl));
    }
    Promise.all(fetches)
      .then(([positionsCsv, commentsCsv]) => {
        this.northData = [];
        this.eastData = [];
        this.heightData = [];
        const parseTime = (time) => d3.utcParse("%Y-%m-%d")(time.slice(0, 10));
        this.positionsData = positionsCsv.forEach((d) => {
          const date = parseTime(d[positionsCsv.columns[0]]);
          const n = [date, [+d[positionsCsv.columns[1]]]];
          const e = [date, [+d[positionsCsv.columns[2]]]];
          const h = [date, [+d[positionsCsv.columns[3]]]];
          if (this.showErrorBars) {
            n.push([+d[positionsCsv.columns[4]]]);
            e.push([+d[positionsCsv.columns[5]]]);
            h.push([+d[positionsCsv.columns[6]]]);
          }
          this.northData.push(n);
          this.eastData.push(e);
          this.heightData.push(h);
        });
        this.xDomain = [
          this.northData[0][0],
          this.northData[this.northData.length - 1][0],
        ];
        this.annotationsData = (commentsCsv || []).map((d) => [
          parseTime(d[commentsCsv.columns[0]]),
          d[commentsCsv.columns[1]],
        ]);
        this.initVis();
      })
      .catch((error) => {
        d3.select(this.el)
          .classed("vis", true)
          .append("div")
          .attr("class", "alert-error")
          .text("Something went wrong. Please try again later.");
        console.error(error);
      });
  }

  initVis() {
    const visWrap = d3.select(this.el).classed("vis", true);
    if (this.title) {
      visWrap.append("div").attr("class", "vis-title").text(this.title);
    }
    const visBodyWrap = visWrap.append("div").attr("class", "vis-body");
    const visFooterWrap = visWrap.append("div").attr("class", "vis-footer");

    new VisBaseChart({
      el: visBodyWrap.append("div").attr("class", "chart-wrap north").node(),
      data: this.northData,
      series: ["North"],
      colors: [visColor(this.el, "--chart-dot", "rgba(3, 62, 140, 0.35)")],
      xDomain: this.xDomain,
      yLogScale: false,
      xTicksVisible: false,
      yTitle: "North",
      yUnit: "mm",
      showErrorBars: this.showErrorBars,
      errorBarColor: visColor(this.el, "--chart-error-bar", "rgba(189, 189, 189, 0.35)"),
    });

    new VisBaseChart({
      el: visBodyWrap.append("div").attr("class", "chart-wrap east").node(),
      data: this.eastData,
      series: ["East"],
      colors: [visColor(this.el, "--chart-dot", "rgba(3, 62, 140, 0.35)")],
      xDomain: this.xDomain,
      yLogScale: false,
      xTicksVisible: false,
      yTitle: "East",
      yUnit: "mm",
      showErrorBars: this.showErrorBars,
      errorBarColor: visColor(this.el, "--chart-error-bar", "rgba(189, 189, 189, 0.35)"),
    });

    new VisBaseChart({
      el: visBodyWrap.append("div").attr("class", "chart-wrap height").node(),
      data: this.heightData,
      series: ["Height"],
      colors: [visColor(this.el, "--chart-dot", "rgba(3, 62, 140, 0.35)")],
      xDomain: this.xDomain,
      yLogScale: false,
      xTicksVisible: true,
      yTitle: "Height",
      yUnit: "mm",
      showErrorBars: this.showErrorBars,
      errorBarColor: visColor(this.el, "--chart-error-bar", "rgba(189, 189, 189, 0.35)"),
    });

    if (this.annotationsData.length > 0) {
      new VisAnnotations({
        el: visBodyWrap.append("div").attr("class", "annotations-wrap").node(),
        elDetails: visFooterWrap
          .append("div")
          .attr("class", "annotations-details")
          .node(),
        data: this.annotationsData,
        xDomain: this.xDomain,
      });
    }

    if (this.footerContent) {
      visFooterWrap
        .append("div")
        .attr("class", "custom-footer")
        .html(this.footerContent);
    }
  }
}
