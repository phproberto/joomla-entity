<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Content\Traits;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Tests\Unit\Content\Traits\Stubs\ClassWithArticles;

/**
 * HasArticles trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasArticlesTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		ClassWithArticles::clearAll();

		parent::tearDown();
	}

	/**
	 * clearArticles clears articles property.
	 *
	 * @return  void
	 */
	public function testClearArticlesClearsArticlesProperty()
	{
		$entity = new ClassWithArticles;

		$reflection = new \ReflectionClass($entity);
		$articlesProperty = $reflection->getProperty('articles');
		$articlesProperty->setAccessible(true);

		$this->assertEquals(null, $articlesProperty->getValue($entity));

		$articles = new Collection(
			array(
				new Article(23),
				new Article(24),
				new Article(25)
			)
		);

		$articlesProperty->setValue($entity, $articles);
		$this->assertEquals($articles, $articlesProperty->getValue($entity));

		$entity->clearArticles();
		$this->assertEquals(null, $articlesProperty->getValue($entity));
	}

	/**
	 * clearArticles is chainable.
	 *
	 * @return  void
	 */
	public function testClearArticlesIsChainable()
	{
		$entity = new ClassWithArticles;

		$this->assertTrue($entity->clearArticles() instanceof ClassWithArticles);
	}

	/**
	 * articles returns correct data.
	 *
	 * @return  void
	 */
	public function testArticlesReturnsCorrectData()
	{
		$entity = new ClassWithArticles;

		$this->assertEquals(new Collection, $entity->articles());

		$entity->articlesIds = array(999);

		// Previous data with no reload
		$this->assertEquals(new Collection, $entity->articles());
		$this->assertEquals(new Collection(array(new Article(999))), $entity->articles(true));
	}

	/**
	 * hasArticle returns correct value.
	 *
	 * @return  void
	 */
	public function testHasArticleReturnsCorrectValue()
	{
		$entity = new ClassWithArticles;

		$entity->articlesIds = array(999, 1001, 1003);

		$this->assertFalse($entity->hasArticle(998));
		$this->assertTrue($entity->hasArticle(999));
		$this->assertFalse($entity->hasArticle(1000));
		$this->assertTrue($entity->hasArticle(1001));
		$this->assertFalse($entity->hasArticle(1002));
		$this->assertTrue($entity->hasArticle(1003));
	}

	/**
	 * hasArticles returns correct value.
	 *
	 * @return  void
	 */
	public function testHasArticlesReturnsCorrectValue()
	{
		$entity = new ClassWithArticles;

		$this->assertFalse($entity->hasArticles());

		$entity = new ClassWithArticles;
		$entity->articlesIds = array(999, 1001, 1003);

		$this->assertTrue($entity->hasArticles());
	}
}
