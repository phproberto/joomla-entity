<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Content\Traits;

use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Tests\Unit\Content\Traits\Stubs\ClassWithArticle;

/**
 * HasArticle trait tests.
 *
 * @since   1.1.0
 */
class HasArticleTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Column storign the article identifier.
	 *
	 * @const
	 */
	const ARTICLE_COLUMN = 'article_id';

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		ClassWithArticle::clearAll();

		parent::tearDown();
	}

	/**
	 * getColumnArticle returns correct value.
	 *
	 * @return  void
	 */
	public function testGetColumnArticleReturnsCorrectValue()
	{
		$class = new ClassWithArticle;

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('getColumnArticle');
		$method->setAccessible(true);

		$this->assertEquals(static::ARTICLE_COLUMN, $method->invoke($class));
	}

	/**
	 * loadArticle loads correct article.
	 *
	 * @return  void
	 */
	public function testLoadArticleLodsCorrectArticle()
	{
		$class = new ClassWithArticle;

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('loadArticle');
		$method->setAccessible(true);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999));

		$this->assertEquals(new Article, $method->invoke($class));

		$rowProperty->setValue($class, array('id' => 999, static::ARTICLE_COLUMN => 666));

		$this->assertEquals(new Article(666), $method->invoke($class));
	}

	/**
	 * loadArticle works with custom column.
	 *
	 * @return  void
	 */
	public function testLoadArticleWorksWithCustomColumn()
	{
		$class = $this->getMockBuilder(ClassWithArticle::class)
			->setMethods(array('getColumnArticle'))
			->getMock();

		$class->method('getColumnArticle')
			->willReturn('custom_article_id');

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('loadArticle');
		$method->setAccessible(true);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999));

		$this->assertEquals(new Article, $method->invoke($class));

		$rowProperty->setValue($class, array('id' => 999, 'custom_article_id' => 666));

		$this->assertEquals(new Article(666), $method->invoke($class));

	}

	/**
	 * getArticle returns correct data.
	 *
	 * @return  void
	 */
	public function testGetArticleReturnsCorrectData()
	{
		$class = new ClassWithArticle;

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999));

		$this->assertEquals(new Article, $class->getArticle());

		$rowProperty->setValue($class, array('id' => 999, static::ARTICLE_COLUMN => 666));

		$this->assertEquals(new Article, $class->getArticle());
		$this->assertEquals(new Article(666), $class->getArticle(true));
	}

	/**
	 * hasArticle returns correct value.
	 *
	 * @return  void
	 */
	public function testHasArticleReturnsCorrectValue()
	{
		$class = new ClassWithArticle;

		$reflection = new \ReflectionClass($class);
		$articleProperty = $reflection->getProperty('article');
		$articleProperty->setAccessible(true);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999));

		$this->assertFalse($class->hasArticle());

		$rowProperty->setValue($class, array('id' => 999, static::ARTICLE_COLUMN => 666));

		// Cached data
		$this->assertFalse($class->hasArticle());

		$articleProperty->setValue($class, null);
		$this->assertTrue($class->hasArticle());
	}
}
