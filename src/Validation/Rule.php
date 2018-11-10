<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Validation\Contracts\Validator as ValidatorContract;

/**
 * Entity validator.
 *
 * @since   1.0.0
 */
abstract class Rule
{
	/**
	 * Id of this rule.
	 *
	 * @var  string
	 */
	protected $id;

	/**
	 * Name of this rule.
	 *
	 * @var  string
	 */
	protected $name;

	/**
	 * Constructor
	 *
	 * @param   mixed  $name  Name of this rule
	 */
	public function __construct($name = null)
	{
		$this->name = $name;
	}

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

	/**
	 * Id of this rule.
	 *
	 * @return  string
	 */
	public function id()
	{
		if (null === $this->id)
		{
			$this->id = spl_object_hash($this);
		}

		return $this->id;
	}

	/**
	 * Name of this rule.
	 *
	 * @return  string
	 */
	public function name()
	{
		return null === $this->name ? get_class($this) : \JText::_($this->name);
	}
}
