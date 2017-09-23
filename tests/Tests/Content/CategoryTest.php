<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content;

use Phproberto\Joomla\Entity\Acl\Acl;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Content\Category;

/**
 * Content category entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class CategoryTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		Category::clearAllInstances();

		parent::tearDown();
	}

	/**
	 * Acl can be retrieved.
	 *
	 * @return  void
	 */
	public function testAclCanBeRetrieved()
	{
		$entity = new Category(666);
		$user = new User(999);

		$acl = $entity->acl($user);

		$reflection = new \ReflectionClass($acl);
		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);

		$this->assertInstanceOf(Acl::class, $acl);
		$this->assertSame($user, $userProperty->getValue($acl));
		$this->assertSame($entity, $entityProperty->getValue($acl));
	}

	/**
	 * loadArticles returns correct value.
	 *
	 * @return  void
	 */
	public function testLoadArticlesReturnsCorrectValue()
	{
		$category = new Category;

		$reflection = new \ReflectionClass($category);
		$method = $reflection->getMethod('loadArticles');
		$method->setAccessible(true);

		$this->assertEquals(new Collection, $method->invoke($category));

		$articlesItems = array(
			(object) array(
				'id' => 999,
				'Sample article'
			),
			(object) array(
				'id' => 1000,
				'Sample 1000 article'
			)
		);

		$category = $this->getCategoryMock(666, $articlesItems);

		$expectedCollection = new Collection;

		$articles = $method->invoke($category);

		$this->assertInstanceOf(Collection::class, $articles);
		$this->assertSame(2, $articles->count());
		$this->assertTrue($articles->has(999));
		$this->assertTrue($articles->has(1000));
	}

	/**
	 * getArticlesModel returns correct model.
	 *
	 * @return  void
	 */
	public function testGetArticlesModelReturnsCorrectModel()
	{
		$category = new Category;

		$reflection = new \ReflectionClass($category);
		$method = $reflection->getMethod('getArticlesModel');
		$method->setAccessible(true);

		$model = $method->invoke($category);

		$this->assertInstanceOf('ContentModelArticles', $model);
		$this->assertSame(null, $model->getState('filter.category_id'));

		$category = new Category(34);

		$model = $method->invoke($category);

		$this->assertInstanceOf('ContentModelArticles', $model);
		$this->assertSame(34, $model->getState('filter.category_id'));
	}

	/**
	 * Get a mock of the articles model returning specific items.
	 *
	 * @param   array  $items  Items returned
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getArticlesModelMock(array $items = array())
	{
		$mock = $this->getMockBuilder('ArticlesModelMock')
			->disableOriginalConstructor()
			->setMethods(array('getItems'))
			->getMock();

		$mock->expects($this->once())
			->method('getItems')
			->willReturn($items);

		return $mock;
	}

	/**
	 * Get a mock of a categoryy returning specific items.
	 *
	 * @param   integer  $id     Identifier to assign
	 * @param   array    $items  Items returned
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getCategoryMock($id = null, array $items = array())
	{
		$category = $this->getMockBuilder(Category::class)
			->setMethods(array('getArticlesModel'))
			->getMock();

		$category->expects($this->once())
			->method('getArticlesModel')
			->willReturn($this->getArticlesModelMock($items));

		if ($id)
		{
			$reflection = new \ReflectionClass($category);
			$idProperty = $reflection->getProperty('id');
			$idProperty->setAccessible(true);
			$idProperty->setValue($category, $id);
		}

		return $category;
	}

	/**
	 * loadTags returns empty collection for missing id.
	 *
	 * @return  void
	 */
	public function testLoadTagsReturnsEmptyCollectionForMissingId()
	{
		$category = new Category;

		$reflection = new \ReflectionClass($category);
		$method = $reflection->getMethod('loadTags');
		$method->setAccessible(true);

		$this->assertEquals(new Collection, $method->invoke($category));
	}

	/**
	 * loadTags loads correct data for existing id.
	 *
	 * @return  void
	 */
	public function testLoadTagsReturnsCorrectDataForExistingId()
	{
		$helperMock = $this->getMockBuilder(\JHelperTags::class)
			->disableOriginalConstructor()
			->setMethods(array('getItemTags'))
			->getMock();

		$helperMock->method('getItemTags')
			->willReturn(
				array(
					(object) array(
						'id' => 23,
						'title' => 'Sample tag'
					)
				)
			);

		$entity = $this->getMockBuilder(Category::class)
			->setMethods(array('getTagsHelperInstance'))
			->getMock();

		$entity
			->method('getTagsHelperInstance')
			->willReturn($helperMock);

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($entity, 999);

		$method = $reflection->getMethod('loadTags');
		$method->setAccessible(true);

		$tag = new Tag(23);

		$tagReflection = new \ReflectionClass($tag);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($tag, array('id' => 23, 'title' => 'Sample tag'));

		$this->assertEquals(new Collection(array($tag)), $method->invoke($entity));
	}
}
