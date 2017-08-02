<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Traits as EntityTraits;

/**
 * User entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class User extends Entity
{
	use EntityTraits\HasParams;

	/**
	 * Get the active joomla user.
	 *
	 * @return  static
	 */
	public static function active()
	{
		$userId = (int) \JFactory::getUser()->get('id');

		return $userId ? static::instance($userId) : new static;
	}

	/**
	 * Check if current user has been activated.
	 *
	 * @return  boolean
	 */
	public function isActivated()
	{
		if (!$this->hasId())
		{
			return false;
		}

		return in_array($this->get('activation'), array('', '0'));
	}

	/**
	 * Check if this user is active.
	 *
	 * @return  boolean
	 */
	public function isActive()
	{
		return !$this->isBlocked() && $this->isActivated();
	}

	/**
	 * Check if this user is blocked.
	 *
	 * @return  boolean
	 */
	public function isBlocked()
	{
		if (!$this->hasId())
		{
			return false;
		}

		return 1 === (int) $this->get('block');
	}

	/**
	 * Is this user a guest?
	 *
	 * @return  boolean
	 */
	public function isGuest()
	{
		if (!$this->hasId())
		{
			return true;
		}

		return 1 === (int) $this->joomlaUser()->get('guest');
	}

	/**
	 * \JFactory::getUser() proxy for testing purposes
	 *
	 * @return  \JUser object
	 */
	public function joomlaUser()
	{
		if (!$this->hasId())
		{
			throw new \InvalidArgumentException("Error trying to load non-existing user");
		}

		$joomlaUser = $this->juser($this->id());

		if ((int) $joomlaUser->get('id') !== $this->id())
		{
			throw new \RuntimeException(sprintf("User (id: `%s`) does not exist", $this->id()));
		}

		return $joomlaUser;
	}

	/**
	 * Get a table instance. Defauts to \JTableUser.
	 *
	 * @param   string  $name     Table name. Optional.
	 * @param   string  $prefix   Class prefix. Optional.
	 * @param   array   $options  Configuration array for the table. Optional.
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$name   = $name ?: 'User';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}
}
