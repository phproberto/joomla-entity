<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Tags\Traits;

use Phproberto\Joomla\Entity\EntityCollection;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Tests\Tags\Traits\Stubs\ClassWithTags;

/**
 * HasTags trait tests.
 *
 * @since   __DEPLOY_VERSION__
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
		ClassWithTags::clearAllInstances();

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

		$tags = new EntityCollection(
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
	 * getTags returns correct data.
	 *
	 * @return  void
	 */
	public function testGetTagsReturnsCorrectData()
	{
		$entity = new ClassWithTags;

		$this->assertEquals(new EntityCollection, $entity->getTags());

		$entity->tagsIds = array(999);

		// Previous data with no reload
		$this->assertEquals(new EntityCollection, $entity->getTags());
		$this->assertEquals(new EntityCollection(array(new Tag(999))), $entity->getTags(true));
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
