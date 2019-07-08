<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Exception;

use Phproberto\Joomla\Entity\Tests\Stubs\Entity;
use Phproberto\Joomla\Entity\Validation\Rule;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * ValidationException tests.
 *
 * @since   1.1.0
 */
class ValidationExceptionTest extends \TestCase
{
	/**
	 * invalidEntity returns ValidationException.
	 *
	 * @return  void
	 */
	public function testInvalidEntityReturnsValidationException()
	{
		$entity = new Entity(999);
		$errors = array(
			'`alias` cannot be empty',
			'`column` is wrong'
		);

		$exception = ValidationException::invalidEntity($entity, $errors);

		$this->assertInstanceOf(ValidationException::class, $exception);
		$this->assertTrue(strlen($exception->getMessage()) > 0);
	}

	/**
	 * invalidColumn returns ValidationException.
	 *
	 * @return  void
	 */
	public function testInvalidColumnReturnsValidationException()
	{
		$entity = new Entity(999);
		$failedRules = array(
			new Rule\IsPositiveInteger('Is not a positive integer'),
			new Rule\IsNotEmptyString('Cannot be an empty string')
		);

		$exception = ValidationException::invalidColumn('sample_column', $failedRules);

		$this->assertInstanceOf(ValidationException::class, $exception);
		$this->assertTrue(strlen($exception->getMessage()) > 0);
	}
}
