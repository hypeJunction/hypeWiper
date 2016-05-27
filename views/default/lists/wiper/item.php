<?php

$query = elgg_extract('query', $vars);
$entity = elgg_extract('entity', $vars);
/* @var $entity ElggObject */

$size = elgg_extract('size', $vars, 'small');

$type = $entity->getType();
$subtype = $entity->getSubtype();
$owner = $entity->getOwnerEntity();
$container = $entity->getContainerEntity();

if (!$entity->getVolatileData('search_matched_title')) {
	$display_name = $entity->getDisplayName() ? : $entity->title;
	if (!$display_name) {
		$display_name = elgg_get_excerpt($entity->description, 50);
	}
	$title = search_get_highlighted_relevant_substrings($display_name, $query);
	$entity->setVolatileData('search_matched_title', $title);
}

$title = $entity->getVolatileData('search_matched_title');
$title = elgg_view('output/url', array(
	'text' => $title,
	'href' => $url,
	'target' => '_blank',
	'class' => 'search-matched-title',
		));

$icon = $entity->getVolatileData('search_icon');
if (!$icon) {
	if ($type == 'user' || $type == 'group' || $entity instanceof ElggFile) {
		$icon = elgg_view_entity_icon($entity, $size);
	} elseif ($owner instanceof ElggUser) {
		$icon = elgg_view_entity_icon($owner, $size);
	} else if ($container instanceof ElggUser) {
		// display a generic icon if no owner, though there will probably be
		// other problems if the owner can't be found.
		$icon = elgg_view_entity_icon($entity, $size);
	}
}

$url = $entity->getVolatileData('search_url');
if (!$url) {
	$url = $entity->getURL();
}

$subtitle = array();

if ($subtype) {
	$type_keys = array(
		"$type:$subtype",
		"$type:default",
		"item:$type:$subtype",
		"item:$type",
	);
	foreach ($type_keys as $key) {
		if (elgg_language_key_exists($key)) {
			$subtitle['type'] = elgg_echo($key);
			break;
		}
	}
}

$byline = array();
if ($type == 'object') {
	if ($owner) {
		$owner_link = elgg_view('output/url', array(
			'text' => $owner->getDisplayName(),
			'href' => $owner->getURL(),
		));
		$byline[] = elgg_echo('wiper:owner', [$owner_link]);
	}
	if ($container && !$container instanceof ElggUser && $container->guid != $owner->guid) {
		$container_link = elgg_view('output/url', array(
			'text' => $container->getDisplayName(),
			'href' => $container->getURL(),
		));
		$byline[] = elgg_echo('wiper:container', [$container_link]);
	}
}

if ($type == 'object') {
	$time = $entity->getVolatileData('search_time');
	if (!$time) {
		$time = elgg_view_friendly_time($entity->time_created);
	}
	$byline[] = $time;
}

if (!empty($byline)) {
	$subtitle['byline'] = implode(' ', $byline);
}

$last_action = $entity->getVolatileData('select:last_action') ? : max($entity->last_action, $entity->last_login, $entity->time_created);
if ($last_action - $entity->time_created >= 24*60*60) {
	$subtitle['last_action'] = elgg_echo("wiper:last_action", [elgg_get_friendly_time($last_action)]);
}

if (!$entity->isEnabled()) {
	$subttitle['disabled'] = elgg_echo('wiper:disabled');
}

$subtitle = elgg_trigger_plugin_hook('subtitle', "search:$type:$subtype", $vars, $subtitle);

$subtitle_str = '';
foreach ($subtitle as $s) {
	$subtitle_str .= elgg_format_element('span', ['class' => 'wiper-subtitle-element'], $s);
}

if ($query) {
	if (!$entity->getVolatileData('search_matched_description')) {
		$desc = search_get_highlighted_relevant_substrings($entity->description, $query);
		$entity->setVolatileData('search_matched_description', $desc);
	}

	if (!$entity->getVolatileData('search_matched_extra')) {

		$fields = elgg_get_registered_tag_metadata_names();
		$prefix = 'tag_names';
		$exclude = array('title', 'description');

		$matches = array();
		foreach ($fields as $field) {
			if (in_array($field, $exclude)) {
				continue;
			}
			$metadata = $entity->$field;
			if (is_array($metadata)) {
				foreach ($metadata as $text) {
					if (stristr($text, $query)) {
						$matches["$prefix:$field"][] = search_get_highlighted_relevant_substrings($text, $query);
					}
				}
			} else {
				if (stristr($metadata, $query)) {
					$matches["$prefix:$field"][] = search_get_highlighted_relevant_substrings($metadata, $query);
				}
			}
		}

		$extra = array();
		foreach ($matches as $label => $match) {
			$extra[] = elgg_format_element('span', ['class' => 'search-match-extra-label'], elgg_echo($label)) . implode(', ', $match);
		}

		$entity->setVolatileData('search_matched_extra', implode('<br />', $extra));
	}

	$description = $entity->getVolatileData('search_matched_description');
	$extra_info = $entity->getVolatileData('search_matched_extra');

	$content = '';
	if ($description) {
		$content .= elgg_format_element('div', ['class' => 'search-matched-description'], $description);
	}
	if ($extra_info) {
		$content .= elgg_format_element('div', ['class' => 'search-matched-extra'], $extra_info);
	}
}

$metadata = elgg_view_menu('entity', [
	'entity' => $entity,
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);

$summary = elgg_view("$type/elements/summary", array(
	'entity' => $entity,
	'tags' => false,
	'title' => $title,
	'subtitle' => $subtitle_str,
	'content' => $content,
	'metadata' => $metadata,
		));

$view = elgg_view_image_block($icon, $summary);

$checkbox = elgg_format_element('input', [
	'type' => 'checkbox',
	'name' => 'guids[]',
	'value' => $entity->guid,
	'class' => 'wiper-checkbox',
]);

?>
<div class="wiper-list-item" data-guid="<?= $entity->guid ?>">
	<div class="wiper-input"><?= $checkbox ?></div>
	<div class="wiper-view"><?= $view ?></div>
</div>
