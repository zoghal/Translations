var Nodes = (function (app, $) {

	var Translations = {},
		saveAll = $('form#TranslationAdminEditLocaleForm > div.form-actions button.btn-primary'),
		textareas = $('form#TranslationAdminEditLocaleForm textarea'),
		buttons = $('button.saveOne');

	/**
	 * Act on blur
	 *
	 * If the content changes mark the save button active
	 */
	function bindTextAreas() {
		textareas.blur(function(e) {
			var button = $(this).parents('div.control-group').find('button');

			if ($(this).val() === this.defaultValue) {
				button
					.addClass('disabled');
			} else {
				button
					.removeClass('disabled');
			}
			updateSaveAll();
		});
	}

	/**
	 * Clicking the save button for one translations, should save one translation only
	 */
	function bindSaveOne() {
		buttons.click(function(e) {
			var that = this,
				textarea;

			e.preventDefault();

			if ($(this).hasClass('disabled')) {
				return;
			}

			textarea = $(this).parents('div.control-group').find('textarea');

			$.ajax({
				url: '/admin' + Nodes.Router.applicationPrefix() + '/translations/translations/update.json',
				type: 'POST',
				data: {
					Translation: {
						key: textarea.data('key'),
						domain: textarea.data('domain'),
						locale: textarea.data('locale'),
						value: textarea.val()
					}
				},
				success: function() {
					textarea[0].defaultValue = textarea.val();
					$(that).addClass('disabled');
					updateSaveAll();
				}
			});
		});
	}

	/**
	 * Click on save all - click on all the save buttons that are active
	 */
	function bindSaveAll() {
		saveAll.click(function(e) {
			e.preventDefault();

			buttons.not('.disabled').click();
			$(this).addClass('disabled');

		});

		saveAll.addClass('disabled');
	}

	/**
	 * Update the enabled/disabled state of the saveall button
	 *
	 * If there is one or more enabled save button - then the saveAll button should be active
	 */
	function updateSaveAll() {
		if (buttons.not('.disabled').length) {
			saveAll.removeClass('disabled');
		} else {
			saveAll.addClass('disabled');
		}
	}

	/**
	 * Prevent accidentally leaving the page with unsaved translations
	 */
	function warnUnsavedChanges() {
		$(window).bind('beforeunload', function() {
			if ($('button.saveOne').not('.disabled').length) {
				return __('There are unsaved changes - really leave this page?');
			}
		});
	}

	function setupEditLocale() {
		bindTextAreas();
		bindSaveOne();
		bindSaveAll();
		warnUnsavedChanges();
	}

	function setupLanguageSwitch() {
		$('#localeChange').change(function() {
			$.post($(this).data('ping-url'), { locale: $(this).val() }, function() {
				document.location = $(this).data('index-url') + '/' + $(this).val();
			});
		});
	}

	Translations.init = function() {
		if (Nodes.config('Request.controller') === 'translations' && Nodes.config('Request.action') === 'admin_edit_locale') {
			setupEditLocale();
		}
		setupLanguageSwitch();
	}

	app.Translations = Translations;
	app.Translations.init();

	return app;
}(Nodes || {}, jQuery));
