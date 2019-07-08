<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\User\UserHelper;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Users\ViewLevel;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Core\Traits\HasParams;
use Phproberto\Joomla\Entity\Exception\SaveException;
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
	 * Active user.
	 *
	 * @var    static
	 * @since  1.6.0
	 */
	private static $active;

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
		if (null === static::$active)
		{
			$userId = (int) Factory::getUser()->get('id');

			static::$active = $userId ? static::find($userId) : new static;
		}

		return static::$active;
	}

	/**
	 * Add this user to an UserGroup.
	 *
	 * @param   int  $userGroupId  User group to add the user to
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	public function addToUserGroup(int $userGroupId)
	{
		$this->addToUserGroups([$userGroupId]);
	}

	/**
	 * Add this user to a list of UserGroups.
	 *
	 * @param   int[]  $userGroupsIds  An array of user groups identifiers
	 *
	 * @return  void
	 *
	 * @since   1.3.0
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
	 * Change this user password.
	 *
	 * @param   string  $newPassword  New password to assign
	 *
	 * @return  void
	 *
	 * @since   1.5.0
	 */
	public function changePassword($newPassword)
	{
		if (!$this->hasId())
		{
			throw new \RuntimeException("Trying to change password for unsaved user", 500);
		}

		$newPassword = trim($newPassword);

		if (empty($newPassword))
		{
			throw new \InvalidArgumentException("Cannot assign empty password to user");
		}

		$this->bind(
			[
				'password'     => UserHelper::hashPassword($newPassword),
				'raw_password' => $newPassword
			]
		);

		$this->save();
	}

	/**
	 * Clear the active user.
	 *
	 * @return  void
	 *
	 * @since   1.6.0
	 */
	public static function clearActive()
	{
		static::$active = null;
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
	 * Get the plugin types that will be used by this entity.
	 *
	 * @return  array
	 *
	 * @since   1.6.0
	 */
	protected function eventsPlugins()
	{
		return array_merge(parent::eventsPlugins(), ['user']);
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
	 * Load an entity from columns data.
	 *
	 * @param   array   $data  Data to load the entity
	 *
	 * @return  false|static
	 *
	 * @since   1.6.0
	 */
	public static function loadFromData(array $data)
	{
		$entity = new static;
		$db = $entity->getDbo();
		$table = $entity->table();

		$query = $db->getQuery(true)
			->select('*')
			->from($table->getTableName());

		$fields = array_keys($table->getProperties());

		foreach ($data as $field => $value)
		{
			if (!in_array($field, $fields))
			{
				throw new \UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($table), $field));
			}

			$query->where($db->qn($field) . ' = ' . $db->q($value));
		}

		$db->setQuery($query);

		$row = $db->loadAssoc();

		// Check that we have a result.
		if (empty($row))
		{
			return false;
		}

		return $entity->bind($row);
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
	 * Removes this user from all assigned user groups.
	 *
	 * @return  void
	 */
	public function removeFromAllUserGroups()
	{
		if ($this->hasId())
		{
			$db = $this->getDbo();

			$query = $db->getQuery(true)
				->delete('#__user_usergroup_map')
				->where($db->qn('user_id') . ' = ' . $this->id());

			$db->setQuery($query);
			$db->execute();
		}

		$this->assign('groups', []);
		$this->clearUserGroups();
	}

	/**
	 * Remove this user from an UserGroup.
	 *
	 * @param   int  $userGroupId  ID of the UserGroup to remove
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	public function removeFromUserGroup(int $userGroupId)
	{
		$this->removeFromUserGroups([$userGroupId]);
	}

	/**
	 * Remove this user from an user group.
	 *
	 * @param   int[]  $userGroupsIds  Array of user groups identifiers
	 *
	 * @return  void
	 *
	 * @since   1.3.0
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

		$newGroups = array_diff($currentIds, $removableIds);

		// Someone decided in Joomla that you cannot save a user without groups....
		if (!$newGroups)
		{
			$this->removeFromAllUserGroups();

			return;
		}

		$this->assign('groups', $newGroups);
		$this->clearUserGroups();
		$this->save();
	}

	/**
	 * Save entity to the database.
	 *
	 * @return  self
	 *
	 * @throws  SaveException
	 *
	 * @since   1.6.0
	 */
	public function save()
	{
		$isNew = !$this->hasId();

		$this->importPlugins();

		try
		{
			parent::save();
		}
		catch (\Exception $e)
		{
			$this->dispatcher()->trigger(
				'onUserAfterSave',
				[
					$this->all(), $isNew, false, $e->getMessage()
				]
			);

			throw $e;
		}

		$this->dispatcher()->trigger(
			'onUserAfterSave',
			[
				$this->all(), $isNew, true, ''
			]
		);

		return $this;
	}

	/**
	 * Set the active user.
	 *
	 * @param   static  $user  User that will be set as active
	 *
	 * @return  void
	 *
	 * @since   1.6.0
	 */
	public static function setActive(User $user)
	{
		static::$active = $user;
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
	 * @since   1.3.0
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
