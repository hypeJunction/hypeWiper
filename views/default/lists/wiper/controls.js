define(function (require) {

	var elgg = require('elgg');
	var $ = require('jquery');
	var spinner = require('elgg/spinner');

	function toggleUiElements($container) {
		var $container = $container || $('body');
		if ($('.wiper-checkbox:visible:checked', $container).length) {
			$('.wiper-action', $container).removeClass('elgg-state-disabled').prop('disabled', false);
		} else {
			$('.wiper-action', $container).addClass('elgg-state-disabled').prop('disabled', true);
		}
		if ($('.wiper-checkbox:visible:checked', $container).length === $('.wiper-checkbox:visible').length) {
			$('.wiper-checkbox-toggle', $container).prop('checked', true);
		} else {
			$('.wiper-checkbox-toggle', $container).prop('checked', false);
		}
	}

	$(document).on('change', '.wiper-checkbox-toggle', function () {
		var $container = $(this).closest('.wiper-module');
		$('.wiper-checkbox:visible').prop('checked', $(this).is(':checked'));
		toggleUiElements($container);
	});

	$(document).on('change', '.wiper-checkbox', function () {
		var $container = $(this).closest('.wiper-module');
		toggleUiElements($container);
	});

	$(document).on('click', '.wiper-action', function (e) {
		if (!confirm(elgg.echo('question:areyousure'))) {
			return false;
		}

		e.preventDefault();
		var $container = $(this).closest('.wiper-module');
		var href = $(this).data('href');
		var $checked = $('.wiper-checkbox:visible:checked', $container);
		var guids = [];
		$checked.each(function() {
			guids.push($(this).val());
		});
		elgg.action(href, {
			data: {
				guids: guids
			},
			beforeSend: spinner.start,
			complete: spinner.stop,
			success: function (response) {
				if (response.status >= 0 && response.output.clear) {
					$.each(response.output.clear, function(index, value) {
						$('#elgg-object-' + value).fadeOut().remove();
					});
					toggleUiElements($container);
					$container.find('.elgg-list').trigger('refresh');
				}
			}
		});
	});

});
