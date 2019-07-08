<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\Traits\HasUserGroups;

/**
 * Sample class to test HasUserGroups traits.
 *
 * @since  1.1.0
 *
 * @codeCoverageIgnore
 */
class EntityWithUserGroups extends Entity
{
	use HasUserGroups;

	/**
	 * Expected loadUserGroups result.
	 *
	 * @var  Collection
	 */
	public $loadableUserGroups;

	/**
	 * Load associated user groups from DB.
	 *
	 * @return  Collection
	 */
	protected function loadUserGroups()
	{
		return null === $this->loadableUserGroups ? new Collection : $this->loadableUserGroups;
	}
}
