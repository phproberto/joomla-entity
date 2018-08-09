<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Translation;

use Phproberto\Joomla\Entity\Tests\Unit\Stubs\Entity;
use Phproberto\Joomla\Entity\Translation\Translator;
use Phproberto\Joomla\Entity\Tests\Unit\Translation\Stubs\TranslatableEntity;

/**
 * Translator decorator tests.
 *
 * @since   1.1.0
 */
class TranslatorTest extends \TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		Entity::clearAll();

		parent::tearDown();
	}

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

		$reflection = new \ReflectionClass($translator);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($translator, new Entity(999));

		$this->assertSame('translatedValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('anotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('', $translator->translate('property', 'defaultValue'));
		$this->assertSame('yetAnotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame($nullDate, $translator->translate('property', '0000-00-00 00:00:00'));
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

		$spanishTranslation = $this->getMockBuilder(Entity::class)
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

		$reflection = new \ReflectionClass($translator);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($translator, new Entity(999));

		$this->assertSame('translatedValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('anotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame('', $translator->translate('property', 'defaultValue'));
		$this->assertSame('yetAnotherValue', $translator->translate('property', 'defaultValue'));
		$this->assertSame($nullDate, $translator->translate('property', 'defaultValue'));

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

		$reflection = new \ReflectionClass($translator);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($translator, new Entity(999));

		$this->assertSame('translatedValue', $translator->translate('property2', 'defaultValue'));
		$this->assertSame('defaultValue', $translator->translate('property2', 'defaultValue'));
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
