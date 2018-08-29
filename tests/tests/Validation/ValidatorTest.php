<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation;

use Phproberto\Joomla\Entity\Tests\Stubs\Entity;
use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Validation\Rule\CustomRule;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * Validator tests.
 *
 * @since   1.1.0
 */
class ValidatorTest extends \TestCase
{
	/**
	 * addGlobalRule adds rule.
	 *
	 * @return  void
	 */
	public function testAddGlobalRuleAddsRule()
	{
		$validator = new Validator(new Entity);

		$rule = new CustomRule(
			function ($value)
			{
				return $value !== 'test';
			},
			'Custom rule test'
		);

		$validator->addGlobalRule($rule);

		$reflection = new \ReflectionClass($validator);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);

		$expected = array(
			$rule->id() => $rule
		);

		$this->assertSame($expected, $globalRulesProperty->getValue($validator));

		$rule2 = new CustomRule(
			function ($value)
			{
				return $value !== 'test2';
			}
		);

		$validator->addGlobalRule($rule2);

		$expected = array(
			$rule->id()  => $rule,
			$rule2->id() => $rule2
		);

		$this->assertSame($expected, $globalRulesProperty->getValue($validator));
	}

	/**
	 * addGlobalRules adds rules.
	 *
	 * @return  void
	 */
	public function testAddGlobalRulesAddsRules()
	{
		$validator = new Validator(new Entity);

		$rules = array(
			new CustomRule(
				function ($value)
				{
					return $value !== 'test';
				},
				'Custom rule test'
			),
			new CustomRule(
				function ($value)
				{
					return $value !== 'test2';
				},
				'Custom rule test2'
			)
		);

		$validator->addGlobalRules($rules);

		$reflection = new \ReflectionClass($validator);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);

		$expected = array(
			$rules[0]->id() => $rules[0],
			$rules[1]->id() => $rules[1]
		);

		$this->assertSame($expected, $globalRulesProperty->getValue($validator));
	}

	/**
	 * addRule adds column rule.
	 *
	 * @return  void
	 */
	public function testAddsRuleAddsColumnRule()
	{
		$validator = new Validator(new Entity);

		$rule = new CustomRule(
			function ($value)
			{
				return $value !== 'test';
			}
		);

		$validator->addRule($rule, 'sample_column');

		$reflection = new \ReflectionClass($validator);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);

		$expected = array(
			'sample_column' => array(
				$rule->id() => $rule
			)
		);

		$this->assertSame($expected, $rulesProperty->getValue($validator));

		$rule2 = new CustomRule(
			function ($value)
			{
				return $value !== 'test2';
			}
		);

		$validator->addRule($rule2, 'sample_column2', 'Rule name');

		$expected = array(
			'sample_column' => array(
				$rule->id() => $rule
			),
			'sample_column2' => array(
				$rule2->id() => $rule2
			)
		);

		$this->assertSame($expected, $rulesProperty->getValue($validator));
	}

	/**
	 * addRules adds rules.
	 *
	 * @return  void
	 */
	public function testAddRulesAddsRules()
	{
		$validator = new Validator(new Entity);

		$rules = array(
			'sample_column' => new CustomRule(
				function ($value)
				{
					return $value !== 'test';
				}
			),
			'sample_column2' => array(
				new CustomRule(
					function ($value)
					{
						return $value !== 'test2';
					}
				),
				new CustomRule(
					function ($value)
					{
						return $value !== 'test3';
					}
				)
			)
		);

		$validator->addRules($rules);

		$reflection = new \ReflectionClass($validator);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);

		$expected = array(
			'sample_column' => array(
				$rules['sample_column']->id() => $rules['sample_column']
			),
			'sample_column2' => array(
				$rules['sample_column2'][0]->id() => $rules['sample_column2'][0],
				$rules['sample_column2'][1]->id() => $rules['sample_column2'][1]
			)
		);

		$this->assertSame($expected, $rulesProperty->getValue($validator));
	}

	/**
	 * globalRules returns property value.
	 *
	 * @return  void
	 */
	public function testGlobalRulesReturnsPropertyValue()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			CustomRule::class => new CustomRule(
				function ($value)
				{
					return $value === 'test1';
				}
			),
			CustomRule::class => new CustomRule(
				function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($validator, $rules);

		$this->assertSame($rules, $validator->globalRules());
	}

	/**
	 * hasGlobalRule returns correct value.
	 *
	 * @return  void
	 */
	public function testHasGlobalRuleReturnsCorrectValue()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'test' => new CustomRule(
				function ($value)
				{
					return $value === 'test1';
				}
			),
			'test two' => new CustomRule(
				function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($validator, $rules);

		$this->assertTrue($validator->hasGlobalRule('test'));
		$this->assertFalse($validator->hasGlobalRule('test1'));
		$this->assertTrue($validator->hasGlobalRule('test two'));
	}

	/**
	 * hasGlobalRules returns correct value.
	 *
	 * @return  void
	 */
	public function testHasGlobalRulesReturnsCorrectValue()
	{
		$validator = new Validator(new Entity);

		$this->assertFalse($validator->hasGlobalRules());

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'test' => new CustomRule(
				function ($value)
				{
					return $value === 'test1';
				}
			),
			'test two' => new CustomRule(
				function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($validator, $rules);

		$this->assertTrue($validator->hasGlobalRules());

		$globalRulesProperty->setValue($validator, array());

		$this->assertFalse($validator->hasGlobalRules());
	}

	/**
	 * hasRule returns correct value.
	 *
	 * @return  void
	 */
	public function testHasRuleReturnsCorrectValue()
	{
		$validator = new Validator(new Entity);

		$this->assertFalse($validator->hasRule('sample rule', 'sample_column'));

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'sample_column' => array(
				'test' => new CustomRule(
					function ($value)
					{
						return $value === 'test1';
					}
				)
			),
			'sample_column2' => array(
				'test two' => new CustomRule(
					function ($value)
					{
						return $value === 'test2';
					}
				)
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($validator, $rules);

		$this->assertTrue($validator->hasRule('test', 'sample_column'));
		$this->assertFalse($validator->hasRule('test two', 'sample_column'));
		$this->assertTrue($validator->hasRule('test two', 'sample_column2'));
	}

	/**
	 * hasRules returns correct value.
	 *
	 * @return  void
	 */
	public function testHasRulesReturnsCorrectValue()
	{
		$validator = new Validator(new Entity);

		$this->assertFalse($validator->hasRules());

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'sample_column' => array(
				'test' => new CustomRule(
					function ($value)
					{
						return $value === 'test1';
					}
				)
			),
			'sample_column2' => array(
				'test two' => new CustomRule(
					function ($value)
					{
						return $value === 'test2';
					}
				)
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($validator, $rules);

		$this->assertTrue($validator->hasRules());

		$rulesProperty->setValue($validator, array());

		$this->assertFalse($validator->hasRules());
	}

	/**
	 * isValid returns false when exception happens.
	 *
	 * @return  void
	 */
	public function testIsValidReturnsFalseWhenExceptionHappens()
	{
		$validator = $this->getMockBuilder(Validator::class)
			->disableOriginalConstructor()
			->setMethods(array('validate'))
			->getMock();

		$validator->expects($this->once())
			->method('validate')
			->will($this->throwException(new ValidationException('Validation failure')));

		$this->assertFalse($validator->isValid());
	}

	/**
	 * isValid returns true when no exception happens.
	 *
	 * @return  void
	 */
	public function testIsValidReturnsTrueWhenNoExceptionHappens()
	{
		$validator = $this->getMockBuilder(Validator::class)
			->disableOriginalConstructor()
			->setMethods(array('validate'))
			->getMock();

		$validator->expects($this->once())
			->method('validate')
			->willReturn(true);

		$this->assertTrue($validator->isValid());
	}

	/**
	 * isValidColumnValue returns false when global rule returns false.
	 *
	 * @return  void
	 */
	public function testIsValidColumnValueReturnsFalseWhenGlobalRuleReturnsFalse()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'test' => new CustomRule(
				function ($value)
				{
					return $value !== 'test1';
				}
			),
			'test two' => new CustomRule(
				function ($value)
				{
					return $value !== 'test2';
				}
			)
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($validator, $rules);

		$this->assertFalse($validator->isValidColumnValue('property', 'test1'));
		$this->assertFalse($validator->isValidColumnValue('property', 'test2'));
		$this->assertTrue($validator->isValidColumnValue('property', 'test3'));
	}

	/**
	 * isValidColumnValue returns false when column rule fails.
	 *
	 * @return  void
	 */
	public function testIsValidColumnValueReturnsFalseWhenColumnRuleFails()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'sample_column' => array(
				'test' => new CustomRule(
					function ($value)
					{
						return $value !== 'test1';
					}
				)
			),
			'sample_column2' => array(
				'test two' => new CustomRule(
					function ($value)
					{
						return $value !== 'test2';
					}
				)
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($validator, $rules);

		$this->assertFalse($validator->isValidColumnValue('sample_column', 'test1'));
		$this->assertFalse($validator->isValidColumnValue('sample_column2', 'test2'));
		$this->assertTrue($validator->isValidColumnValue('sample_column', 'test3'));
		$this->assertTrue($validator->isValidColumnValue('sample_column2', 'test3'));
	}

	/**
	 * removeGlobalRule removes rule.
	 *
	 * @return  void
	 */
	public function testRemoveGlobalRuleRemovesRule()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'test' => new CustomRule(
				function ($value)
				{
					return $value === 'test1';
				}
			),
			'test two' => new CustomRule(
				function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($validator, $rules);

		$this->assertSame($rules, $globalRulesProperty->getValue($validator));

		$validator->removeGlobalRule('test');
		unset($rules['test']);

		$this->assertSame($rules, $globalRulesProperty->getValue($validator));

		$validator->removeGlobalRule('test two');

		$this->assertSame(array(), $globalRulesProperty->getValue($validator));
	}

	/**
	 * removeGlobalRules removes all the rules.
	 *
	 * @return  void
	 */
	public function testRemoveGlobalRulesRemovesAllTheRules()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'test' => new CustomRule(
				function ($value)
				{
					return $value === 'test1';
				}
			),
			'test two' => new CustomRule(
				function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($validator, $rules);

		$this->assertSame($rules, $globalRulesProperty->getValue($validator));

		$validator->removeGlobalRules();

		$this->assertSame(array(), $globalRulesProperty->getValue($validator));
	}

	/**
	 * removeRule removes rule.
	 *
	 * @return  void
	 */
	public function testRemoveRuleRemovesRule()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'sample_column' => array(
				'test' => new CustomRule(
					function ($value)
					{
						return $value === 'test1';
					}
				)
			),
			'sample_column2' => array(
				'test two' => new CustomRule(
					function ($value)
					{
						return $value === 'test2';
					}
				)
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($validator, $rules);

		$this->assertSame($rules, $rulesProperty->getValue($validator));

		$validator->removeRule('sample_column', 'test');
		unset($rules['sample_column']['test']);

		$this->assertSame($rules, $rulesProperty->getValue($validator));

		$validator->removeRule('sample_column2', 'test two');

		$this->assertSame(array('sample_column' => array(), 'sample_column2' => array()), $rulesProperty->getValue($validator));
	}

	/**
	 * removeRules removes all the rules.
	 *
	 * @return  void
	 */
	public function testRemoveRulesRemovesAllTheRules()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'sample_column' => array(
				'test' => new CustomRule(
					function ($value)
					{
						return $value === 'test1';
					}
				)
			),
			'sample_column2' => array(
				'test two' => new CustomRule(
					function ($value)
					{
						return $value === 'test2';
					}
				)
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($validator, $rules);

		$this->assertSame($rules, $rulesProperty->getValue($validator));

		$validator->removeRules();

		$this->assertSame(array(), $rulesProperty->getValue($validator));
	}

	/**
	 * rules returns all rules if no column is specified.
	 *
	 * @return  void
	 */
	public function testRulesReturnsAllRulesIfNoColumnIsSpecified()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'sample_column' => array(
				'test' => new CustomRule(
					function ($value)
					{
						return $value === 'test1';
					}
				)
			),
			'sample_column2' => array(
				'test two' => new CustomRule(
					function ($value)
					{
						return $value === 'test2';
					}
				)
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($validator, $rules);

		$this->assertSame($rules, $validator->rules());
	}

	/**
	 * rules returns only column rules if column is specified.
	 *
	 * @return  void
	 */
	public function testRulesReturnsOnlyColumnRulesIfColumnIsSpecified()
	{
		$validator = new Validator(new Entity);

		$reflection = new \ReflectionClass($validator);
		$rules = array(
			'sample_column' => array(
				'test' => new CustomRule(
					function ($value)
					{
						return $value === 'test1';
					}
				)
			),
			'sample_column2' => array(
				'test two' => new CustomRule(
					function ($value)
					{
						return $value === 'test2';
					}
				)
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($validator, $rules);

		$this->assertSame($rules['sample_column'], $validator->rules('sample_column'));
		$this->assertSame($rules['sample_column2'], $validator->rules('sample_column2'));
		$this->assertSame(array(), $validator->rules('non_existing_column'));
	}

	/**
	 * validate throws exception for invalid column value.
	 *
	 * @return  void
	 */
	public function testValidateThrowsExceptionForInvalidColumnValue()
	{
		$entity = new Entity(999);
		$entity->bind(array('id' => 999, 'sample_column' => 'test1'));

		$validator = new Validator($entity);
		$validator->addRule(
			new CustomRule(
				function ($value)
				{
					return $value !== 'test1';
				}
			),
			array('sample_column')
		);

		try
		{
			$validator->validate();
		}
		catch (ValidationException $e)
		{
		}

		$this->assertInstanceOf(ValidationException::class, $e);
		$this->assertTrue(strlen($e->getMessage()) > 0);
	}

	/**
	 * validate returns true for valid column values.
	 *
	 * @return  void
	 */
	public function testValidateReturnsTrueForValidColumnValues()
	{
		$entity = new Entity(999);
		$entity->bind(array('id' => 999, 'sample_column' => 'test1', 'sample_column2' => 1));

		$validator = new Validator($entity);
		$validator
			->addGlobalRule(
				new CustomRule(
					function ($value)
					{
						return is_int($value) || in_array($value, array('test1'), true);
					}
				)
			)
			->addRule(
				new CustomRule(
					function ($value)
					{
						return $value === 'test1';
					}
				),
				array('sample_column')
			);

		$this->assertTrue($validator->validate());
	}
}
