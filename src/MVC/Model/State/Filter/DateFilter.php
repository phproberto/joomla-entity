<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State\Filter;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;

/**
 * Filter for dates.
 *
 * @since  __DEPLOY_VERSION__
 */
class DateFilter extends StringQuoted
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
		$format = "/^'([0-9]{4})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])'/";

		if (!preg_match($format, $value, $matches))
		{
			return false;
		}

		// Extra year check
		return $matches[1] > 0;
	}

	/**
	 * Prepare value.
	 *
	 * @param   mixed  $value  Value to prepare
	 *
	 * @return  mixed
	 */
	public function prepareValue($value)
	{
		$value = parent::prepareValue($value);

		if (!is_string($value) && !is_numeric($value))
		{
			return null;
		}

		return (string) $value;
	}
}
