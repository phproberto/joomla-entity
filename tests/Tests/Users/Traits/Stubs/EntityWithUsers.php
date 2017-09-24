<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\Traits\HasUsers;

/**
 * Sample class to test HasUsers traits.
 *
 * @since  __DEPLOY_VERSION__
 *
 * @codeCoverageIgnore
 */
class EntityWithUsers extends Entity
{
	use HasUsers;

	/**
	 * Expected loadUsers result.
	 *
	 * @var  Collection
	 */
	public $loadableUsers;

	/**
	 * Load associated user groups from DB.
	 *
	 * @return  Collection
	 */
	protected function loadUsers()
	{
		return null === $this->loadableUserGroups ? new Collection : $this->loadableUserGroups;
	}
}
