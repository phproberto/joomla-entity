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
 * Filter for strings.
 *
 * @since  __DEPLOY_VERSION__
 */
class StringFilter extends BaseFilter
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
		return is_string($value);
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
