<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\GuestUserGroup;
use Phproberto\Joomla\Entity\Users\PredefinedViewLevel;

/**
 * GuestViewLevel entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class GuestViewLevel extends PredefinedViewLevel
{
	/**
	 * View level title.
	 *
	 * @const
	 */
	const TITLE = 'Guest';

	/**
	 * Predefined data to load the group.
	 *
	 * @return  array
	 */
	public static function predefinedData()
	{
		return [
			'title' => self::TITLE,
			'rules' => json_encode(
				[
					GuestUserGroup::instanceOrCreate()->id()
				]
			)
		];
	}
}
