<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Validation\Traits;

use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Tests\Unit\Validation\Traits\Stubs\EntityWithValidation;

/**
 * HasValidation trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasValidationTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * isValid returns validator isValid.
	 *
	 * @return  void
	 */
	public function testIsValidReturnsValidatorIsValid()
	{
		$validator = $this->getMockBuilder('MockedValidator')
			->setMethods(array('isValid'))
			->getMock();

		$validator->method('isValid')
			->will($this->onConsecutiveCalls(false, true));

		$entity = $this->getMockBuilder(EntityWithValidation::class)
			->setMethods(array('validator'))
			->getMock();

		$entity->method('validator')
			->willReturn($validator);

		$this->assertFalse($entity->isValid());
		$this->assertTrue($entity->isValid());
	}

	/**
	 * setValidator sets validator.
	 *
	 * @return  void
	 */
	public function testSetValidatorSetsValidator()
	{
		$entity = new EntityWithValidation(999);

		$reflection = new \ReflectionClass($entity);
		$validatorProperty = $reflection->getProperty('validator');
		$validatorProperty->setAccessible(true);

		$this->assertSame(null, $validatorProperty->getValue($entity));

		$validator = new Validator($entity);
		$entity->setValidator($validator);

		$reflection = new \ReflectionClass($entity);
		$validatorProperty = $reflection->getProperty('validator');
		$validatorProperty->setAccessible(true);

		$this->assertSame($validator, $validatorProperty->getValue($entity));
	}

	/**
	 * validator returns correct value.
	 *
	 * @return  void
	 */
	public function testValidatorReturnsCorrectValue()
	{
		$entity = new EntityWithValidation(999);

		$customValidator = new Validator($entity);
		$this->assertEquals(new Validator($entity), $entity->validator());

		$reflection = new \ReflectionClass($entity);
		$validatorProperty = $reflection->getProperty('validator');
		$validatorProperty->setAccessible(true);
		$validatorProperty->setValue($entity, $customValidator);

		$this->assertSame($customValidator, $entity->validator());
	}

	/**
	 * validate returns validator validate.
	 *
	 * @return  void
	 */
	public function testValidateReturnsValidatorValidate()
	{
		$validator = $this->getMockBuilder('MockedValidator')
			->setMethods(array('validate'))
			->getMock();

		$validator->method('validate')
			->will($this->onConsecutiveCalls(false, true));

		$entity = $this->getMockBuilder(EntityWithValidation::class)
			->setMethods(array('validator'))
			->getMock();

		$entity->method('validator')
			->willReturn($validator);

		$this->assertFalse($entity->validate());
		$this->assertTrue($entity->validate());
	}
}
