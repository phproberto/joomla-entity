<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs\EntityWithOwner;

/**
 * HasOwner trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasOwnerWithCustomColumnTest extends HasOwnerTest
{
	/**
	 * Name of the owner column.
	 *
	 * @const
	 */
	const OWNER_COLUMN = 'owner_id';
}
