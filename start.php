<?php

/**
 * Bulk delete content
 *
 * @package hypeJunction
 * @subpackage hypeWiper
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', function() {
	elgg_register_page_handler('wiper', '\\hypeJunction\\Wiper\\PageHandler::wiper');

	elgg_register_plugin_hook_handler('register', 'menu:page', '\\hypeJunction\\Wiper\\Menus::setupPageMenu');
	elgg_register_menu_item('admin', [
		'name' => 'wiper',
		'href' => "wiper/site/" . elgg_get_site_entity()->guid,
		'text' => elgg_echo('wiper'),
		'section' => 'administer',
		'parent_name' => 'administer_utilities',
	]);

	elgg_register_action('wiper/disable', __DIR__ . '/actions/wiper/disable.php');
	elgg_register_action('wiper/enable', __DIR__ . '/actions/wiper/enable.php', 'admin');
	elgg_register_action('wiper/delete', __DIR__ . '/actions/wiper/delete.php');

	elgg_extend_view('elgg.css', 'wiper.css');
	elgg_extend_view('page/components/list', 'lists/wiper/controls', 400);
});
