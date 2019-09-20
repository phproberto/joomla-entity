<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\PredefinedVieLevel;

/**
 * PublicViewLevel entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class PublicViewLevel extends PredefinedVieLevel
{
	/**
	 * View level title.
	 *
	 * @const
	 */
	const TITLE = 'Public';

	/**
	 * Predefined data to load the group.
	 *
	 * @return  array
	 */
	public static function predefinedData()
	{
		return [
			'title' => self::TITLE
		];
	}
}
