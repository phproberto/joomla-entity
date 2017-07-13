<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content;

use Phproberto\Joomla\Entity\EntityCollection;
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

		$this->assertEquals(new EntityCollection, $method->invoke($category));

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

		$expectedCollection = new EntityCollection;

		$articles = $method->invoke($category);

		$this->assertInstanceOf(EntityCollection::class, $articles);
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
		$this->assertSame(null, $model->getState('filter.catid'));

		$category = new Category(34);

		$model = $method->invoke($category);

		$this->assertInstanceOf('ContentModelArticles', $model);
		$this->assertSame(34, $model->getState('filter.catid'));
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
}
