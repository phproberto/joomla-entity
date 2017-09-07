<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Acl\Traits as AclTraits;
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;

/**
 * User entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class User extends ComponentEntity
{
	use AclTraits\HasAcl;
	use CoreTraits\HasParams;

	/**
	 * Is this user root/super user?
	 *
	 * @var  boolean
	 */
	protected $isRoot;

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
	 * Proxy to JUser::authorise().
	 *
	 * @param   string  $action     The name of the action to check for permission.
	 * @param   string  $assetname  The name of the asset on which to perform the action.
	 *
	 * @return  boolean
	 */
	public function authorise($action, $assetname = null)
	{
		if ($this->isRoot())
		{
			return true;
		}

		try
		{
			return $this->joomlaUser()->authorise($action, $assetname);
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Can this user administrate a component?
	 *
	 * @param   string  $component  Component to check for admin permission
	 *
	 * @return  boolean
	 */
	public function canAdmin($component)
	{
		if ($this->isRoot())
		{
			return true;
		}

		return $this->authorise('core.admin', $component);
	}

	/**
	 * Get the list of column aliases.
	 *
	 * @return  array
	 */
	public function columnAliases()
	{
		return array(
			Column::OWNER  => 'id'
		);
	}

	/**
	 * Get an array of the authorised access levels for this user.
	 *
	 * @return  array
	 */
	public function getAuthorisedViewLevels()
	{
		try
		{
			return array_values(
				array_unique(
					$this->joomlaUser()->getAuthorisedViewLevels()
				)
			);
		}
		catch (\Exception $e)
		{
			return array();
		}
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
	 * Check if this user is super user.
	 *
	 * @return  boolean
	 */
	public function isRoot()
	{
		if (null === $this->isRoot)
		{
			$this->isRoot = $this->joomlaUser()->authorise('core.admin');
		}

		return $this->isRoot;
	}

	/**
	 * \JFactory::getUser() proxy for testing purposes
	 *
	 * @return  \JUser object
	 */
	public function joomlaUser()
	{
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
