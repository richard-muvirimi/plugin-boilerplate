(function ($) {
	'use strict';

	$(document).ready(function () {

		/**
		 * Generate the slug field if not provided
		 * 
		 * Use internal wordpress function to reduce disparencies
		 */
		$("#" + window.plugin_boilerplate.name + "-name").change(function () {

			if ($("#" + window.plugin_boilerplate.name + "-slug").val() == "") {

				$.post({
					url: window.plugin_boilerplate.cb,
					data: {
						title: $(this).val()
					},
					success: function (response) {
						if (response.success) {
							let _inputSlug = $("#" + window.plugin_boilerplate.name + "-slug");

							if (_inputSlug.val() == "") {
								_inputSlug.val(response.data.title);
							}
						}
					},
				});

			}

		});
	});

})(jQuery);
