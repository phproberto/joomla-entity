<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Content\Category;

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
		Article::clearAllInstances();

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
	 * access retrieved.
	 *
	 * @return  void
	 */
	public function testAccessRetrieved()
	{
		$article = $this->getMockBuilder(Article::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$article->method('columnAlias')
			->willReturn('access');

		$reflection = new \ReflectionClass($article);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($article, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($article, array('id' => 999, 'access' => 0));

		$this->assertSame(0, $article->access());

		$rowProperty->setValue($article, array('id' => 999, 'access' => 1));

		$this->assertSame(1, $article->access());
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
		$article = Article::instance(1);

		$asset = $article->getAsset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertNotSame(0, $asset->id());
	}

	/**
	 * author retrieved.
	 *
	 * @return  void
	 */
	public function testAuthorRetrieved()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, array('id' => 999, 'created_by' => 666));

		$this->assertSame(User::instance(666), $article->author());
	}

	/**
	 * editor retrieved.
	 *
	 * @return  void
	 */
	public function testEditorRetrieved()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, array('id' => 999, 'modified_by' => 666));

		$this->assertSame(User::instance(666), $article->editor());
	}

	/**
	 * Category can be retrieved.
	 *
	 * @return  void
	 */
	public function testCategoryCanBeRetrieved()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($article, array('id' => 999));

		$this->assertEquals(new Category, $article->category());

		$rowProperty->setValue($article, array('id' => 999, 'catid' => 666));

		// No reload = same category
		$this->assertEquals(new Category, $article->category());
		$this->assertEquals(Category::instance(666), $article->category(true));
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

		$rowProperty->setValue($article, array('id' => 999));

		$this->assertSame(array(), $article->getImages(true));

		$rowProperty->setValue(
			$article,
			array(
				'id' => 999,
				'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text","image_fulltext":"images\/fulltext.png","float_fulltext":"right","image_fulltext_alt":"Alt fulltext","image_fulltext_caption":"Caption fulltext"}'
			)
		);

		$this->assertSame(array(), $article->getImages());

		$expected = array(
			'intro' => array(
				'url'     => 'images/joomla_black.png',
				'float'   => 'left',
				'alt'     => 'Alt text',
				'caption' => 'Caption text'
			),
			'full' => array(
				'url'     => 'images/fulltext.png',
				'float'   => 'right',
				'alt'     => 'Alt fulltext',
				'caption' => 'Caption fulltext'
			)
		);
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

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => '{"foo":"bar"}'));

		$expected = array(
			'foo' => 'bar'
		);

		$this->assertEquals($expected, $article->metadata());
	}

	/**
	 * getArticlesModel returns correct value.
	 *
	 * @return  void
	 */
	public function testGetArticlesModelReturnsCorrectValue()
	{
		$article = new Article;

		$reflection = new \ReflectionClass($article);
		$method = $reflection->getMethod('getArticlesModel');
		$method->setAccessible(true);

		$model = $method->invoke($article);

		$this->assertInstanceOf('ContentModelArticles', $model);
		$this->assertSame(null, $model->getState('filter.article_id'));
		$this->assertEquals(new Registry, $model->getState('params'));

		$article = new Article;
		$params = new Registry(array('foo' => 'var'));

		$model = $method->invoke($article, array(
				'filter.article_id' => array(34),
				'params'            => $params
			)
		);

		$this->assertInstanceOf('ContentModelArticles', $model);
		$this->assertSame(array(34), $model->getState('filter.article_id'));
		$this->assertEquals($params, $model->getState('params'));
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

		$rowProperty->setValue($article, array('id' => 999));

		$this->assertEquals(array(), $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, array('id' => 999, 'urls' => ''));

		$this->assertEquals(array(), $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, array('id' => 999, 'urls' => '{}'));

		$this->assertEquals(array(), $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue(
			$article,
			array(
				'id' => 999,
				'urls' => '{"urla":"","urlatext":"","targeta":"","urlb":"","urlbtext":"","targetb":"","urlc":"","urlctext":"","targetc":""}'
			)
		);

		$this->assertEquals(array(), $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue(
			$article,
			array(
				'id' => 999,
				'urls' => '{"urla":"http://google.com","urlatext":"Google","targeta":"0"}'
			)
		);

		$expected = array(
			'a' => array(
				'url'    => 'http://google.com',
				'text'   => 'Google',
				'target' => '0'
			)
		);

		$this->assertEquals($expected, $article->getUrls());

		$article = Article::freshInstance(999);
		$rowProperty->setValue(
			$article,
			array(
				'id' => 999,
				'urls' => '{"urla":"http:\/\/google.es","urlatext":"Google","targeta":"1","urlb":"http:\/\/yahoo.com","urlbtext":"Yahoo","targetb":"0","urlc":"http://www.phproberto.com","urlctext":"Phproberto","targetc":""}'
			)
		);

		$expected = array(
			'a' => array(
				'url'    => 'http://google.es',
				'text'   => 'Google',
				'target' => '1'
			),
			'b' => array(
				'url'    => 'http://yahoo.com',
				'text'   => 'Yahoo',
				'target' => '0'
			),
			'c' => array(
				'url'    => 'http://www.phproberto.com',
				'text'   => 'Phproberto'
			)
		);

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

		$rowProperty->setValue($article, array('id' => 999, 'images' => ''));

		$this->assertFalse($article->hasFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue(
			$article,
			array(
				'id' => 999,
				'images' => '{"image_fulltext":"images\/joomla_black.png"}'
			)
		);

		$this->assertTrue($article->hasFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, array('id' => 999));

		$this->assertFalse($article->hasFullTextImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue(
			$article,
			array(
				'id' => 999,
				'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}'
			)
		);

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

		$rowProperty->setValue($article, array('id' => 999, 'images' => ''));

		$this->assertFalse($article->hasIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue(
			$article,
			array(
				'id' => 999,
				'images' => '{"image_intro":"images\/joomla_black.png"}'
			)
		);

		$this->assertTrue($article->hasIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue($article, array('id' => 999));

		$this->assertFalse($article->hasIntroImage());

		$article = Article::freshInstance(999);
		$rowProperty->setValue(
			$article,
			array(
				'id' => 999,
				'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}'
			)
		);

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

		$rowProperty->setValue($article, array('id' => 999));

		$this->assertFalse($article->isFeatured(true));

		$rowProperty->setValue($article, array('id' => 999, 'featured' => 0));

		$this->assertFalse($article->isFeatured(true));

		$rowProperty->setValue($article, array('id' => 999, 'featured' => '0'));

		$this->assertFalse($article->isFeatured(true));

		$rowProperty->setValue($article, array('id' => 999, 'featured' => '1'));

		$this->assertTrue($article->isFeatured(true));

		$rowProperty->setValue($article, array('id' => 999, 'featured' => 1));

		$this->assertTrue($article->isFeatured(true));
	}

	/**
	 * loadTags returns empty collection for missing id.
	 *
	 * @return  void
	 */
	public function testLoadTagsReturnsEmptyCollectionForMissingId()
	{
		$article = new Article;

		$reflection = new \ReflectionClass($article);
		$method = $reflection->getMethod('loadTags');
		$method->setAccessible(true);

		$this->assertEquals(new Collection, $method->invoke($article));
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

		$entity = $this->getMockBuilder(Article::class)
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

	/**
	 * loadTranslations returns empty collection for missing id.
	 *
	 * @return  void
	 */
	public function testLoadTranslationsReturnsEmptyCollectionForMissingId()
	{
		$article = new Article;

		$reflection = new \ReflectionClass($article);
		$method = $reflection->getMethod('loadTranslations');
		$method->setAccessible(true);

		$this->assertEquals(new Collection, $method->invoke($article));
	}

	/**
	 * loadTranslations returns empty collection for missing translations.
	 *
	 * @return  void
	 */
	public function testLoadTranslationsReturnsEmptyCollectionForMissingTranslations()
	{
		$article = $this->getMockBuilder(Article::class)
			->setMethods(array('associations'))
			->getMock();

		$article
			->expects($this->once())
			->method('associations')
			->willReturn(array());

		$reflection = new \ReflectionClass($article);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($article, 333);

		$method = $reflection->getMethod('loadTranslations');
		$method->setAccessible(true);

		$this->assertEquals(new Collection, $method->invoke($article));
	}

	/**
	 * loadTranslations loads correct data for existing id.
	 *
	 * @return  void
	 */
	public function testLoadTranslationsReturnsCorrectDataForExistingId()
	{
		$articles = array(
			333 => array('id' => 333, 'title' => 'Source article', 'language' => 'en-GB'),
			666 => array('id' => 666, 'title' => 'Spanish translation', 'language' => 'es-ES'),
			999 => array('id' => 999, 'title' => 'Brasialian translation', 'language' => 'pt-BR')

		);

		$associations = array(
			'es-ES' => new Article(666),
			'pt-BR' => new Article(999)
		);

		$article = $this->getMockBuilder(Article::class)
			->setMethods(array('associations', 'getArticlesModel'))
			->getMock();

		$article
			->expects($this->once())
			->method('associations')
			->willReturn($associations);

		$getItemsResponse = array(
			(object) $articles[666],
			(object) $articles[999]
		);

		$article
			->expects($this->once())
			->method('getArticlesModel')
			->willReturn($this->getArticlesModelMock($getItemsResponse));

		$reflection = new \ReflectionClass($article);

		$method = $reflection->getMethod('loadTranslations');
		$method->setAccessible(true);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($article, 333);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($article, $articles[333]);

		$spanish = new Article(666);
		$rowProperty->setValue($spanish, $articles[666]);

		$brasilian = new Article(999);
		$rowProperty->setValue($brasilian, $articles[999]);

		$expectedCollection = new Collection(array($spanish, $brasilian));

		// We can only compare ids because mocked article returns a collection of mocked articles
		$this->assertEquals($expectedCollection->ids(), $method->invoke($article)->ids());
	}

	/**
	 * params returns parameters.
	 *
	 * @return  void
	 */
	public function testParamsReturnsParameters()
	{
		$article = new Article(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, array('id' => 999, 'attribs' => '{"foo":"var"}'));

		$this->assertEquals(new Registry(array('foo' => 'var')), $article->params(true));
	}

	/**
	 * table returns correct table instance.
	 *
	 * @return  void
	 */
	public function testTableReturnsCorrectTableInstance()
	{
		$article = new Article;

		$this->assertInstanceOf('JTableContent', $article->table());
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
}
