<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content\Model;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Core\Asset;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Content\Category;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;
use Phproberto\Joomla\Entity\Content\Model\ArticlesModel;
use Phproberto\Joomla\Entity\Core\Command\CreateRootAsset;

/**
 * ArticlesModel tests.
 *
 * @since   __DEPLOY_VERSION_
 */
class ArticlesModelTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function articlesAreReturned()
	{
		$category1 = Category::create(
			[
				'title' => 'My category'
			]
		);

		$this->assertTrue($category1->isLoaded());

		$article1 = Article::create(
			[
				'title' => 'Sample article 1',
				'catid' => $category1->id()
			]
		);

		$this->assertTrue($article1->isLoaded());

		$model = new ArticlesModel;

		$items = $model->getItems();

		$this->assertTrue(count($items) > 0);
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

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function idAndNotIdFiltersAreApplied()
	{
		$article1 = Article::create(
			[
				'title' => 'Sample article 1'
			]
		);

		$this->assertTrue($article1->isLoaded());

		$article2 = Article::create(
			[
				'title' => 'Sample article 2'
			]
		);

		$this->assertTrue($article2->isLoaded());

		$model = new ArticlesModel;

		$items = $model->search(['filter.id' => [$article1->id()]]);

		$this->assertTrue(1 === count($items));
		$this->assertSame($article1->id(), (int) $items[0]->id);

		$items = $model->search(['filter.not_id' => [$article1->id()]]);

		$this->assertTrue(1 === count($items));
		$this->assertSame($article2->id(), (int) $items[0]->id);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function stateAndNotStateFiltersAreApplied()
	{
		$article1 = Article::create(
			[
				'title' => 'Sample article 1',
				'state' => 1
			]
		);

		$this->assertTrue($article1->isLoaded());

		$article2 = Article::create(
			[
				'title' => 'Sample article 2',
				'state' => 0
			]
		);

		$this->assertTrue($article2->isLoaded());

		$model = new ArticlesModel;

		$items = $model->search(['filter.state' => [1]]);

		$this->assertTrue(1 === count($items));
		$this->assertSame($article1->id(), (int) $items[0]->id);

		$items = $model->search(['filter.not_state' => [1]]);

		$this->assertTrue(1 === count($items));
		$this->assertSame($article2->id(), (int) $items[0]->id);

		$items = $model->search(['filter.state' => [0,1]]);

		$this->assertTrue(2 === count($items));
	}

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

		Factory::$session     = $this->getMockSession();
		Factory::$config      = $this->getMockConfig();
		Factory::$application = $this->getMockCmsApp();
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

		EmptyTable::instance(['#__assets'])->execute();
		EmptyTable::instance(['#__categories'])->execute();
		EmptyTable::instance(['#__content'])->execute();

		Asset::clearAll();
		Article::clearAll();
		Category::clearAll();
	}
}
