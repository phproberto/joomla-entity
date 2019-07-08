<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Acl\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Acl\Acl;

/**
 * Trait for entities with ACL.
 *
 * @since   1.0.0
 */
trait HasAcl
{
	/**
	 * Acl instance.
	 *
	 * @param   User|null  $user  User to check ACL against.
	 *
	 * @return  Acl
	 */
	public function acl(User $user = null)
	{
		return new Acl($this, $user);
	}

	/**
	 * Get the ACL prefix applied to this entity
	 *
	 * @return  string
	 */
	public function aclPrefix()
	{
		return 'core';
	}

	/**
	 * Get the identifier of the associated asset
	 *
	 * @return  string
	 */
	public function aclAssetName()
	{
		if ($this->hasId())
		{
			return $this->component()->option() . '.' . $this->name() . '.' . $this->id();
		}

		return $this->component()->option();
	}
}
