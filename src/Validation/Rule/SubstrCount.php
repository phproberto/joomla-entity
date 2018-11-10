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
use Phproberto\Joomla\Entity\Validation\Contracts\Rule as RuleContract;

/**
 * Check that a string is present in another string.
 *
 * @since   1.0.0
 */
class SubstrCount extends Rule implements RuleContract
{
	/**
	 * String to search for.
	 *
	 * @var  string
	 */
	protected $substr;

	/**
	 * Constructor
	 *
	 * @param   string  $substr  String to search for
	 * @param   mixed   $name    Name of this rule
	 */
	public function __construct($substr, $name = null)
	{
		parent::__construct($name);

		$this->substr = $substr;
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
		return substr_count($value, $this->substr) > 0;
	}
}
