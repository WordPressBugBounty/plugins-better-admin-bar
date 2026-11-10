/**
 * Icon picker: the Lucide icons, with search.
 *
 * The icons are loaded when the picker is opened the first time.
 * Picking an icon triggers "iconPicker:selected" on the document with the button, the icon name & its SVG markup.
 *
 * Used global objects: jQuery, SwiftControlIcons.
 */
(function ($) {
	var settings = window.SwiftControlIcons || { labels: {} };
	var labels = settings.labels || {};
	var request = null;
	var icons = null;
	var tags = {};

	// Shown first, the rest follows in alphabetical order.
	var popular = [
		"gauge", "layout-dashboard", "square-pen", "pencil", "paintbrush", "palette", "file-plus", "file-text",
		"images", "image", "plug", "settings", "log-out", "house", "users", "user", "message-square", "mail",
		"shopping-cart", "store", "chart-column", "search", "link", "external-link", "globe", "layout-template",
		"folder", "calendar", "bell", "star", "heart", "lock", "shield", "wrench", "database", "code", "zap", "rocket",
	];

	/**
	 * Load the icons and their search words (once).
	 *
	 * @return {Promise} Resolves when the icons are there.
	 */
	function load() {
		if (!request) {
			request = $.when($.getJSON(settings.iconsUrl), $.getJSON(settings.tagsUrl)).then(
				function (iconsResponse, tagsResponse) {
					icons = iconsResponse[0] || {};
					tags = tagsResponse[0] || {};
				},
				function () {
					// Try again next time.
					request = null;
				}
			);
		}

		return request;
	}

	/**
	 * Get the SVG markup of an icon.
	 *
	 * @param {string} name The icon name.
	 * @return {string} The SVG markup.
	 */
	function svg(name) {
		return (
			'<svg class="swift-control-icon" data-icon="' + name + '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' +
			(icons && icons[name] ? icons[name] : "") +
			"</svg>"
		);
	}

	/**
	 * Escape text for an HTML attribute.
	 *
	 * @param {string} text The text.
	 * @return {string} The escaped text.
	 */
	function escapeAttr(text) {
		return String(text)
			.replace(/&/g, "&amp;")
			.replace(/"/g, "&quot;")
			.replace(/</g, "&lt;")
			.replace(/>/g, "&gt;");
	}

	/**
	 * Get the icon names in the order of the picker.
	 *
	 * @return {Array} The icon names.
	 */
	function orderedNames() {
		var first = popular.filter(function (name) {
			return icons.hasOwnProperty(name);
		});

		return first.concat(
			Object.keys(icons)
				.sort()
				.filter(function (name) {
					return first.indexOf(name) === -1;
				})
		);
	}

	/**
	 * Close every open picker.
	 */
	function close() {
		$(".icon-picker-container").remove();
		$(document).off(".icon-picker");
	}

	/**
	 * Open the picker under a button.
	 *
	 * @param {HTMLElement} field The button that opens the picker.
	 */
	function open(field) {
		close();

		var popup = $(
			'<div class="icon-picker-container">' +
				'<div class="icon-picker-control"><input type="search" class="icon-picker-search" autocomplete="off"></div>' +
				'<p class="icon-picker-message" role="status"></p>' +
				'<ul class="icon-picker-list"></ul>' +
				"</div>"
		);
		var search = popup.find(".icon-picker-search");
		var message = popup.find(".icon-picker-message");
		var list = popup.find(".icon-picker-list");

		search.attr({ placeholder: labels.search, "aria-label": labels.search });
		message.text(labels.loading);
		popup.appendTo(field.parentNode);
		search.trigger("focus");

		load().then(
			function () {
				if (!popup.closest("body").length) return;

				var html = "";
				var current = field.closest(".widget-icon").querySelector("svg");
				var currentName = current ? current.getAttribute("data-icon") : "";

				orderedNames().forEach(function (name) {
					html +=
						'<li data-search="' + escapeAttr(name.replace(/-/g, " ") + " " + (tags[name] || "")) + '">' +
						'<button type="button" class="icon-picker-item' + (name === currentName ? " is-current" : "") + '" data-icon="' + name + '" title="' + name + '" aria-label="' + name + '">' +
						svg(name) +
						"</button></li>";
				});

				list.html(html);
				message.text("");
				filter();
			},
			function () {
				message.text(labels.error);
			}
		);

		/**
		 * Show the icons that match the search.
		 */
		function filter() {
			var words = search.val().toLowerCase().trim().split(/\s+/).filter(Boolean);
			var found = 0;

			list.children().each(function () {
				var text = this.getAttribute("data-search");
				var match = words.every(function (word) {
					return text.indexOf(word) !== -1;
				});

				this.hidden = !match;
				if (match) found++;
			});

			if (icons) message.text(found ? "" : labels.none);
		}

		search.on("input", filter);

		popup.on("keydown", function (e) {
			if (e.key === "Escape") {
				e.preventDefault();
				e.stopPropagation();
				close();
				field.focus();
			}
		});

		list.on("click", ".icon-picker-item", function () {
			var name = this.getAttribute("data-icon");

			field.dataset.icon = name;
			$(document).trigger("iconPicker:selected", [field, name, svg(name)]);
			close();
			field.focus();
		});

		$(document).on("mouseup.icon-picker", function (e) {
			if (e.target !== field && !popup.is(e.target) && popup.has(e.target).length === 0) {
				close();
			}
		});
	}

	$(document).on("click", ".icon-picker", function () {
		var isOpen = $(this.parentNode).children(".icon-picker-container").length > 0;

		if (isOpen) {
			close();
		} else {
			open(this);
		}
	});
})(jQuery);
