<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Validation\Exception;

use Phproberto\Joomla\Entity\Validation\Rule;
use Phproberto\Joomla\Entity\Tests\Unit\Stubs\Entity;
use Phproberto\Joomla\Entity\Exception\SaveException;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * SaveException tests.
 *
 * @since   1.1.0
 */
class SaveExceptionTest extends \TestCase
{
	/**
	 * table returns SaveException.
	 *
	 * @return  void
	 */
	public function testTableReturnsSaveException()
	{
		$entity = new Entity(999);

		$table = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('getError'))
			->getMock();

		$table->method('getError')
			->willReturn('Save failed');

		$exception = SaveException::table($entity, $table);

		$this->assertInstanceOf(SaveException::class, $exception);
		$this->assertTrue(strlen($exception->getMessage()) > 0);
	}

	/**
	 * validation returns SaveException.
	 *
	 * @return  void
	 */
	public function testValidationReturnsSaveException()
	{
		$entity = new Entity(999);
		$validationException = new ValidationException('Something went wrong');

		$exception = SaveException::validation($entity, $validationException);

		$this->assertInstanceOf(SaveException::class, $exception);
		$this->assertTrue(strlen($exception->getMessage()) > 0);

		$entity = new Entity;
		$validationException = new ValidationException('Something went wrong');

		$exception = SaveException::validation($entity, $validationException);

		$this->assertInstanceOf(SaveException::class, $exception);
		$this->assertTrue(strlen($exception->getMessage()) > 0);
	}
}
