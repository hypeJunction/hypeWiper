<?php

$guids = (array) get_input('guids', []);

$count = count($guids);
$enabled = [];
$success = 0;
$error = 0;

foreach ($guids as $guid) {
	$entity = get_entity($guid);
	if ($entity && !$entity->isEnabled() && $entity->enable('wiper unarchived')) {
		$enabled[] = $guid;
		$success++;
	} else {
		$error++;
	}
}

if ($error == $count) {
	register_error(elgg_echo('wiper:enable:error'));
} else if ($error) {
	system_message(elgg_echo('wiper:enable:partial_success', [$success, $count]));
} else {
	system_message(elgg_echo('wiper:enable:success', [$count]));
}
