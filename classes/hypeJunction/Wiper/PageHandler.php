<?php

namespace hypeJunction\Wiper;

class PageHandler {

	/**
	 * Handles requests to /wiper
	 * /wiper/owner/<guid>
	 * /wiper/group/<guid>
	 * /wiper/site/<guid>
	 *
	 * @param array  $segments   URL segments
	 * @return bool
	 */
	public static function wiper($segments) {

		$container_type = array_shift($segments);
		$container_guid = array_shift($segments);

		echo elgg_view_resource('wiper', [
			'container_type' => $container_type,
			'container_guid' => $container_guid,
		]);
		return true;
	}

}
