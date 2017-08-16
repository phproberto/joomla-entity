<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Contracts;

use Phproberto\Joomla\Entity\Users\User;

defined('_JEXEC') || die;

/**
 * Describes methods required by entities with an owner.
 *
 * @since  __DEPLOY_VERSION__
 */
interface Ownerable
{
	/**
	 * Get the owner of this entity.
	 *
	 * @return  User
	 */
	public function owner();

	/**
	 * Check if this entit has an owner.
	 *
	 * @return  boolean
	 */
	public function hasOwner();

	/**
	 * Check if an user is this entity owner.
	 *
	 * @param   User  $user  User to check for ownership. Defaults to active user.
	 *
	 * @return  boolean
	 */
	public function isOwner(User $user = null);
}
