<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;
use Phproberto\Joomla\Entity\Validation\Contracts\Validator as ValidatorContract;

/**
 * Trait for entities with validation.
 *
 * @since   1.0.0
 */
trait HasValidation
{
	/**
	 * Associated validator.
	 *
	 * @var  ValidatorContract
	 */
	protected $validator;

	/**
	 * Check if this entity is valid.
	 *
	 * @return  boolean
	 */
	public function isValid()
	{
		return $this->validator()->isValid();
	}

	/**
	 * Set validator.
	 *
	 * @param   ValidatorContract  $validator  Validator to use
	 *
	 * @return  self
	 */
	public function setValidator(ValidatorContract $validator)
	{
		$this->validator = $validator;

		return $this;
	}

	/**
	 * Retrieve entity validator.
	 *
	 * @return  ValidatorContract
	 */
	public function validator()
	{
		if (null === $this->validator)
		{
			$this->validator = new Validator($this);
		}

		return $this->validator;
	}

	/**
	 * Validate this entity.
	 *
	 * @return  boolean
	 *
	 * @throws  ValidationException
	 */
	public function validate()
	{
		return $this->validator()->validate();
	}
}
