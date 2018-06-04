<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Fields;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\ComponentEntity;
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
	 * @since    __DEPLOY_VERSION__
	 */
	public function fieldName()
	{
		return $this->get('name');
	}

	/**
	 * Field value getter
	 *
	 * @return   string
	 *
	 * @since    __DEPLOY_VERSION__
	 */
	public function value()
	{
		return $this->get('value');
	}

	/**
	 * Field raw value getter
	 *
	 * @return   string
	 *
	 * @since    __DEPLOY_VERSION__
	 */
	public function rawValue()
	{
		return $this->get('rawvalue');
	}
}
