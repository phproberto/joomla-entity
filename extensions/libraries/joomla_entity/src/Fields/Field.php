<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Fields;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Fields\Column;
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Fields\FieldGroup;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Core\Contracts\Publishable;

/**
 * Field entity.
 *
 * @since   1.0.0
 */
class Field extends ComponentEntity implements Publishable
{
	use CoreTraits\HasParams, CoreTraits\HasState;

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
		$name = $name ?: 'Field';
		$prefix = $prefix ?: 'FieldsTable';

		if ($prefix === 'FieldsTable')
		{
			return $this->component()->table($name);
		}

		return parent::table($name, $prefix, $options);
	}

	/**
	 * Field name getter
	 *
	 * @return   string
	 *
	 * @since    1.1.0
	 */
	public function fieldName()
	{
		return $this->get('name');
	}

	/**
	 * Retrieve this field field group.
	 *
	 * @return  FieldGroup
	 *
	 * @since   1.2.0
	 *
	 * @throws  \InvalidArgumentException  Field group property not found
	 */
	public function fieldGroup()
	{
		return FieldGroup::find($this->get($this->columnAlias(Column::FIELD_GROUP)));
	}

	/**
	 * Check if this field has an associated field group.
	 *
	 * @return  boolean
	 *
	 * @since   1.2.0
	 */
	public function hasFieldGroup()
	{
		if (!$this->has($this->columnAlias(Column::FIELD_GROUP)))
		{
			return false;
		}

		return 0 !== (int) $this->get($this->columnAlias(Column::FIELD_GROUP));
	}

	/**
	 * Check if this field has a raw value assigned.
	 *
	 * @return  boolean
	 *
	 * @since   1.1.0
	 */
	public function hasRawValue()
	{
		return $this->has('rawvalue');
	}

	/**
	 * Check if this field has a value assigned.
	 *
	 * @return  boolean
	 *
	 * @since   1.1.0
	 */
	public function hasValue()
	{
		return $this->has('value');
	}

	/**
	 * Field raw value getter
	 *
	 * @return   string
	 *
	 * @since    1.1.0
	 */
	public function rawValue()
	{
		return $this->get('rawvalue');
	}

	/**
	 * Field value getter
	 *
	 * @return   string
	 *
	 * @since    1.1.0
	 */
	public function value()
	{
		return $this->get('value');
	}
}
