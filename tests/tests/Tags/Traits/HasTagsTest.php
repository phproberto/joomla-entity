<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Tags\Traits;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Tests\Tags\Traits\Stubs\ClassWithTags;
use Phproberto\Joomla\Entity\Tests\Tags\Traits\Stubs\ClassWithSearchableTags;

/**
 * HasTags trait tests.
 *
 * @since   1.1.0
 */
class HasTagsTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function contentTypeAliasReturnsExpectedString()
	{
		$this->assertSame('', ClassWithTags::contentTypeAlias());
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_tags', dirname(__DIR__) . '/Stubs/Database/tags.csv');
		$dataSet->addTable('jos_contentitem_tag_map', dirname(__DIR__) . '/Stubs/Database/contentitem_tag_map.csv');

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadTagsReturnsEmptyCollectionForEntityWithoutId()
	{
		$entity = new ClassWithSearchableTags;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadTags');
		$method->setAccessible(true);

		$tags = $method->invoke($entity);

		$this->assertInstanceOf(Collection::class, $tags);
		$this->assertTrue($tags->isEmpty());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadTagsReturnsEmptyCollectionForEntityWithoutContentTypeAlias()
	{
		$entity = new ClassWithTags(15);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadTags');
		$method->setAccessible(true);

		$tags = $method->invoke($entity);

		$this->assertInstanceOf(Collection::class, $tags);
		$this->assertTrue($tags->isEmpty());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadTagsReturnsExpectedTags()
	{
		$mockSession = $this->getMockBuilder('JSession')
			->setMethods(array('_start', 'get'))
			->getMock();

		$mockSession->expects($this->once())
			->method('get')
			->will($this->returnValue(new \JUser(42)));

		Factory::$session = $mockSession;

		$entity = new ClassWithSearchableTags(15);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadTags');
		$method->setAccessible(true);

		$tags = $method->invoke($entity);

		$this->assertInstanceOf(Collection::class, $tags);
		$this->assertFalse($tags->isEmpty());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function removeAllTagsRemovesAssignedTags()
	{
		$entity = $this->getMockBuilder(ClassWithSearchableTags::class)
			->setConstructorArgs([15])
			->setMethods(array('getDbo'))
			->getMock();

		$entity->expects($this->once())
			->method('getDbo')
			->willReturn(Factory::getDbo());

		$tags = $entity->searchTags();

		$this->assertInstanceOf(Collection::class, $tags);
		$this->assertFalse($tags->isEmpty());

		$entity->removeAllTags();

		$this->assertTrue($entity->searchTags()->isEmpty());
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function removeAllTagsThrowsExceptionForEntityWithoutId()
	{
		$entity = new ClassWithSearchableTags;

		$entity->removeAllTags();
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchTagsReturnsEmptyCollectionForMissingId()
	{
		$entity = new ClassWithSearchableTags;

		$tags = $entity->searchTags();

		$this->assertInstanceOf(Collection::class, $tags);
		$this->assertTrue($tags->isEmpty());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchTagsReturnsEmptyCollectionForMissingAlias()
	{
		$entity = new ClassWithTags;

		$tags = $entity->searchTags();

		$this->assertInstanceOf(Collection::class, $tags);
		$this->assertTrue($tags->isEmpty());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchTagsReturnsExpectedTags()
	{
		$entity = new ClassWithSearchableTags(15);

		$tags = $entity->searchTags();

		$this->assertInstanceOf(Collection::class, $tags);
		$this->assertSame([4,6], $tags->ids());
	}

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
