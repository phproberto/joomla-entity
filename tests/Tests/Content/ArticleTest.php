<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content;

use Phproberto\Joomla\Entity\Content\Article;
use Joomla\Registry\Registry;

/**
 * Article entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ArticleTest extends \TestCaseDatabase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		\JFactory::$session     = $this->getMockSession();
		\JFactory::$config      = $this->getMockConfig();
		\JFactory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_assets', JPATH_TEST_DATABASE . '/jos_assets.csv');
		$dataSet->addTable('jos_categories', JPATH_TEST_DATABASE . '/jos_categories.csv');
		$dataSet->addTable('jos_content', JPATH_TEST_DATABASE . '/jos_content.csv');

		return $dataSet;
	}

	/**
	 * Article loaded.
	 *
	 * @return  void
	 */
	public function testArticleLoaded()
	{
		$article = Article::fetch(1);

		$this->assertTrue($article->isLoaded());
	}

	/**
	 * Asset can be retrieved.
	 *
	 * @return  void
	 */
	public function testAssetCanBeRetrieved()
	{
		$article = new Article;

		$asset = $article->getAsset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertSame(0, $asset->getId());

		$article = Article::instance(1);

		$asset = $article->getAsset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertNotSame(0, $asset->getId());
	}

	/**
	 * Category can be retrieved.
	 *
	 * @return  void
	 */
	public function testCategoryCanBeRetrieved()
	{
		$article = new Article;

		$category = $article->getCategory();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Categories\Category', $category);
		$this->assertSame(0, $category->getId());

		$article = Article::instance(1);

		$category = $article->getCategory();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Categories\Category', $category);
		$this->assertNotSame(0, $category->getId());
	}

	/**
	 * getFullTextImage returns correct value.
	 *
	 * @return  void
	 */
	public function testGetFullTextImageReturnsCorrectValue()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'images' => '']);

		$this->assertEquals([], $article->getFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png"}']);

		$this->assertEquals(['url' => 'images/joomla_black.png'], $article->getFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertEquals([], $article->getFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}']);

		$this->assertEquals([], $article->getFullTextImage());
	}

	/**
	 * getImages returns intro image if exists.
	 *
	 * @return  void
	 */
	public function testGetImagesReturnsEmptyArrayForNoImages()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'images' => '']);

		$this->assertSame([], $article->getImages(true));

		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertSame([], $article->getImages(true));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}']);

		$this->assertSame([], $article->getImages(true));
	}

	/**
	 * getImages returns intro image if exists.
	 *
	 * @return  void
	 */
	public function testGetImagesReturnsFullImageIfExists()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png"}']);

		$images = $article->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertFalse(isset($images['full']['float']));
		$this->assertFalse(isset($images['full']['alt']));
		$this->assertFalse(isset($images['full']['caption']));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left"}']);

		$images = $article->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertEquals("left", $images['full']['float']);
		$this->assertFalse(isset($images['full']['alt']));
		$this->assertFalse(isset($images['full']['caption']));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left","image_fulltext_alt":"Alt text"}']);

		$images = $article->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertEquals("left", $images['full']['float']);
		$this->assertEquals("Alt text", $images['full']['alt']);
		$this->assertFalse(isset($images['full']['caption']));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left","image_fulltext_alt":"Alt text","image_fulltext_caption":"Caption text"}']);

		$images = $article->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertEquals("left", $images['full']['float']);
		$this->assertEquals("Alt text", $images['full']['alt']);
		$this->assertEquals("Caption text", $images['full']['caption']);
	}

	/**
	 * getImages returns intro image if exists.
	 *
	 * @return  void
	 */
	public function testGetImagesReturnsIntroImageIfExists()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png"}']);

		$images = $article->getImages(true);

		$this->assertTrue(isset($article->getImages()['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertFalse(isset($images['intro']['float']));
		$this->assertFalse(isset($images['intro']['alt']));
		$this->assertFalse(isset($images['intro']['caption']));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left"}']);

		$images = $article->getImages(true);

		$this->assertTrue(isset($images['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertEquals("left", $images['intro']['float']);
		$this->assertFalse(isset($images['intro']['alt']));
		$this->assertFalse(isset($images['intro']['caption']));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text"}']);

		$images = $article->getImages(true);

		$this->assertTrue(isset($images['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertEquals("left", $images['intro']['float']);
		$this->assertEquals("Alt text", $images['intro']['alt']);
		$this->assertFalse(isset($images['intro']['caption']));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text"}']);

		$images = $article->getImages(true);

		$this->assertTrue(isset($images['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertEquals("left", $images['intro']['float']);
		$this->assertEquals("Alt text", $images['intro']['alt']);
		$this->assertEquals("Caption text", $images['intro']['caption']);
	}

	/**
	 * getIntroImage returns correct value.
	 *
	 * @return  void
	 */
	public function testGetIntroImageReturnsCorrectValue()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'images' => '']);

		$this->assertEquals([], $article->getIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png"}']);

		$this->assertEquals(['url' => 'images/joomla_black.png'], $article->getIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertEquals([], $article->getIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}']);

		$this->assertEquals([], $article->getIntroImage());
	}

	/**
	 * getParams returns parameters.
	 *
	 * @return  void
	 */
	public function testGetParamsReturnsParameters()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'attribs' => '{"foo":"var"}']);

		$this->assertEquals(new Registry(['foo' => 'var']), $article->getParams());
	}

	/**
	 * hasFullTextImage returns correct value.
	 *
	 * @return  void
	 */
	public function testhasFullTextImageReturnsCorrectValue()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'images' => '']);

		$this->assertFalse($article->hasFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png"}']);

		$this->assertTrue($article->hasFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertFalse($article->hasFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}']);

		$this->assertFalse($article->hasFullTextImage());
	}

	/**
	 * hasIntroImage returns correct value.
	 *
	 * @return  void
	 */
	public function testhasIntroImageReturnsCorrectValue()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'images' => '']);

		$this->assertFalse($article->hasIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png"}']);

		$this->assertTrue($article->hasIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertFalse($article->hasIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}']);

		$this->assertFalse($article->hasIntroImage());
	}

	/**
	 * isFeatured returns correct value.
	 *
	 * @return  void
	 */
	public function testIsFeaturedReturnsCorrectValue()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertFalse($article->isFeatured());

		$rowProperty->setValue($article, ['id' => 999, 'featured' => 0]);

		$this->assertFalse($article->isFeatured());

		$rowProperty->setValue($article, ['id' => 999, 'featured' => '0']);

		$this->assertFalse($article->isFeatured());

		$rowProperty->setValue($article, ['id' => 999, 'featured' => '1']);

		$this->assertTrue($article->isFeatured());

		$rowProperty->setValue($article, ['id' => 999, 'featured' => 1]);

		$this->assertTrue($article->isFeatured());
	}
}
