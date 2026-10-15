class VisQCCharts {
  constructor({
    el,
    title,
    slipsDataUrl,
    multipathDataUrl,
    snrDataUrl,
    commentsDataUrl,
    footerContent,
  }) {
    this.el = el;
    this.title = title;
    this.slipsDataUrl = slipsDataUrl;
    this.multipathDataUrl = multipathDataUrl;
    this.snrDataUrl = snrDataUrl;
    this.commentsDataUrl = commentsDataUrl;
    this.footerContent = footerContent;
    this.loadData();
  }

  loadData() {
    const fetches = [
      d3.csv(this.slipsDataUrl),
      d3.csv(this.multipathDataUrl),
      d3.csv(this.snrDataUrl),
    ];
    if (this.commentsDataUrl) {
      fetches.push(d3.csv(this.commentsDataUrl));
    }
    Promise.all(fetches)
      .then(([slipsCsv, multipathCsv, snrCsv, commentsCsv]) => {
        const parseTime = (time) => d3.utcParse("%Y-%m-%d")(time.slice(0, 10));
        this.slipsData = slipsCsv.map((d) => [
          parseTime(d[slipsCsv.columns[0]]),
          [+d[slipsCsv.columns[1]]],
        ]);
        this.multipathData = multipathCsv.map((d) => [
          parseTime(d[multipathCsv.columns[0]]),
          [+d[multipathCsv.columns[1]], +d[multipathCsv.columns[2]]],
        ]);
        this.snrData = snrCsv.map((d) => [
          parseTime(d[snrCsv.columns[0]]),
          [+d[snrCsv.columns[1]], +d[snrCsv.columns[2]]],
        ]);
        this.xDomain = [
          d3.min([
            this.slipsData[0][0],
            this.multipathData[0][0],
            this.snrData[0][0],
          ]),
          d3.max([
            this.slipsData[this.slipsData.length - 1][0],
            this.multipathData[this.multipathData.length - 1][0],
            this.snrData[this.snrData.length - 1][0],
          ]),
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
      el: visBodyWrap.append("div").attr("class", "chart-wrap slips").node(),
      data: this.slipsData,
      series: ["Slips"],
      colors: [visColor(this.el, "--chart-dot", "rgba(3, 62, 140, 0.35)")],
      xDomain: this.xDomain,
      xTicksVisible: false,
      yLogScale: true,
      yTitle: "Slips",
      yUnit: "",
      yTickFormat: d3.format(",d"),
    });

    new VisBaseChart({
      el: visBodyWrap
        .append("div")
        .attr("class", "chart-wrap multipath")
        .node(),
      data: this.multipathData,
      series: ["L1", "L2"],
      colors: [visColor(this.el, "--chart-dot-alt", "rgba(4, 137, 190, 0.35)"), visColor(this.el, "--chart-dot", "rgba(3, 62, 140, 0.35)")],
      xDomain: this.xDomain,
      xTicksVisible: false,
      yLogScale: false,
      yTitle: "Multipath",
      yUnit: "m",
    });

    new VisBaseChart({
      el: visBodyWrap.append("div").attr("class", "chart-wrap snr").node(),
      data: this.snrData,
      series: ["L1", "L2"],
      colors: [visColor(this.el, "--chart-dot-alt", "rgba(4, 137, 190, 0.35)"), visColor(this.el, "--chart-dot", "rgba(3, 62, 140, 0.35)")],
      xDomain: this.xDomain,
      yLogScale: false,
      xTicksVisible: true,
      yTitle: "Signal-to-Noise",
      yUnit: "dB-Hz",
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
