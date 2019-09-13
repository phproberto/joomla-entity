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
 * Boolean values filterer.
 *
 * @since  __DEPLOY_VERSION__
 */
class Boolean extends BaseFilter
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
		return in_array($value, [true, false], true);
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

		if (!in_array($value, [1, 0,'1', '0', true, false, 'true', 'false'], true))
		{
			return null;
		}

		return 'true' === $value ? true : ('false' === $value ? false : (bool) $value);
	}
}
