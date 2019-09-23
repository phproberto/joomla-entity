<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\AuthorUserGroup;
use Phproberto\Joomla\Entity\Users\ManagerUserGroup;
use Phproberto\Joomla\Entity\Users\PredefinedViewLevel;
use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;

/**
 * SpecialViewLevel entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class SpecialViewLevel extends PredefinedViewLevel
{
	/**
	 * View level title.
	 *
	 * @const
	 */
	const TITLE = 'Special';

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
					AuthorUserGroup::instanceOrCreate()->id(),
					ManagerUserGroup::instanceOrCreate()->id(),
					SuperUsersUserGroup::instanceOrCreate()->id()
				]
			)
		];
	}
}
