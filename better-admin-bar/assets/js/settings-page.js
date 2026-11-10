/**
 * Settings page: tabs & fields that depend on a checkbox.
 */
(function () {
	var tabs = document.querySelectorAll(".swift-control-settings .heatbox-tab-nav-item");
	var panels = document.querySelectorAll(".swift-control-settings .heatbox-admin-panel");

	/**
	 * Show the panel of a tab.
	 *
	 * @param {string} name The panel name.
	 */
	function showPanel(name) {
		var found = false;

		panels.forEach(function (panel) {
			if (panel.dataset.panel === name) found = true;
		});

		if (!found) name = "settings";

		panels.forEach(function (panel) {
			panel.style.display = panel.dataset.panel === name ? "block" : "none";
		});

		tabs.forEach(function (tab) {
			tab.classList.toggle("active", tab.dataset.panel === name);
		});
	}

	/**
	 * Show or hide an element depending on a checkbox.
	 *
	 * @param {HTMLElement} child The element with a data-show-if-field-(un)checked attribute.
	 */
	function setupDependency(child) {
		var showIfChecked = child.hasAttribute("data-show-if-field-checked");
		var parentId = showIfChecked
			? child.dataset.showIfFieldChecked
			: child.dataset.showIfFieldUnchecked;
		var parent = document.getElementById(parentId);

		if (!parent) return;

		function update() {
			child.hidden = parent.checked !== showIfChecked;
		}

		parent.addEventListener("change", update);
		update();
	}

	window.addEventListener("hashchange", function () {
		showPanel(location.hash.slice(1));
	});

	showPanel(location.hash.slice(1));

	document
		.querySelectorAll("[data-show-if-field-checked], [data-show-if-field-unchecked]")
		.forEach(setupDependency);
})();
