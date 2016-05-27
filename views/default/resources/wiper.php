<?php

$container_guid = elgg_extract('container_guid', $vars);

elgg_entity_gatekeeper($container_guid);
elgg_group_gatekeeper(true, $container_guid);

$container = get_entity($container_guid);

if (!$container->canEdit()) {
	forward('', '403');
}

elgg_set_page_owner_guid($container->guid);

$title = elgg_echo('wiper');
elgg_push_breadcrumb($container->getDisplayName(), $container->getURL());
elgg_push_breadcrumb($title);

$content = elgg_view('lists/wiper', array(
	'entity' => $container,
		));

if (elgg_is_xhr()) {
	echo $content;
} else {
	$layout = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => false,
	));

	echo elgg_view_page($title, $layout);
}