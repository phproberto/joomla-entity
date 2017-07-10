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
		$article = Article::load(1);

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
	 * getImages returns correct value.
	 *
	 * @return  void
	 */
	public function testGetImagesReturnsCorrectValue()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertSame([], $article->getImages(true));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text","image_fulltext":"images\/fulltext.png","float_fulltext":"right","image_fulltext_alt":"Alt fulltext","image_fulltext_caption":"Caption fulltext"}']);

		$this->assertSame([], $article->getImages());

		$expected = [
			'intro' => [
				'url'     => 'images/joomla_black.png',
				'float'   => 'left',
				'alt'     => 'Alt text',
				'caption' => 'Caption text'
			],
			'full' => [
				'url'     => 'images/fulltext.png',
				'float'   => 'right',
				'alt'     => 'Alt fulltext',
				'caption' => 'Caption fulltext'
			]
		];
		$images = $article->getImages(true);

		$this->assertEquals($expected, $article->getImages(true));
	}

	/**
	 * getMetadata returns data.
	 *
	 * @return  void
	 */
	public function testGetMetadataReturnsData()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '{"foo":"bar"}']);

		$expected = [
			'foo' => 'bar'
		];

		$this->assertEquals($expected, $article->getMetadata());
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
	 * getUrls returns correct information.
	 *
	 * @return  void
	 */
	public function testGetUrlsReturnsCorrectInformation()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertEquals([], $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'urls' => '']);

		$this->assertEquals([], $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'urls' => '{}']);

		$this->assertEquals([], $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'urls' => '{"urla":"","urlatext":"","targeta":"","urlb":"","urlbtext":"","targetb":"","urlc":"","urlctext":"","targetc":""}']);

		$this->assertEquals([], $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'urls' => '{"urla":"http://google.com","urlatext":"Google","targeta":"0"}']);

		$expected = [
			'a' => [
				'url'    => 'http://google.com',
				'text'   => 'Google',
				'target' => '0'
			]
		];

		$this->assertEquals($expected, $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'urls' => '{"urla":"http:\/\/google.es","urlatext":"Google","targeta":"1","urlb":"http:\/\/yahoo.com","urlbtext":"Yahoo","targetb":"0","urlc":"http://www.phproberto.com","urlctext":"Phproberto","targetc":""}']);

		$expected = [
			'a' => [
				'url'    => 'http://google.es',
				'text'   => 'Google',
				'target' => '1'
			],
			'b' => [
				'url'    => 'http://yahoo.com',
				'text'   => 'Yahoo',
				'target' => '0'
			],
			'c' => [
				'url'    => 'http://www.phproberto.com',
				'text'   => 'Phproberto'
			]
		];

		$this->assertEquals($expected, $article->getUrls());
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

		$this->assertFalse($article->isFeatured(true));

		$rowProperty->setValue($article, ['id' => 999, 'featured' => 0]);

		$this->assertFalse($article->isFeatured(true));

		$rowProperty->setValue($article, ['id' => 999, 'featured' => '0']);

		$this->assertFalse($article->isFeatured(true));

		$rowProperty->setValue($article, ['id' => 999, 'featured' => '1']);

		$this->assertTrue($article->isFeatured(true));

		$rowProperty->setValue($article, ['id' => 999, 'featured' => 1]);

		$this->assertTrue($article->isFeatured(true));
	}
}
