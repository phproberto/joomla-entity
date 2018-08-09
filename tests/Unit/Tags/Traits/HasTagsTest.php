<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Tags\Traits;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Tests\Unit\Tags\Traits\Stubs\ClassWithTags;

/**
 * HasTags trait tests.
 *
 * @since   1.1.0
 */
class HasTagsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		ClassWithTags::clearAll();

		parent::tearDown();
	}

	/**
	 * clearTags clears tags property.
	 *
	 * @return  void
	 */
	public function testClearTagsClearsTagsProperty()
	{
		$entity = new ClassWithTags;

		$reflection = new \ReflectionClass($entity);
		$tagsProperty = $reflection->getProperty('tags');
		$tagsProperty->setAccessible(true);

		$this->assertEquals(null, $tagsProperty->getValue($entity));

		$tags = new Collection(
			array(
				new Tag(23),
				new Tag(24),
				new Tag(25)
			)
		);

		$tagsProperty->setValue($entity, $tags);
		$this->assertEquals($tags, $tagsProperty->getValue($entity));

		$entity->clearTags();
		$this->assertEquals(null, $tagsProperty->getValue($entity));
	}

	/**
	 * clearTags is chainable.
	 *
	 * @return  void
	 */
	public function testClearTagsIsChainable()
	{
		$entity = new ClassWithTags;

		$this->assertTrue($entity->clearTags() instanceof ClassWithTags);
	}

	/**
	 * getTagsHelper returns correct class.
	 *
	 * @return  void
	 */
	public function testGetTagsHelperReturnsCorrectInstance()
	{
		$entity = new ClassWithTags;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('getTagsHelperInstance');
		$method->setAccessible(true);

		$this->assertTrue($method->invoke($entity) instanceof \JHelperTags);
	}

	/**
	 * tags returns correct data.
	 *
	 * @return  void
	 */
	public function testTagsReturnsCorrectData()
	{
		$entity = new ClassWithTags;

		$this->assertEquals(new Collection, $entity->tags());

		$entity->tagsIds = array(999);

		// Previous data with no reload
		$this->assertEquals(new Collection, $entity->tags());
		$this->assertEquals(new Collection(array(new Tag(999))), $entity->tags(true));
	}

	/**
	 * hasTag returns correct value.
	 *
	 * @return  void
	 */
	public function testHasTagReturnsCorrectValue()
	{
		$entity = new ClassWithTags;

		$entity->tagsIds = array(999, 1001, 1003);

		$this->assertFalse($entity->hasTag(998));
		$this->assertTrue($entity->hasTag(999));
		$this->assertFalse($entity->hasTag(1000));
		$this->assertTrue($entity->hasTag(1001));
		$this->assertFalse($entity->hasTag(1002));
		$this->assertTrue($entity->hasTag(1003));
	}

	/**
	 * hasTags returns correct value.
	 *
	 * @return  void
	 */
	public function testHasTagsReturnsCorrectValue()
	{
		$entity = new ClassWithTags;

		$this->assertFalse($entity->hasTags());

		$entity = new ClassWithTags;
		$entity->tagsIds = array(999, 1001, 1003);

		$this->assertTrue($entity->hasTags());
	}
}
