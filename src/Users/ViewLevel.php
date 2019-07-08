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
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Users\Traits\HasUserGroups;

/**
 * ViewLevel entity.
 *
 * @since   1.2.0
 */
class ViewLevel extends ComponentEntity
{
	use HasUserGroups;

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
		$name   = $name ?: 'ViewLevel';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}

	/**
	 * Load associated user groups from DB.
	 *
	 * @return  Collection
	 */
	protected function loadUserGroups()
	{
		$userGroups = new Collection;

		if (!$this->has('rules'))
		{
			return $userGroups;
		}

		$rules = $this->get('rules');
		$ids = array_unique(
			array_filter(
				empty($rules) ? [] : json_decode($rules)
			)
		);

		foreach ($ids as $id)
		{
			$userGroups->add(UserGroup::find($id));
		}

		return $userGroups;
	}
}
