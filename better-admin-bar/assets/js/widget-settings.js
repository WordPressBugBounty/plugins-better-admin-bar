/**
 * Settings page: the widgets of the quick access panel (drag & drop, edit, preview).
 *
 * Used global objects: ajaxurl, jQuery, SwiftControl.
 */
(function ($) {
	var activeList = document.getElementById("active-items");
	var statusIndicator = document.querySelector(
		".active-widgets-box .heatbox-status-indicator"
	);
	var isRequesting = false;

	if (!activeList) return;

	/**
	 * Drag & drop between the active & available widgets.
	 */
	function setupSortable() {
		var options = {
			connectWith: ".widget-items",
			// A widget in edit mode stays in place.
			cancel: ".edit-mode, button",
			distance: 5,
			placeholder: "widget-item-placeholder",
			tolerance: "pointer",
			// The dragged widget shrinks to the size of the ones in the Widgets box, keep it under the cursor.
			cursorAt: { left: 28, top: 29 },
			start: function (e, ui) {
				ui.helper.css({
					width: $("#available-items").width(),
					height: "auto",
				});
			},
		};

		$(activeList).sortable(
			$.extend({}, options, {
				// Only the active list saves, it's part of every move.
				update: changeWidgetsOrder,
			})
		);

		$("#available-items").sortable(
			$.extend({}, options, {
				receive: function (e, ui) {
					escapeEditMode(ui.item[0]);
				},
			})
		);
	}

	/**
	 * Set the readonly state of a widget's name field.
	 *
	 * @param {HTMLElement} widgetItem The widget item.
	 * @param {boolean}     readonly   Whether the field is readonly.
	 */
	function setReadonly(widgetItem, readonly) {
		var field = widgetItem.querySelector(".widget-text-field");
		if (field) field.readOnly = readonly;
	}

	/**
	 * Get the icon of a widget.
	 *
	 * @param {HTMLElement} widgetItem The widget item.
	 * @return {SVGElement} The icon.
	 */
	function getIcon(widgetItem) {
		return widgetItem.querySelector(".widget-icon > svg");
	}

	/**
	 * Replace the icon of a widget.
	 *
	 * @param {HTMLElement} widgetItem The widget item.
	 * @param {string}      markup     The SVG markup of the new icon.
	 */
	function setIcon(widgetItem, markup) {
		var icon = getIcon(widgetItem);
		var template = document.createElement("template");

		template.innerHTML = markup.trim();
		if (icon && template.content.firstElementChild) {
			icon.replaceWith(template.content.firstElementChild);
		}
	}

	/**
	 * Start editing a widget.
	 *
	 * @param {HTMLElement} widgetItem The widget item.
	 */
	function switchEditMode(widgetItem) {
		escapeEditMode("all");

		// Remember the saved values, Escape puts them back.
		widgetItem.swiftControlSaved = getValues(widgetItem);
		widgetItem.classList.add("edit-mode");
		widgetItem.querySelector(".edit-button").textContent = SwiftControl.labels.save;
		setReadonly(widgetItem, false);
		widgetItem.querySelector(".widget-text-field").focus();
	}

	/**
	 * Get the editable values of a widget.
	 *
	 * @param {HTMLElement} widgetItem The widget item.
	 * @return {Object} The values.
	 */
	function getValues(widgetItem) {
		var newTab = widgetItem.querySelector(".new-tab-field");
		var redirectUrl = widgetItem.querySelector(".redirect-url-field");

		return {
			icon: getIcon(widgetItem).outerHTML,
			text: widgetItem.querySelector(".widget-text-field").value,
			newTab: newTab ? newTab.checked : null,
			redirectUrl: redirectUrl ? redirectUrl.value : null,
		};
	}

	/**
	 * Put values back into a widget.
	 *
	 * @param {HTMLElement} widgetItem The widget item.
	 * @param {Object}      values     The values from getValues().
	 */
	function setValues(widgetItem, values) {
		var newTab = widgetItem.querySelector(".new-tab-field");
		var redirectUrl = widgetItem.querySelector(".redirect-url-field");

		setIcon(widgetItem, values.icon);
		widgetItem.querySelector(".widget-text-field").value = values.text;
		if (newTab && values.newTab !== null) newTab.checked = values.newTab;
		if (redirectUrl && values.redirectUrl !== null) redirectUrl.value = values.redirectUrl;
	}

	/**
	 * Stop editing a widget (or all with "all"). Unsaved changes are undone.
	 *
	 * @param {HTMLElement|string} widgetItem The widget item or "all".
	 * @param {boolean}            keep       Keep the changes (they are being saved).
	 */
	function escapeEditMode(widgetItem, keep) {
		var items =
			widgetItem === "all"
				? document.querySelectorAll(".swift-control-settings .widget-item")
				: [widgetItem];

		items.forEach(function (item) {
			var button = item.querySelector(".edit-button");

			if (item.classList.contains("edit-mode") && !keep && item.swiftControlSaved) {
				setValues(item, item.swiftControlSaved);
			}

			item.classList.remove("edit-mode");
			if (button) button.textContent = SwiftControl.labels.edit;
			setReadonly(item, true);
		});

		$(".icon-picker-container").remove();
		$(document).off(".icon-picker");
	}

	/**
	 * Show the status in the box title.
	 *
	 * @param {string} state "loading" or "done".
	 */
	function setStatus(state) {
		if (!statusIndicator) return;

		statusIndicator.classList.remove("is-loading", "is-done");
		statusIndicator.textContent = "";

		if (state === "done") {
			statusIndicator.textContent = SwiftControl.labels.saved;
			// Restart the fade-out animation.
			void statusIndicator.offsetWidth;
		}

		statusIndicator.classList.add("is-" + state);
	}

	/**
	 * Run one request at a time, they write to the same option.
	 *
	 * @param {Function} request The request, returns a promise.
	 * @param {Function} onFail  Called when the request fails (optional).
	 */
	function queue(request, onFail) {
		if (isRequesting) {
			setTimeout(function () {
				queue(request, onFail);
			}, 250);

			return;
		}

		isRequesting = true;
		setStatus("loading");

		request()
			.done(function () {
				buildPreview();
				setStatus("done");
			})
			.fail(function (message) {
				setStatus("");
				if (onFail) onFail();
				window.alert(typeof message === "string" && message ? message : SwiftControl.labels.error);
			})
			.always(function () {
				isRequesting = false;
			});
	}

	/**
	 * Save the settings of a widget.
	 *
	 * @param {HTMLElement} widgetItem The widget item.
	 */
	function saveWidgetSettings(widgetItem) {
		var data = {
			action: "swift_control_change_widget_settings",
			nonce: SwiftControl.nonces.changeWidgetSettings,
			widget_key: widgetItem.dataset.widgetKey,
			icon_class: getIcon(widgetItem).getAttribute("data-icon"),
			text: widgetItem.querySelector(".widget-text-field").value,
		};
		var newTab = widgetItem.querySelector(".new-tab-field");
		var redirectUrl = widgetItem.querySelector(".redirect-url-field");

		if (newTab) data.new_tab = newTab.checked ? 1 : 0;
		if (redirectUrl) data.redirect_url = redirectUrl.value;

		var saved = widgetItem.swiftControlSaved;

		escapeEditMode(widgetItem, true);

		queue(
			function () {
				return post(data);
			},
			function () {
				// Not saved: show the saved values again.
				if (saved) setValues(widgetItem, saved);
			}
		);
	}

	/**
	 * Send an ajax request that fails unless the server says it worked.
	 *
	 * @param {Object} data The request data.
	 * @return {Promise} The request.
	 */
	function post(data) {
		return $.post(ajaxurl, data, null, "json").then(function (r) {
			return r && r.success ? r : $.Deferred().reject(r && r.data);
		});
	}

	/**
	 * Save the active widgets & their order.
	 */
	function changeWidgetsOrder() {
		var keys = [];

		activeList.querySelectorAll(".widget-item").forEach(function (el) {
			keys.push(el.dataset.widgetKey);
		});

		queue(function () {
			return post({
				action: "swift_control_change_widgets_order",
				nonce: SwiftControl.nonces.changeWidgetsOrder,
				active_widgets: keys,
			});
		});
	}

	/**
	 * Rebuild the preview of the quick access panel from the active widgets.
	 */
	function buildPreview() {
		var panel = document.querySelector(".swift-control-widgets");
		if (!panel) return;

		// Keep the settings button (the first item).
		$(panel).find(".swift-control-widget-item").not(":first").remove();

		activeList.querySelectorAll(".widget-item").forEach(function (el) {
			var item = document.createElement("li");
			var link = document.createElement("a");
			var title = document.createElement("span");

			item.className = "swift-control-widget-item";
			item.dataset.widgetKey = el.dataset.widgetKey;
			link.className = "swift-control-widget-link";
			link.href = "#";
			title.className = "swift-control-widget-title";
			title.textContent = el.querySelector(".widget-text-field").value;

			link.appendChild(getIcon(el).cloneNode(true));
			item.appendChild(link);
			item.appendChild(title);
			panel.appendChild(item);
		});

		rebuildTransition(panel);
	}

	/**
	 * Rebuild the staggered animation of the preview's widgets.
	 *
	 * @param {HTMLElement} panel The preview panel.
	 */
	function rebuildTransition(panel) {
		var items = [].slice.call(panel.querySelectorAll(".swift-control-widget-item")).slice(1);
		var oldStyle = document.querySelector(".swift-control-transition-style");
		var style = document.querySelector(".swift-control-preview-transition-style");
		var transition = 75;
		var showingDelay = 0;
		var hidingDelay = (items.length - 1) * transition;
		var css = "";

		if (oldStyle) oldStyle.remove();
		if (!style) return;

		items.forEach(function (el) {
			var selector = '[data-widget-key="' + CSS.escape(el.dataset.widgetKey || "") + '"]';

			css += ".swift-control-widgets " + selector + " { transition-delay: " + hidingDelay + "ms; }";
			css += ".swift-control-widgets.is-expanded " + selector + " { transition-delay: " + showingDelay + "ms; }";

			showingDelay += transition;
			hidingDelay -= transition;
		});

		// The settings button rounds its bottom corner while the first widget fades out.
		css += ".swift-control-widgets { --swift-control-radius-delay: " + (Math.max(0, items.length - 1) * transition + 150) + "ms; }";

		style.textContent = css;
	}

	/**
	 * Register the events.
	 */
	function setupEvents() {
		var $settings = $(".swift-control-settings");

		$settings.on("click", ".widget-item .edit-button", function () {
			var widgetItem = this.closest(".widget-item");

			if (widgetItem.classList.contains("edit-mode")) {
				saveWidgetSettings(widgetItem);
			} else {
				switchEditMode(widgetItem);
			}
		});

		$settings.on("dblclick", ".active-items .dblclick-trigger", function () {
			var widgetItem = this.closest(".widget-item");

			if (!widgetItem.classList.contains("edit-mode")) switchEditMode(widgetItem);
		});

		// Enter in the icon search would submit the settings form.
		$settings.on("keydown", ".icon-picker-container input", function (e) {
			if (e.key === "Enter") e.preventDefault();
		});

		// Not the search field of the icon picker.
		$settings.on("keydown", ".widget-item > input, .widget-extra-settings input", function (e) {
			var widgetItem = this.closest(".widget-item");

			if (e.key === "Enter") {
				// Don't submit the settings form.
				e.preventDefault();
				if (widgetItem.classList.contains("edit-mode")) saveWidgetSettings(widgetItem);
			} else if (e.key === "Escape") {
				escapeEditMode(widgetItem);
			}
		});

		$settings.on("click", ".widget-item .icon-picker", function () {
			var widgetItem = this.closest(".widget-item");

			if (!widgetItem.classList.contains("edit-mode")) return;

			window.getSelection().removeAllRanges();
			widgetItem.querySelector(".widget-text-field").blur();
		});

		$(document).on("iconPicker:selected", function (e, trigger, name, markup) {
			setIcon(trigger.closest(".widget-item"), markup);
		});
	}

	setupSortable();
	setupEvents();
})(jQuery);
