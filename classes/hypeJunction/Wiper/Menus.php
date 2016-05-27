<?php

namespace hypeJunction\Wiper;

use ElggGroup;
use ElggMenuItem;
use ElggUser;

class Menus {

	/**
	 * Setup owner block menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:owner_block"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupPageMenu($hook, $type, $return, $params) {

		if (!elgg_in_context('settings') && !elgg_in_context('groups')) {
			return;
		}
		
		$entity = elgg_get_page_owner_entity();
		if (!$entity instanceof ElggUser && !$entity instanceof ElggGroup) {
			return;
		}

		if (!$entity->canEdit()) {
			return;
		}
		
		$return[] = ElggMenuItem::factory([
			'name' => 'wiper',
			'text' => elgg_echo('wiper'),
			'href' => "wiper/owner/$entity->guid",
		]);

		return $return;
	}
}
