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
 * Rule requirements.
 *
 * @since   1.0.0
 */
interface Rule
{
	/**
	 * Id of this rule.
	 *
	 * @return  string
	 */
	public function id();

	/**
	 * Name of this rule.
	 *
	 * @return  string
	 */
	public function name();

	/**
	 * Check if a value is valid.
	 *
	 * @param   mixed  $value  Value to check
	 *
	 * @return  boolean
	 */
	public function passes($value);

	/**
	 * Check if a value is not valid.
	 *
	 * @param   mixed  $value  Value to check
	 *
	 * @return  boolean
	 */
	public function fails($value);
}
