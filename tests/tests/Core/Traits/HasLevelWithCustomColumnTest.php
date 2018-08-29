<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Tests\Core\Traits\HasLevelTest;

/**
 * HasLevel tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasLevelWithCustomColumnTest extends HasLevelTest
{
	/**
	 * Name of the column used to store level.
	 *
	 * @const
	 */
	const LEVEL_COLUMN = 'custom_level';
}
