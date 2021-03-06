<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\ViewLevel;

/**
 * ViewLevel entity tests.
 *
 * @since   1.2.0
 */
class ViewLevelTest extends \TestCaseDatabase
{
	/**
	 * Preloaded entity for tests.
	 *
	 * @var  ViewLevel
	 */
	private $entity;

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_viewlevels', JPATH_TEST_DATABASE . '/jos_viewlevels.csv');

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadWorks()
	{
		$data = $this->entity->all();

		$this->assertTrue(is_array($data));
		$this->assertTrue($this->entity->isLoaded());
		$this->assertNotSame(0, count($data));
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

		$this->entity = ViewLevel::find(3);
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function tableReturnsExpectedInstances()
	{
		$this->assertInstanceOf(\Joomla\CMS\Table\ViewLevel::class, $this->entity->table());
		$this->assertInstanceOf(\Joomla\CMS\Table\User::class, $this->entity->table('User', 'JTable'));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		ViewLevel::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function userGroupsReturnsEmptyCollectionsForEmptyRules()
	{
		$entity = new ViewLevel;
		$userGroups = $entity->userGroups();

		$this->assertInstanceOf(Collection::class, $userGroups);

		$entity = new ViewLevel;
		$entity->bind(['id' => 333, 'title' => 'Unexisting1', 'rules' => '']);

		$this->assertSame([], $entity->userGroups()->ids());

		$entity = new ViewLevel;
		$entity->bind(['id' => 333, 'title' => 'Unexisting2', 'rules' => '[2,4]']);

		$this->assertSame([2,4], $entity->userGroups()->ids());

		$entity = new ViewLevel;
		$entity->bind(['id' => 222, 'title' => 'Unexisting2', 'rules' => null]);

		$this->assertSame([], $entity->userGroups()->ids());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function userGroupsReturnsExpectedUserGroups()
	{
		$userGroups = $this->entity->userGroups();

		$this->assertInstanceOf(Collection::class, $userGroups);
		$this->assertSame(3, $userGroups->count());
	}
}
