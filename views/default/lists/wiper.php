<?php

$entity = elgg_extract('entity', $vars);
$guid = (int) $entity->guid;

$base_url = elgg_normalize_url("wiper/owner/$entity->guid") . '?' . parse_url(current_page_url(), PHP_URL_QUERY);

$list_class = (array) elgg_extract('list_class', $vars, array());
$list_class[] = 'wiper-list';

$item_class = (array) elgg_extract('item_class', $vars, array());

$options = (array) elgg_extract('options', $vars, array());

$list_options = array(
	'full_view' => false,
	'limit' => elgg_extract('limit', $vars, elgg_get_config('default_limit')) ? : 10,
	'list_class' => implode(' ', $list_class),
	'item_class' => implode(' ', $item_class),
	'no_results' => elgg_echo('wiper:no_results'),
	'pagination' => true,
	'pagination_type' => 'default',
	'base_url' => $base_url,
	'list_id' => "wiper-$entity->guid",
	'item_view' => 'lists/wiper/item',
);

$subtype = get_input('entity_subtype', ELGG_ENTITIES_NO_VALUE);
if (!$subtype) {
	$types = get_registered_entity_types();
	$types = elgg_trigger_plugin_hook('search_types', 'get_queries', $params, $types);
	$subtype = elgg_extract('object', $types);
}

$owner_guid = $container_guid = ELGG_ENTITIES_ANY_VALUE;
if ($entity instanceof ElggGroup) {
	$container_guid = $entity->guid;
} else if ($entity instanceof ElggUser) {
	$owner_guid = $entity->guid;
}

$getter_options = array(
	'type' => 'object',
	'subtype' => $subtype,
	'owner_guid' => $owner_guid,
	'container_guid' => $container_guid,
	'search_type' => 'entities',
	'query' => elgg_extract('query', $vars),
	'preload_owner' => true,
	'preload_containers' => true,
	'search_tags' => false,
);

$options = array_merge($list_options, $options, $getter_options);

$ha = access_get_show_hidden_status();
if (elgg_is_admin_logged_in()) {
	access_show_hidden_entities(true);
}
if (elgg_is_active_plugin('object_sort')) {
	$params = $vars;
	$params['options'] = $options;
	$params['callback'] = 'elgg_list_entities';
	$params['show_search'] = true;
	$params['show_sort'] = true;
	$params['show_subtype'] = true;

	$list = elgg_view('lists/objects', $params);
} else {
	$list = elgg_list_entities($options);
}

access_show_hidden_entities($ha);

echo elgg_format_element('div', [
	'class' => 'wiper-module',
		], $list);

