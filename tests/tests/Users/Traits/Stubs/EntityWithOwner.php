<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\Traits\HasOwner;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;

/**
 * Sample class to test HasOwner trait.
 *
 * @since  1.1.0
 *
 * @codeCoverageIgnore
 */
class EntityWithOwner extends Entity implements Ownerable
{
	use HasOwner;

	/**
	 * Expected active user.
	 *
	 * @var  User
	 */
	public $activeUser;

	/**
	 * Retrieve active user.
	 *
	 * @return  USer
	 */
	private function activeUser()
	{
		return $this->activeUser ?: new User;
	}
}
