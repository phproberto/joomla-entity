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
}
