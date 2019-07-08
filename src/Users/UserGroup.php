<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Users\Traits\HasUsers;

/**
 * User Group entity.
 *
 * @since   1.0.0
 */
class UserGroup extends ComponentEntity
{
	use HasUsers;

	/**
	 * Load associated users from DB.
	 *
	 * @return  Collection
	 */
	protected function loadUsers()
	{
		if (!$this->hasId())
		{
			return new Collection;
		}

		$users = array_map(
			function ($item)
			{
				return User::find($item->id)->bind($item);
			},
			$this->usersModel()->getItems() ?: array()
		);

		return new Collection($users);
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
		$name   = $name ?: 'Usergroup';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}

	/**
	 * Get an instance of the users model.
	 *
	 * @return  \UsersModelUsersModel
	 */
	protected function usersModel()
	{
		\JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models', 'UsersModel');

		$model = \JModelLegacy::getInstance('Users', 'UsersModel', array('ignore_request' => true));

		if ($this->hasId())
		{
			$model->setState('filter.group_id', $this->id());
		}

		return $model;
	}
}
