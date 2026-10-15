/* --------------------------------------------------------------------------
   Data charts
   Starts one vis-library chart per .r-data-chart__canvas, from the row's
   data-verquin-chart config. Loaded only on pages carrying a data-chart row.

   The library's classes are plain top-level `class` declarations in classic
   scripts, so they are global lexical bindings, not properties of window:
   they must be read as bare identifiers, guarded with typeof.
   -------------------------------------------------------------------------- */
(function () {
	"use strict";

	var nodes = document.querySelectorAll("[data-verquin-chart]");

	if (!nodes.length || typeof d3 === "undefined") {
		return;
	}

	Array.prototype.forEach.call(nodes, function (node) {
		var config;

		try {
			config = JSON.parse(node.getAttribute("data-verquin-chart"));
		} catch (error) {
			return;
		}

		var Chart =
			config.kind === "qc"
				? typeof VisQCCharts === "function"
					? VisQCCharts
					: null
				: typeof VisPositionsCharts === "function"
					? VisPositionsCharts
					: null;

		if (!Chart) {
			return;
		}

		var settings = {
			el: node,
			title: config.title,
			footerContent: config.footer || "",
			showErrorBars: Boolean(config.errorBars),
		};

		Object.keys(config.urls).forEach(function (key) {
			settings[key] = config.urls[key];
		});

		new Chart(settings);
	});
})();
