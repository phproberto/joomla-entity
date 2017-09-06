<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule;
use Phproberto\Joomla\Entity\Validation\Contracts\Rule as RuleContract;

/**
 * Check that value is a positive integer.
 *
 * @since   __DEPLOY_VERSION__
 */
class PositiveInteger extends Rule implements RuleContract
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
		return is_numeric($value) && $value == (int) $value && (int) $value > 0;
	}
}
