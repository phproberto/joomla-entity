<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State\Filter;

defined('_JEXEC') || die;

/**
 * IntegerBoolean filter. Boolean in binary state: 0 (false) | 1 (true).
 *
 * @since  __DEPLOY_VERSION__
 */
class IntegerBoolean extends Boolean
{
	/**
	 * Determine if a value will be used or not.
	 *
	 * @param   mixed  $value  Value to filter
	 *
	 * @return  boolean
	 */
	protected function filterValue($value)
	{
		return in_array($value, [0,1], true);
	}

	/**
	 * Prepare a value before applying filter.
	 *
	 * @param   mixed  $value  Value to prepare
	 *
	 * @return  mixed
	 */
	protected function prepareValue($value)
	{
		$value = parent::prepareValue($value);

		return null === $value ? null : (int) $value;
	}
}
