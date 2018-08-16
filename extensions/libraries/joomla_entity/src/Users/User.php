<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Users\ViewLevel;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Core\Traits\HasParams;
use Phproberto\Joomla\Entity\Users\Traits\HasUserGroups;
use Phproberto\Joomla\Entity\Users\Traits\HasViewLevels;

/**
 * User entity.
 *
 * @since   1.0.0
 */
class User extends ComponentEntity implements Aclable
{
	use HasAcl, HasParams, HasUserGroups, HasViewLevels;

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

		return $userId ? static::find($userId) : new static;
	}

	/**
	 * Add this user to an UserGroup.
	 *
	 * @param   int  $userGroupId  User group to add the user to
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addToUserGroup(int $userGroupId)
	{
		return $this->addToUserGroups([$userGroupId]);
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addToUserGroups(array $userGroupsIds)
	{
		$userGroupsIds = array_unique(
			array_filter(
				ArrayHelper::toInteger($userGroupsIds)
			)
		);

		$currentIds = $this->userGroupsIds();
		$newIds = array_diff($userGroupsIds, $currentIds);

		if (!$newIds)
		{
			return;
		}

		$groupsIds = array_unique(
			array_filter(
				array_merge($userGroupsIds, $currentIds)
			)
		);

		$this->assign('groups', $groupsIds);
		$this->save();
		$this->clearUserGroups();
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
	 * Load associated user groups from DB.
	 *
	 * @return  Collection
	 */
	protected function loadUserGroups()
	{
		$userGroups = new Collection;

		if (!$this->hasId())
		{
			return $userGroups;
		}

		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('ug.*')
			->from($db->qn('#__usergroups', 'ug'))
			->innerJoin(
				$db->qn('#__user_usergroup_map', 'ugm')
				. ' ON ' . $db->qn('ugm.group_id') . ' = ' . $db->qn('ug.id')
			)
			->where($db->qn('ugm.user_id') . ' = ' . (int) $this->id);

		$db->setQuery($query);

		$items = $db->loadObjectList() ?: array();

		foreach ($items as $item)
		{
			$userGroup = UserGroup::find($item->id)->bind($item);

			$userGroups->add($userGroup);
		}

		return $userGroups;
	}

	/**
	 * Load associated view levels.
	 *
	 * @return  Collection
	 *
	 * @since   1.2.0
	 */
	protected function loadViewLevels()
	{
		$viewLevels = new Collection;

		foreach ($this->getAuthorisedViewLevels() as $id)
		{
			$viewLevels->add(ViewLevel::find($id));
		}

		return $viewLevels;
	}

	/**
	 * Remove this user from an UserGroup.
	 *
	 * @param   int  $userGroupId  ID of the UserGroup to remove
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function removeFromUserGroup(int $userGroupId)
	{
		return $this->removeFromUserGroups([$userGroupId]);
	}

	/**
	 * Remove this user from an user group.
	 *
	 * @param   int     $userGroupId  [description]
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function removeFromUserGroups(array $userGroupsIds)
	{
		$userGroupsIds = array_unique(
			array_filter(
				ArrayHelper::toInteger($userGroupsIds)
			)
		);

		$currentIds = $this->userGroupsIds();

		$removableIds = array_intersect($currentIds, $userGroupsIds);

		if (!$removableIds)
		{
			return;
		}

		$this->assign('groups', array_diff($currentIds, $removableIds));
		$this->save();
		$this->clearUserGroups();
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

	/**
	 * Get an array of user groups associated to this user.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function userGroupsIds()
	{
		return array_values(
			array_unique(
				array_filter(
					ArrayHelper::toInteger((array) $this->get('groups'))
				)
			)
		);
	}
}
