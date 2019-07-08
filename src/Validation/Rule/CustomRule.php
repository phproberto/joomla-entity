<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation\Rule;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Validation\Rule;
use Phproberto\Joomla\Entity\Validation\Contracts\Rule as RuleContract;

/**
 * Custom validation rule.
 *
 * @since   1.0.0
 */
class CustomRule extends Rule implements RuleContract
{
	/**
	 * String to search for.
	 *
	 * @var  callable
	 */
	protected $validator;

	/**
	 * Constructor
	 *
	 * @param   callable  $validator  Closure to execute
	 * @param   mixed     $name       Name of this rule
	 */
	public function __construct(callable $validator, $name = null)
	{
		parent::__construct($name);

		$this->validator = $validator;
	}

	/**
	 * Check if a value is valid.
	 *
	 * @param   mixed  $value  Value to check
	 *
	 * @return  boolean
	 */
	public function passes($value)
	{
		return call_user_func_array($this->validator, array($value));
	}
}
