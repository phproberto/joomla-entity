<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits;

defined('_JEXEC') || die;

require_once __DIR__ . '/Stubs/PhprobertoControllerDeveloper.php';

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Content\Entity\Article;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\Sample;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\Entity\Developer;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\ArticleController;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\Controller\SampleController;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\ControllerWithAssociatedEntity;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\Controller\DeveloperController;

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
	public function entityClassReturnsExpectedValueForNamespacedControllers()
	{
		$this->assertSame(
			Sample::class,
			(new SampleController)->entityClass()
		);

		$this->assertSame(
			'Phproberto\\Joomla\\Entity\\Tests\\MVC\\Controller\\Traits\\Stubs\\Entity\\Developer',
			(new DeveloperController)->entityClass()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityClassReturnsExpectedValueForLegacyControllers()
	{
		$controller = new \PhprobertoControllerDeveloper;

		$this->assertSame('Developer', $controller->entityClass());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityClassOrFailThrowsExceptionForUnexistingClass()
	{
		$error = '';

		try
		{
			(new DeveloperController)->entityClassOrFail();
		}
		catch (\Exception $e)
		{
			$error = $e->getMessage();
		}

		$this->assertTrue(substr_count($error, 'Entity class not found ') > 0);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityInstanceReturnsExistingInstance()
	{
		$entity = (new SampleController)->entityInstance();

		$this->assertInstanceOf(Sample::class, $entity);
		$this->assertFalse($entity->hasId());

		$entity = (new SampleController)->entityInstance(12);

		$this->assertInstanceOf(Sample::class, $entity);
		$this->assertSame(12, $entity->id());
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

		$controller = new ArticleController;

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
