/**
 * Settings page: color pickers, the role checkboxes & saving the settings.
 *
 * Used global objects: ajaxurl, jQuery, SwiftControl.
 */
(function ($) {
	var form = document.querySelector(".swift-control-settings-form");
	var isRequesting = false;
	var noticeTimeout = 0;

	if (!form) return;

	/**
	 * Color pickers, with an instant preview of the quick access panel.
	 */
	function setupColorPickers() {
		$(form)
			.find(".color-picker-field")
			.wpColorPicker({
				palettes: true,
				hide: true,
				change: function (e, ui) {
					if (!e.target.classList.contains("has-instant-preview")) return;

					document.dispatchEvent(
						new CustomEvent("swift_control_" + e.target.id + "_change", {
							detail: {
								fieldId: e.target.id,
								hexColor: ui.color.toString(),
							},
						})
					);
				},
			});
	}

	/**
	 * "All users" checks & locks every role. Unchecking it brings back the roles picked before.
	 */
	function setupRoles() {
		var all = form.querySelector(".remove-by-roles-all");
		var roles = form.querySelectorAll(".remove-by-roles-role");
		var picked = [];

		if (!all) return;

		all.addEventListener("change", function () {
			if (all.checked) {
				picked = [];

				roles.forEach(function (role) {
					if (role.checked) picked.push(role.value);
				});
			}

			roles.forEach(function (role) {
				role.checked = all.checked || picked.indexOf(role.value) !== -1;
				role.disabled = all.checked;
			});
		});
	}

	/**
	 * Show a message next to every save button.
	 *
	 * @param {string} type    "success" or "error".
	 * @param {string} message The message.
	 */
	function showNotice(type, message) {
		var notices = form.querySelectorAll(".swift-control-notice");

		clearTimeout(noticeTimeout);

		notices.forEach(function (notice) {
			notice.textContent = message;
			notice.classList.remove("is-success", "is-error");
			notice.classList.add("is-shown", "is-" + type);
		});

		noticeTimeout = setTimeout(function () {
			notices.forEach(function (notice) {
				notice.classList.remove("is-shown");
			});
		}, 2500);
	}

	/**
	 * Collect the fields & save them.
	 */
	function saveSettings() {
		var buttons = form.querySelectorAll(".save-general-settings");
		var data = {
			action: "swift_control_save_general_settings",
			nonce: SwiftControl.nonces.saveGeneralSettings,
			remove_by_roles: [],
		};

		if (isRequesting) return;
		isRequesting = true;

		form.querySelectorAll(".general-setting-field").forEach(function (field) {
			data[field.name] =
				field.type === "checkbox" ? (field.checked ? 1 : 0) : field.value;
		});

		form
			.querySelectorAll('input[name="remove_by_roles[]"]:checked')
			.forEach(function (role) {
				// "All users" covers the roles.
				if (data.remove_by_roles.indexOf("all") === -1) {
					data.remove_by_roles.push(role.value);
				}
			});

		buttons.forEach(function (button) {
			button.classList.add("is-loading");
		});

		$.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: data,
		})
			.done(function (r) {
				if (r && r.success) {
					showNotice("success", SwiftControl.labels.saved);
				} else {
					showNotice("error", r && r.data ? r.data : SwiftControl.labels.error);
				}
			})
			.fail(function () {
				showNotice("error", SwiftControl.labels.error);
			})
			.always(function () {
				buttons.forEach(function (button) {
					button.classList.remove("is-loading");
				});

				isRequesting = false;
			});
	}

	setupColorPickers();
	setupRoles();

	form.addEventListener("submit", function (e) {
		e.preventDefault();
		saveSettings();
	});
})(jQuery);
