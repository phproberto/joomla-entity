<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Decorator;

use Phproberto\Joomla\Entity\Tests\Stubs\Entity;
use Phproberto\Joomla\Entity\Core\Decorator\Translator;
use Phproberto\Joomla\Entity\Tests\Core\Decorator\Stubs\TranslatableEntity;

/**
 * Translator decorator tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class TranslatorTest extends \TestCase
{
	/**
	 * Constructor sets entity and language tag.
	 *
	 * @return  void
	 */
	public function testConstructorSetsEntityAndLanguageTag()
	{
		$entity = new TranslatableEntity;
		$langTag = 'es-ES';

		$translator = new Translator($entity, $langTag);

		$this->assertInstanceOf(Translator::class, $translator);

		$reflection = new \ReflectionClass($translator);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);

		$langTagProperty = $reflection->getProperty('langTag');
		$langTagProperty->setAccessible(true);

		$this->assertSame($entity, $entityProperty->getValue($translator));
		$this->assertSame($langTag, $langTagProperty->getValue($translator));
	}

	/**
	 * addGlobalRule adds rule.
	 *
	 * @return  void
	 */
	public function testAddGlobalRuleAddsRule()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$rule = function ($value)
		{
			return $value !== 'test';
		};

		$translator->addGlobalRule($rule);

		$reflection = new \ReflectionClass($translator);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);

		$expected = array(spl_object_hash($rule) => $rule);

		$this->assertSame($expected, $globalRulesProperty->getValue($translator));

		$rule2 = function ($value)
		{
			return $value !== 'test2';
		};

		$translator->addGlobalRule($rule2);

		$expected = array(
			spl_object_hash($rule) => $rule,
			spl_object_hash($rule2) => $rule2
		);

		$this->assertSame($expected, $globalRulesProperty->getValue($translator));
	}

	/**
	 * addRule adds column rule.
	 *
	 * @return  void
	 */
	public function testAddsRuleAddsColumnRule()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$rule = function ($value)
		{
			return $value !== 'test';
		};

		$translator->addRule($rule, 'sample_column');

		$reflection = new \ReflectionClass($translator);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);

		$expected = array(
			'sample_column' => array(
				spl_object_hash($rule) => $rule
			)
		);

		$this->assertSame($expected, $rulesProperty->getValue($translator));

		$rule2 = function ($value)
		{
			return $value !== 'test2';
		};

		$translator->addRule($rule2, 'sample_column2');

		$expected = array(
			'sample_column' => array(
				spl_object_hash($rule) => $rule
			),
			'sample_column2' => array(
				spl_object_hash($rule2) => $rule2
			)
		);

		$this->assertSame($expected, $rulesProperty->getValue($translator));
	}

	/**
	 * defaultEmptyValues returns correct values.
	 *
	 * @return  void
	 */
	public function testDefaultEmptyValuesReturnsCorrectValues()
	{
		$translator = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->setMethods(array('nullDate'))
			->getMock();

		$translator->method('nullDate')
			->will($this->onConsecutiveCalls('1976-11-16 16:00:00', '2005-09-17 00:00:00'));

		$reflection = new \ReflectionClass($translator);

		$method = $reflection->getMethod('defaultEmptyValues');
		$method->setAccessible(true);

		$expected = array(null, '', '1976-11-16 16:00:00');
		$expected2 = array(null, '', '2005-09-17 00:00:00');

		$this->assertSame($expected, $method->invoke($translator));
		$this->assertSame($expected2, $method->invoke($translator));
	}

	/**
	 * emptyValues returns property if set.
	 *
	 * @return  void
	 */
	public function testEmptyValuesReturnsPropertyIfSet()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$emptyValues = array('Roberto', '', '1976-11-16 16:00:00');

		$emptyValuesProperty = $reflection->getProperty('emptyValues');
		$emptyValuesProperty->setAccessible(true);
		$emptyValuesProperty->setValue($translator, $emptyValues);

		$method = $reflection->getMethod('emptyValues');
		$method->setAccessible(true);

		$this->assertSame($emptyValues, $method->invoke($translator));
	}

	/**
	 * emptyValues returns default empty values if property is not set.
	 *
	 * @return  void
	 */
	public function testEmptyValuesReturnsDefaultEmptyValuesIfPropertyIsNotSet()
	{
		$emptyValues = array('Roberto', '', '1976-11-16 16:00:00');
		$emptyValues2 = array('Joomla', '', '2005-09-17 00:00:00');

		$translator = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->setMethods(array('defaultEmptyValues'))
			->getMock();

		$translator->method('defaultEmptyValues')
			->will($this->onConsecutiveCalls($emptyValues, $emptyValues2, $emptyValues));

		$reflection = new \ReflectionClass($translator);

		$method = $reflection->getMethod('emptyValues');
		$method->setAccessible(true);

		$this->assertSame($emptyValues, $method->invoke($translator));
		$this->assertSame($emptyValues2, $method->invoke($translator));
		$this->assertSame($emptyValues, $method->invoke($translator));
	}

	/**
	 * globalRules returns property value.
	 *
	 * @return  void
	 */
	public function testGlobalRulesReturnsPropertyValue()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			function ($value)
			{
				return $value === 'test1';
			},
			function ($value)
			{
				return $value === 'test2';
			}
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($translator, $rules);

		$this->assertSame($rules, $translator->globalRules());
	}

	/**
	 * hasGlobalRule returns correct value.
	 *
	 * @return  void
	 */
	public function testHasGlobalRuleReturnsCorrectValue()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'test' => function ($value)
			{
				return $value === 'test1';
			},
			'test two' => function ($value)
			{
				return $value === 'test2';
			}
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($translator, $rules);

		$this->assertTrue($translator->hasGlobalRule('test'));
		$this->assertFalse($translator->hasGlobalRule('test1'));
		$this->assertTrue($translator->hasGlobalRule('test two'));
	}

	/**
	 * hasGlobalRules returns correct value.
	 *
	 * @return  void
	 */
	public function testHasGlobalRulesReturnsCorrectValue()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$this->assertFalse($translator->hasGlobalRules());

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'test' => function ($value)
			{
				return $value === 'test1';
			},
			'test two' => function ($value)
			{
				return $value === 'test2';
			}
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($translator, $rules);

		$this->assertTrue($translator->hasGlobalRules());

		$globalRulesProperty->setValue($translator, array());

		$this->assertFalse($translator->hasGlobalRules());
	}

	/**
	 * hasRule returns correct value.
	 *
	 * @return  void
	 */
	public function testHasRuleReturnsCorrectValue()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$this->assertFalse($translator->hasRule('sample rule', 'sample_column'));

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'sample_column' => array(
				'test' => function ($value)
				{
					return $value === 'test1';
				}
			),
			'sample_column2' => array(
				'test two' => function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($translator, $rules);

		$this->assertTrue($translator->hasRule('test', 'sample_column'));
		$this->assertFalse($translator->hasRule('test two', 'sample_column'));
		$this->assertTrue($translator->hasRule('test two', 'sample_column2'));
	}

	/**
	 * hasRules returns correct value.
	 *
	 * @return  void
	 */
	public function testHasRulesReturnsCorrectValue()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$this->assertFalse($translator->hasRules());

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'sample_column' => array(
				'test' => function ($value)
				{
					return $value === 'test1';
				}
			),
			'sample_column2' => array(
				'test two' => function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($translator, $rules);

		$this->assertTrue($translator->hasRules());

		$rulesProperty->setValue($translator, array());

		$this->assertFalse($translator->hasRules());
	}

	/**
	 * isEntityLanguage returns true when translator uses entity language.
	 *
	 * @return  void
	 */
	public function testIsEntityLanguageReturnsTrueWhenTranslatorUsesEntityLanguage()
	{
		$entity = $this->getMockBuilder(TranslatableEntity::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias', 'get'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('language');

		$entity->method('get')
			->with('language')
			->willReturn('es-ES');


		$translator = new Translator($entity, 'es-ES');

		$reflection = new \ReflectionClass($translator);

		$method = $reflection->getMethod('isEntityLanguage');
		$method->setAccessible(true);

		$this->assertTrue($method->invoke($translator));

		$translator = new Translator($entity, 'en-GB');

		$this->assertFalse($method->invoke($translator));
	}

	/**
	 * isValidColumnValue returns false when global rule returns false.
	 *
	 * @return  void
	 */
	public function testIsValidColumnValueReturnsFalseWhenGlobalRuleReturnsFalse()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'test' => function ($value)
			{
				return $value !== 'test1';
			},
			'test two' => function ($value)
			{
				return $value !== 'test2';
			}
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($translator, $rules);

		$this->assertFalse($translator->isValidColumnValue('test1', 'property'));
		$this->assertFalse($translator->isValidColumnValue('test2', 'property'));
		$this->assertTrue($translator->isValidColumnValue('test3', 'property'));
	}

	/**
	 * isValidColumnValue returns false when column rule fails.
	 *
	 * @return  void
	 */
	public function testIsValidColumnValueReturnsFalseWhenColumnRuleFails()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'sample_column' => array(
				'test' => function ($value)
				{
					return $value !== 'test1';
				}
			),
			'sample_column2' => array(
				'test two' => function ($value)
				{
					return $value !== 'test2';
				}
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($translator, $rules);

		$this->assertFalse($translator->isValidColumnValue('test1', 'sample_column'));
		$this->assertFalse($translator->isValidColumnValue('test2', 'sample_column2'));
		$this->assertTrue($translator->isValidColumnValue('test3', 'sample_column'));
		$this->assertTrue($translator->isValidColumnValue('test3', 'sample_column2'));
	}

	/**
	 * noEmptyColumnValues adds rule.
	 *
	 * @return  void
	 */
	public function testNoEmptyColumnValuesAddsRule()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);

		$this->assertFalse(isset($rulesProperty->getValue($translator)['sample_column']['noEmptyColumnValues']));

		$translator->noEmptyColumnValues('sample_column');

		$this->assertTrue(isset($rulesProperty->getValue($translator)['sample_column']['noEmptyColumnValues']));
	}

	/**
	 * noEmptyValues adds rule.
	 *
	 * @return  void
	 */
	public function testNoEmptyValuesAddsRule()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);

		$this->assertFalse(isset($globalRulesProperty->getValue($translator)['noEmptyValues']));

		$translator->noEmptyValues();

		$this->assertTrue(isset($globalRulesProperty->getValue($translator)['noEmptyValues']));
	}

	/**
	 * removeGlobalRule removes rule.
	 *
	 * @return  void
	 */
	public function testRemoveGlobalRuleRemovesRule()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'test' => function ($value)
			{
				return $value === 'test1';
			},
			'test two' => function ($value)
			{
				return $value === 'test2';
			}
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($translator, $rules);

		$this->assertSame($rules, $globalRulesProperty->getValue($translator));

		$translator->removeGlobalRule('test');
		unset($rules['test']);

		$this->assertSame($rules, $globalRulesProperty->getValue($translator));

		$translator->removeGlobalRule('test two');

		$this->assertSame(array(), $globalRulesProperty->getValue($translator));
	}

	/**
	 * removeGlobalRules removes all the rules.
	 *
	 * @return  void
	 */
	public function testRemoveGlobalRulesRemovesAllTheRules()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'test' => function ($value)
			{
				return $value === 'test1';
			},
			'test two' => function ($value)
			{
				return $value === 'test2';
			}
		);

		$globalRulesProperty = $reflection->getProperty('globalRules');
		$globalRulesProperty->setAccessible(true);
		$globalRulesProperty->setValue($translator, $rules);

		$this->assertSame($rules, $globalRulesProperty->getValue($translator));

		$translator->removeGlobalRules();

		$this->assertSame(array(), $globalRulesProperty->getValue($translator));
	}

	/**
	 * removeRule removes rule.
	 *
	 * @return  void
	 */
	public function testRemoveRuleRemovesRule()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'sample_column' => array(
				'test' => function ($value)
				{
					return $value === 'test1';
				}
			),
			'sample_column2' => array(
				'test two' => function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($translator, $rules);

		$this->assertSame($rules, $rulesProperty->getValue($translator));

		$translator->removeRule('test', 'sample_column');
		unset($rules['sample_column']['test']);

		$this->assertSame($rules, $rulesProperty->getValue($translator));

		$translator->removeRule('test two', 'sample_column2');

		$this->assertSame(array('sample_column' => array(), 'sample_column2' => array()), $rulesProperty->getValue($translator));
	}

	/**
	 * removeRules removes all the rules.
	 *
	 * @return  void
	 */
	public function testRemoveRulesRemovesAllTheRules()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'sample_column' => array(
				'test' => function ($value)
				{
					return $value === 'test1';
				}
			),
			'sample_column2' => array(
				'test two' => function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($translator, $rules);

		$this->assertSame($rules, $rulesProperty->getValue($translator));

		$translator->removeRules();

		$this->assertSame(array(), $rulesProperty->getValue($translator));
	}

	/**
	 * rules returns all rules if no column is specified.
	 *
	 * @return  void
	 */
	public function testRulesReturnsAllRulesIfNoColumnIsSpecified()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$this->assertFalse($translator->hasRules());

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'sample_column' => array(
				'test' => function ($value)
				{
					return $value === 'test1';
				}
			),
			'sample_column2' => array(
				'test two' => function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($translator, $rules);

		$this->assertSame($rules, $translator->rules());
	}

	/**
	 * rules returns only column rules if column is specified.
	 *
	 * @return  void
	 */
	public function testRulesReturnsOnlyColumnRulesIfColumnIsSpecified()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);
		$rules = array(
			'sample_column' => array(
				'test' => function ($value)
				{
					return $value === 'test1';
				}
			),
			'sample_column2' => array(
				'test two' => function ($value)
				{
					return $value === 'test2';
				}
			)
		);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($translator, $rules);

		$this->assertSame($rules['sample_column'], $translator->rules('sample_column'));
		$this->assertSame($rules['sample_column2'], $translator->rules('sample_column2'));
		$this->assertSame(array(), $translator->rules('non_existing_column'));
	}

	/**
	 * setEmptyValues sets property.
	 *
	 * @return  void
	 */
	public function testSetEmptyValuesSetsProperty()
	{
		$translator = new Translator(new TranslatableEntity, 'es-ES');

		$reflection = new \ReflectionClass($translator);

		$emptyValuesProperty = $reflection->getProperty('emptyValues');
		$emptyValuesProperty->setAccessible(true);

		$this->assertSame(null, $emptyValuesProperty->getValue($translator));

		$translator->setEmptyValues(array('one', 'two'));

		$this->assertSame(array('one', 'two'), $emptyValuesProperty->getValue($translator));
	}

	/**
	 * emptyValues returns correct values.
	 *
	 * @return  void
	 */
	public function testEmptyValuesReturnsCorrectValues()
	{
		$nullDate = '1976-11-16 16:00:00';
		$expectedEmptyValues = array(null, '', $nullDate);

		$translator = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->setMethods(array('nullDate'))
			->getMock();

		$translator->method('nullDate')
			->willReturn($nullDate);

		$reflection = new \ReflectionClass($translator);

		$method = $reflection->getMethod('emptyValues');
		$method->setAccessible(true);

		$this->assertSame($expectedEmptyValues, $method->invoke($translator));
	}

	/**
	 * translate returns correct value.
	 *
	 * @return  void
	 */
	public function testTranslateReturnsCorrectValuesWithNoEmptyValues()
	{
		$nullDate = '1976-11-16 16:00:00';
		$emptyValues = array(null, '', $nullDate);

		$spanishTranslation = $this->getMockBuilder('MockedTranslation')
			->setMethods(array('get'))
			->getMock();

		$spanishTranslation->method('get')
			->with($this->equalTo('property'))
			->will($this->onConsecutiveCalls('translatedValue', null, 'anotherValue', '', 'yetAnotherValue', $nullDate));

		$translator = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->setMethods(array('translation', 'isEntityLanguage'))
			->getMock();

		$translator->method('translation')
			->willReturn($spanishTranslation);

		$translator->method('isEntityLanguage')
			->willReturn(false);

		$translator->setEmptyValues($emptyValues)->noEmptyValues();

		$this->assertSame('translatedValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('anotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('yetAnotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('0000-00-00 00:00:00', $translator->translate('property', '0000-00-00 00:00:00'));
	}

	/**
	 * translate returns correct value.
	 *
	 * @return  void
	 */
	public function testTranslateReturnsCorrectValuesWithNoEmptyColumnValues()
	{
		$nullDate = '1976-11-16 16:00:00';
		$emptyValues = array(null, '', $nullDate);

		$spanishTranslation = $this->getMockBuilder('MockedTranslation')
			->setMethods(array('get'))
			->getMock();

		$spanishTranslation->method('get')
			->with('property')
			->will($this->onConsecutiveCalls('translatedValue', null, 'anotherValue', '', 'yetAnotherValue', $nullDate));

		$translator = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->setMethods(array('translation', 'isEntityLanguage'))
			->getMock();

		$translator->method('translation')
			->willReturn($spanishTranslation);

		$translator->method('isEntityLanguage')
			->willReturn(false);

		$translator->setEmptyValues($emptyValues)->noEmptyColumnValues('property');

		$this->assertSame('translatedValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('anotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('yetAnotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));

		$spanishTranslation = $this->getMockBuilder('MockedTranslation')
			->setMethods(array('get'))
			->getMock();

		$spanishTranslation->method('get')
			->with('property2')
			->will($this->onConsecutiveCalls('translatedValue', null, 'anotherValue', '', 'yetAnotherValue', $nullDate));

		$translator = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->setMethods(array('translation', 'isEntityLanguage'))
			->getMock();

		$translator->method('translation')
			->willReturn($spanishTranslation);

		$translator->method('isEntityLanguage')
			->willReturn(false);

		$translator->setEmptyValues($emptyValues)->noEmptyColumnValues('property');

		$this->assertSame('translatedValue', $translator->translate('property2', 'defaultValue'));
		$this->assertSame(null, $translator->translate('property2', 'defaultValue'));
		$this->assertSame('anotherValue', $translator->translate('property2', 'defaultValue'));
		$this->assertSame('', $translator->translate('property2', 'defaultValue'));
		$this->assertSame('yetAnotherValue', $translator->translate('property2', 'defaultValue'));
		$this->assertSame('1976-11-16 16:00:00', $translator->translate('property2', 'defaultValue'));
	}

	/**
	 * translation returns entity translation.
	 *
	 * @return  void
	 */
	public function testTranslationReturnsEntityTranslation()
	{
		$nullDate = '1976-11-16 16:00:00';
		$emptyValues = array(null, '', $nullDate);

		$entity = $this->getMockBuilder('MockedTranslation')
			->setMethods(array('translation'))
			->getMock();

		$entity->method('translation')
			->with($this->equalTo('es-ES'))
			->willReturn(new TranslatableEntity(999));

		$translator = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->getMock();

		$reflection = new \ReflectionClass($translator);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($translator, $entity);

		$langTagProperty = $reflection->getProperty('langTag');
		$langTagProperty->setAccessible(true);
		$langTagProperty->setValue($translator, 'es-ES');

		$method = $reflection->getMethod('translation');
		$method->setAccessible(true);

		$this->assertEquals(new TranslatableEntity(999), $method->invoke($translator));
	}
}
