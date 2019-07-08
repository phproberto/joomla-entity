<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

defined('_JEXEC') || die;

/**
 * Trait for entities that have associated users.
 *
 * @since  1.0.0
 */
trait HasUsers
{
	/**
	 * Associated users.
	 *
	 * @var  Collection
	 */
	protected $users;

	/**
	 * Clear already loaded users.
	 *
	 * @return  self
	 */
	public function clearUsers()
	{
		$this->users = null;

		return $this;
	}

	/**
	 * Get the associated users.
	 *
	 * @return  Collection
	 */
	public function users()
	{
		if (null === $this->users)
		{
			$this->users = $this->loadUsers();
		}

		return $this->users;
	}

	/**
	 * Check if this entity has an associated user.
	 *
	 * @param   integer   $id  User identifier
	 *
	 * @return  boolean
	 */
	public function hasUser($id)
	{
		return $this->users()->has($id);
	}

	/**
	 * Check if this entity has associated users.
	 *
	 * @return  boolean
	 */
	public function hasUsers()
	{
		return !$this->users()->isEmpty();
	}

	/**
	 * Load associated users from DB.
	 *
	 * @return  Collection
	 */
	abstract protected function loadUsers();
}
