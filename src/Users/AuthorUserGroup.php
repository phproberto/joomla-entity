<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\PredefinedUserGroup;
use Phproberto\Joomla\Entity\Users\RegisteredUserGroup;

/**
 * AuthorUserGroup entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class AuthorUserGroup extends PredefinedUserGroup
{
	/**
	 * Group title.
	 *
	 * @const
	 */
	const TITLE = 'Author';

	/**
	 * Predefined data to load the group.
	 *
	 * @return  array
	 */
	public static function predefinedData()
	{
		return [
			'parent_id' => RegisteredUserGroup::instanceOrCreate()->id(),
			'title'     => self::TITLE
		];
	}
}
