<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Fields;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Fields\Column;
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Fields\Traits\HasFields;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Core\Contracts\Publishable;

/**
 * Field Group entity.
 *
 * @since   1.2.0
 */
class FieldGroup extends ComponentEntity implements Publishable
{
	use CoreTraits\HasParams, CoreTraits\HasState, HasFields;

	/**
	 * Load associated fields from DB.
	 *
	 * @return  Collection
	 */
	protected function loadFields()
	{
		$fields = new Collection;

		if (!$this->hasId())
		{
			return $fields;
		}

		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('f.*')
			->from($db->qn('#__fields', 'f'))
			->where($db->qn('f.group_id') . ' = ' . (int) $this->id());

		$db->setQuery($query);

		$items = $db->loadObjectList() ?: [];

		foreach ($items as $item)
		{
			$field = Field::find($item->id)->bind($item);

			$fields->add($field);
		}

		return $fields;
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
		$name = $name ?: 'Group';
		$prefix = $prefix ?: 'FieldsTable';

		if ($prefix === 'FieldsTable')
		{
			return $this->component()->table($name);
		}

		return parent::table($name, $prefix, $options);
	}
}
