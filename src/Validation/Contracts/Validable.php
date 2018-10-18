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
 * Validable entities requirements.
 *
 * @since   1.0.0
 */
interface Validable
{
	/**
	 * Check if this entity is valid.
	 *
	 * @return  boolean
	 */
	public function isValid();

	/**
	 * Validate this entity.
	 *
	 * @return  boolean
	 *
	 * @throws  ValidationException
	 */
	public function validate();
}
