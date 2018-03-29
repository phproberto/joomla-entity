<?php
/**
 * Virtual storage for objects.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Phproberto\Joomla\Entity\Tests\Unit\Stubs\EntityWithCustomPrimaryKey;

/**
 * Entity test.
 *
 * @since   __DEPLOY_VERSION__
 */
class EntityWithCustomPrimaryKeyTest extends EntityTest
{
	/**
	 * Name of the primary key
	 *
	 * @const
	 */
	const PRIMARY_KEY = 'entity_id';
}
