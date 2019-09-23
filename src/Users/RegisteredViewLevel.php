<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\ManagerUserGroup;
use Phproberto\Joomla\Entity\Users\PredefinedViewLevel;
use Phproberto\Joomla\Entity\Users\RegisteredUserGroup;
use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;

/**
 * RegisteredViewLevel entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class RegisteredViewLevel extends PredefinedViewLevel
{
	/**
	 * View level title.
	 *
	 * @const
	 */
	const TITLE = 'Registered';

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
					RegisteredUserGroup::instanceOrCreate()->id(),
					ManagerUserGroup::instanceOrCreate()->id(),
					SuperUsersUserGroup::instanceOrCreate()->id()
				]
			)
		];
	}
}
