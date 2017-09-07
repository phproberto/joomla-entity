<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Decorator;

use Phproberto\Joomla\Entity\Tests\Stubs\Entity;
use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Validation\Rule\CustomRule;
use Phproberto\Joomla\Entity\Translation\TranslatorWithFallback;
use Phproberto\Joomla\Entity\Tests\Translation\Stubs\TranslatableEntity;

/**
 * TranslatorWithFallback decorator tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class TranslatorWithFallbackTest extends \TestCase
{
	/**
	 * translateIf returns correct value.
	 *
	 * @return  void
	 */
	public function testTranslateReturnsCorrectValue()
	{
		$spanishTranslation = $this->getMockBuilder('MockedTranslation')
			->setMethods(array('get'))
			->getMock();

		$spanishTranslation->method('get')
			->with($this->equalTo('property'))
			->will($this->onConsecutiveCalls('translatedValue', 'validValue', 'invalidValue', 'validValue'));

		$entity = new TranslatableEntity;
		$entity->bind(array('id' => 999, 'language' => 'en-GB', 'property' => 'entityValue'));

		$translator = $this->getMockBuilder(TranslatorWithFallback::class)
			->disableOriginalConstructor()
			->setMethods(array('translation', 'isEntityLanguage'))
			->getMock();

		$translator->method('translation')
			->willReturn($spanishTranslation);

		$translator->method('isEntityLanguage')
			->willReturn(false);

		$reflection = new \ReflectionClass($translator);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($translator, $entity);

		$langTagProperty = $reflection->getProperty('langTag');
		$langTagProperty->setAccessible(true);
		$langTagProperty->setValue($translator, 'es-ES');

		$validator = new Validator($entity);

		$validator->addRule(
			new CustomRule(
				function ($value) {
					return in_array($value, array('validValue'), true);
				}
			),
			'property',
			'testValidValue'
		);

		$translator->setValidator($validator);

		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('validValue', $translator->translate('property', 'defaultValue'));

		$validatorReflection = new \ReflectionClass($validator);
		$rulesProperty = $validatorReflection->getProperty('rules');
		$rulesProperty->setAccessible(true);
		$rulesProperty->setValue($validator, array());

		$validator->addRule(
			new CustomRule(
				function ($value) {
					return in_array($value, array('validValue', 'entityValue'), true);
				}
			),
			'property',
			'testValidValue'
		);

		$translator->setValidator($validator);

		$this->assertSame('entityValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('validValue', $translator->translate('property', 'defaultValue'));
	}

	/**
	 * translate returns entity value if is entity language.
	 *
	 * @return  void
	 */
	public function testTranslateReturnsEntityValueIfIsEntityLanguage()
	{
		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('get'))
			->getMock();

		$entity->method('get')
			->with($this->equalTo('property'))
			->will($this->onConsecutiveCalls('value', '', null, 'anotherValue', '0000-00-00 00:00:00'));

		$translator = $this->getMockBuilder(TranslatorWithFallback::class)
			->disableOriginalConstructor()
			->setMethods(array('isEntityLanguage'))
			->getMock();

		$translator->method('isEntityLanguage')
			->willReturn(true);

		$reflection = new \ReflectionClass($translator);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($translator, $entity);

		$this->assertSame('value', $translator->translate('property', 'default'));
		$this->assertSame('', $translator->translate('property', 'default'));
		$this->assertSame('default', $translator->translate('property', 'default'));
		$this->assertSame('anotherValue', $translator->translate('property', 'default'));
		$this->assertSame('0000-00-00 00:00:00', $translator->translate('property', 'default'));
	}

	/**
	 * translate returns translation value if not entity language.
	 *
	 * @return  void
	 */
	public function testTranslateReturnsTranslationValueIfNotEntityLanguage()
	{
		$translation = $this->getMockBuilder(Entity::class)
			->setMethods(array('get'))
			->getMock();

		$translation->method('get')
			->with($this->equalTo('property'))
			->will($this->onConsecutiveCalls('value', '', null, 'anotherValue', '0000-00-00 00:00:00'));

		$translator = $this->getMockBuilder(TranslatorWithFallback::class)
			->disableOriginalConstructor()
			->setMethods(array('isEntityLanguage', 'translation'))
			->getMock();

		$translator->method('isEntityLanguage')
			->willReturn(false);

		$translator->method('translation')
			->willReturn($translation);

		$entity = new Entity(999);
		$entity->bind(array('id' => 999, 'property' => 'entityValue'));

		$reflection = new \ReflectionClass($translator);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($translator, $entity);

		$this->assertSame('value', $translator->translate('property', 'default'));
		$this->assertSame('', $translator->translate('property', 'default'));
		$this->assertSame('entityValue', $translator->translate('property', 'default'));
		$this->assertSame('anotherValue', $translator->translate('property', 'default'));
		$this->assertSame('0000-00-00 00:00:00', $translator->translate('property', 'default'));
	}
}
