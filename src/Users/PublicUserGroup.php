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
 * User Group entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class PublicUserGroup extends UserGroup
{
	use HasSingleton;

	/**
	 * Parent group identifier.
	 *
	 * @const
	 */
	const PARENT_ID = 0;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$group = UserGroup::loadFromData(
			[
				'parent_id' => self::PARENT_ID
			]
		);

		if (!$group->isLoaded())
		{
			throw new \RuntimeException('Cannot find Public users user group');
		}

		$class = get_called_class();
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

		$data = [
			'parent_id' => self::PARENT_ID
		];

		$query = $db->getQuery(true)
			->insert($db->qn('#__usergroups'))
			->columns(array_keys($data))
			->values(implode(',', array_values($data)));

		$db->setQuery($query);
		$db->execute();

		return self::instance();
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
			return self::instance();
		}
		catch (\Exception $e)
		{
			return self::create();
		}
	}
}
