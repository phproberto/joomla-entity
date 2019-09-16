<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\View\Traits;

defined('_JEXEC') || die;

require_once __DIR__ . '/Stubs/View/PhprobertoViewDeveloper.php';

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Tests\MVC\View\Traits\Stubs\Sample;
use Phproberto\Joomla\Entity\Tests\MVC\View\Traits\Stubs\View\SampleView;
use Phproberto\Joomla\Entity\Tests\MVC\View\Traits\Stubs\View\ArticleView;
use Phproberto\Joomla\Entity\Tests\MVC\View\Traits\Stubs\View\DeveloperView;

/**
 * HasAssociatedEntity trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasAssociatedEntityTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityClassReturnsExpectedValueForNamespacedViews()
	{
		$this->assertSame(
			Sample::class,
			(new SampleView)->entityClass()
		);

		$this->assertSame(
			'Phproberto\\Joomla\\Entity\\Tests\\MVC\\View\\Traits\\Stubs\\Entity\\Developer',
			(new DeveloperView)->entityClass()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityClassReturnsExpectedValueForLegacyControllers()
	{
		$view = new \PhprobertoViewDeveloper;

		$this->assertSame('Developer', $view->entityClass());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadEntityFromRequestReturnsExpectedEntity()
	{
		defined('JPATH_COMPONENT') || define('JPATH_COMPONENT', JPATH_BASE . '/components/com_content');

		Factory::getApplication()->input->set('id', 1);

		$controller = new ArticleView;

		$entity = $controller->loadEntityFromRequest();

		$this->assertInstanceOf(Article::class, $entity);
		$this->assertTrue($entity->isLoaded());
		$this->assertSame(1, $entity->id());
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_content', JPATH_TEST_DATABASE . '/jos_content.csv');

		return $dataSet;
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

		Article::clearAll();

		parent::tearDown();
	}
}
