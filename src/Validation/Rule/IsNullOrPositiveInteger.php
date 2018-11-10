<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation\Rule;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Validation\Rule;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;
use Phproberto\Joomla\Entity\Validation\Contracts\Rule as RuleContract;

/**
 * Check that value is null or a positve integer.
 *
 * @since   1.7.0
 */
class IsNullOrPositiveInteger extends Rule implements RuleContract
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
		$isNullRule = new IsNull($this->name);

		if ($isNullRule->passes($value))
		{
			return true;
		}

		$isPositiveIntegerRule = new IsPositiveInteger($this->name);

		return $isPositiveIntegerRule->passes($value);
	}
}
