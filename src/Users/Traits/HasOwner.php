<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\Column;

/**
 * Trait for entities with an owner.
 *
 * @since   1.0.0
 */
trait HasOwner
{
	/**
	 * Onwer of this entity.
	 *
	 * @var  User
	 */
	protected $owner;

	/**
	 * Get the owner of this entity.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  User
	 */
	public function owner($reload = false)
	{
		if ($reload || null === $this->owner)
		{
			$this->owner = $this->loadOwner();
		}

		return $this->owner;
	}

	/**
	 * Check if this entity has an owner.
	 *
	 * @return  boolean
	 */
	public function hasOwner()
	{
		if (!$this->has($this->columnAlias(Column::OWNER)))
		{
			return false;
		}

		return 0 !== (int) $this->get($this->columnAlias(Column::OWNER));
	}

	/**
	 * Check if an user is this entity owner.
	 *
	 * @param   User  $user  User to check for ownership. Defaults to active user.
	 *
	 * @return  boolean
	 */
	public function isOwner(User $user = null)
	{
		$user = $user ?: User::active();

		if ($user->isGuest() || !$this->hasOwner())
		{
			return false;
		}

		return $this->owner()->id() === $user->id();
	}

	/**
	 * Load owner from DB.
	 *
	 * @return  User
	 *
	 * @throws  \InvalidArgumentException
	 */
	protected function loadOwner()
	{
		$ownerId = (int) $this->get($this->columnAlias(Column::OWNER));

		if (!$ownerId)
		{
			$msg = sprintf('Entity %s does not have an owner', get_class($this));

			throw new \InvalidArgumentException($msg);
		}

		return User::find($ownerId);
	}
}
