<?php

$guids = (array) get_input('guids', []);

$count = count($guids);
$disabled = [];
$success = 0;
$error = 0;

foreach ($guids as $guid) {
	$entity = get_entity($guid);
	if ($entity && $entity->isEnabled() && $entity->disable('wiper archived')) {
		$disabled[] = $guid;
		$success++;
	} else {
		$error++;
	}
}

if ($error == $count) {
	register_error(elgg_echo('wiper:disable:error'));
} else if ($error) {
	system_message(elgg_echo('wiper:disable:partial_success', [$success, $count]));
} else {
	system_message(elgg_echo('wiper:disable:success', [$count]));
}

if (elgg_is_xhr()) {
//	if (elgg_is_admin_logged_in()) {
//		// admin can still see the items
//		$disabled = [];
//	}
	echo json_encode([
		'clear' => $disabled,
	]);
}
