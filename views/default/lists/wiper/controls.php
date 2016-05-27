<?php
if (elgg_extract('item_view', $vars) !== 'lists/wiper/item') {
	return;
}

elgg_require_js('lists/wiper/controls');

$label = elgg_echo('wiper:toggle');
$checkbox = elgg_format_element('input', [
	'type' => 'checkbox',
	'class' => 'wiper-checkbox-toggle',
		]);

$view = elgg_format_element('button', [
	'data-href' => elgg_add_action_tokens_to_url(elgg_normalize_url('action/wiper/disable')),
	'data-confirm' => elgg_echo('question:areyousure'),
	'class' => 'wiper-action elgg-button elgg-button-action elgg-state-disabled',
	'disabled' => true,
	'title' => elgg_echo('wiper:disable:help'),
		], elgg_echo('wiper:disable'));

if (elgg_is_admin_logged_in()) {
	$view .= elgg_format_element('button', [
		'data-href' => elgg_add_action_tokens_to_url(elgg_normalize_url('action/wiper/enable')),
		'data-confirm' => elgg_echo('question:areyousure'),
		'class' => 'wiper-action elgg-button elgg-button-action elgg-state-disabled',
		'disabled' => true,
		'title' => elgg_echo('wiper:enable:help'),
			], elgg_echo('wiper:enable'));
}

$view .= elgg_format_element('button', [
	'data-href' => elgg_add_action_tokens_to_url(elgg_normalize_url('action/wiper/delete')),
	'data-confirm' => elgg_echo('question:areyousure'),
	'class' => 'wiper-action elgg-button elgg-button-action elgg-state-disabled',
	'disabled' => true,
	'title' => elgg_echo('wiper:delete:help'),
		], elgg_echo('wiper:delete'));
?>
<div class="wiper-list-item wiper-module-controls" data-guid="<?= $entity->guid ?>">
	<div class="wiper-input"><label><?= $checkbox . $label ?></label></div>
	<div class="wiper-view wiper-buttonbank"><?= $view ?></div>
</div>