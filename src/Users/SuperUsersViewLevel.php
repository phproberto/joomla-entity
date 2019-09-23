<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;
use Phproberto\Joomla\Entity\Users\PredefinedViewLevel;

/**
 * SuperUsersViewLevel entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class SuperUsersViewLevel extends PredefinedViewLevel
{
	/**
	 * View level title.
	 *
	 * @const
	 */
	const TITLE = 'Super Users';

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
					SuperUsersUserGroup::instanceOrCreate()->id()
				]
			)
		];
	}
}
