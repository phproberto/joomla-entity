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
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Content\Category;
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
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		CreateRootAsset::instance()->execute();

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

		Article::clearAll();
		Category::clearAll();
		User::clearActive();
	}
}
