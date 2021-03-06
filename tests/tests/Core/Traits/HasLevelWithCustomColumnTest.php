<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Tests\Core\Traits\HasLevelTest;

/**
 * HasLevel tests.
 *
 * @since   1.4.0
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
