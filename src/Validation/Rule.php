<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Validation\Contracts\Validator as ValidatorContract;

/**
 * Entity validator.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class Rule
{
	/**
	 * Check if a value is not valid.
	 *
	 * @param   mixed  $value  Value to check
	 *
	 * @return  boolean
	 */
	public function fails($value)
	{
		return !$this->passes($value);
	}
}
