/* --------------------------------------------------------------------------
   Data charts: colours that cannot be stylesheet rules

   Theme addition, not part of the supplied vis library. The plotted points and
   error bars are painted on a <canvas>, so no CSS rule reaches them — the
   library takes them as constructor arguments. This reads them from custom
   properties on the chart element, which keeps them in theme.css with the rest
   of the chart's colours instead of as literals in the library files.

   Declared on .r-data-chart in theme.css "Row: data-chart". The fallbacks are
   the legacy site's own values, so the charts still draw if a property is
   absent or the stylesheet fails to load.

   Loaded first in the chart script chain (inc/charts.php), so it is defined
   before any chart is constructed. A plain function declaration, deliberately:
   the library files are classic scripts sharing the global scope.
   -------------------------------------------------------------------------- */
function visColor(el, property, fallback) {
	if (!el || typeof window.getComputedStyle !== "function") {
		return fallback;
	}

	var value = window.getComputedStyle(el).getPropertyValue(property);

	return value && value.trim() ? value.trim() : fallback;
}
