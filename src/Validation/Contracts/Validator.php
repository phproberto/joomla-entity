<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation\Contracts;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * Validator requirements.
 *
 * @since   1.0.0
 */
interface Validator
{
	/**
	 * Validate entity.
	 *
	 * @return  boolean
	 *
	 * @throws  ValidationException
	 */
	public function validate();

	/**
	 * Validate a column value.
	 *
	 * @param   string  $column  Column to check value against.
	 * @param   mixed   $value   Value for the column.
	 *
	 * @return  boolean
	 *
	 * @throws  ValidationException
	 */
	public function validateColumnValue($column, $value);

	/**
	 * Check if the entity is valid.
	 *
	 * @return  boolean
	 */
	public function isValid();

	/**
	 * Check if a value is valid for a specific column.
	 *
	 * @param   string  $column  Column to validate against
	 * @param   mixed   $value   Value to check
	 *
	 * @return  boolean
	 */
	public function isValidColumnValue($column, $value);
}
