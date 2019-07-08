<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation\Rule;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Validation\Rule\IsInteger;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;
use Phproberto\Joomla\Entity\Validation\Contracts\Rule as RuleContract;

/**
 * Check that value is zero or a positve integer.
 *
 * @since   1.7.0
 */
class IsZeroOrPositiveInteger extends IsInteger implements RuleContract
{
	/**
	 * Check if a value is valid.
	 *
	 * @param   mixed  $value  Value to check
	 *
	 * @return  boolean
	 */
	public function passes($value)
	{
		if (!parent::passes($value))
		{
			return false;
		}

		return  (int) $value > 0 || (int) $value === 0;
	}
}
