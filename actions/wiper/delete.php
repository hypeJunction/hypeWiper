<?php

$guids = (array) get_input('guids', []);

$count = count($guids);
$deleted = [];
$success = 0;
$error = 0;

foreach ($guids as $guid) {
	$entity = get_entity($guid);
	if ($entity && $entity->delete()) {
		$deleted[] = $guid;
		$success++;
	} else {
		$error++;
	}
}

if ($error == $count) {
	register_error(elgg_echo('wiper:delete:error'));
} else if ($error) {
	system_message(elgg_echo('wiper:delete:partial_success', [$success, $count]));
} else {
	system_message(elgg_echo('wiper:delete:success', [$count]));
}

if (elgg_is_xhr()) {
	echo json_encode([
		'clear' => $deleted,
	]);
}
