<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\EntityWithState;

/**
 * HasState trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasStateWithCustomColumnTest extends HasStateTest
{
	/**
	 * Column to use to load/store state.
	 *
	 * @const
	 */
	const COLUMN_STATE = 'state';
}
