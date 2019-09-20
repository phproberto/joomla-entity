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
use Phproberto\Joomla\Entity\Users\UserGroup;
use Phproberto\Joomla\Entity\Users\Traits\HasUsers;
use Phproberto\Joomla\Entity\Core\Traits\HasSingleton;

/**
 * PredefinedUserGroup entity.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class PredefinedUserGroup extends UserGroup
{
	/**
	 * Predefined data to load the group.
	 *
	 * @return  array
	 */
	abstract public static function predefinedData();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$data = static::predefinedData();

		$group = UserGroup::loadFromData($data);

		if (!$group->isLoaded())
		{
			throw new \RuntimeException('Cannot find user group: ' . json_encode($data));
		}

		$this->id = $group->id();
		$this->bind($group->all());
	}

	/**
	 * Create the user group.
	 *
	 * @param   array|\stdClass  $data  Data to store
	 *
	 * @return  static
	 */
	public static function create($data = null)
	{
		$group = new UserGroup;

		$db = $group->getDbo();

		$data = static::predefinedData();

		$columns = array_keys($data);
		$values = array_map([$db, 'q'], array_values($data));

		$query = $db->getQuery(true)
			->insert($db->qn('#__usergroups'))
			->columns($columns)
			->values(implode(',', $values));

		$db->setQuery($query);
		$db->execute();

		return static::instance();
	}

	/**
	 * Retrieve the cached instance.
	 *
	 * @return  static
	 */
	public function instance()
	{
		$group = new static;

		return static::find($group->id());
	}

	/**
	 * Retrieve the group if exists or create it on the fly.
	 *
	 * @return  static
	 *
	 * @throws  \RuntimeException
	 */
	public function instanceOrCreate()
	{
		try
		{
			return static::instance();
		}
		catch (\Exception $e)
		{
			return static::create();
		}
	}
}
