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
	 * translateIf returns correct value.
	 *
	 * @return  void
	 */
	public function testTranslateIfReturnsCorrectValue()
	{
		$spanishTranslation = $this->getMockBuilder('MockedTranslation')
			->setMethods(array('get'))
			->getMock();

		$spanishTranslation->method('get')
			->with($this->equalTo('property'))
			->will($this->onConsecutiveCalls('translatedValue', 'invalidValue'));

		$entity = $this->getMockBuilder(TranslatableEntity::class)
			->disableOriginalConstructor()
			->setMethods(array('translation'))
			->getMock();

		$entity->method('translation')
			->willReturn($spanishTranslation);

		$entity->bind(array('id' => 999, 'language' => 'en-GB', 'property' => 'value'));

		$translator = new Translator($entity, 'es-ES');

		$condition = function ($value) {
			return 'translatedValue' === $value;
		};

		$this->assertSame('translatedValue', $translator->translateIf($condition, 'property'));
		$this->assertSame('defaultValue', $translator->translateIf($condition, 'property', 'defaultValue'));
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
	public function testTranslateReturnsCorrectValue()
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
			->setMethods(array('emptyValues', 'translation'))
			->getMock();

		$translator->method('emptyValues')
			->willReturn($emptyValues);

		$translator->method('translation')
			->willReturn($spanishTranslation);

		$this->assertSame('translatedValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('anotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('yetAnotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('0000-00-00 00:00:00', $translator->translate('property', '0000-00-00 00:00:00'));
	}
}
