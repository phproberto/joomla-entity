<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\Column;
use Phproberto\Joomla\Entity\Users\UserGroup;

/**
 * UserGroup entity tests.
 *
 * @since   1.1.0
 */
class UserGroupTest extends \TestCaseDatabase
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
		UserGroup::clearAll();

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
		$dataSet->addTable('jos_users', JPATH_TEST_DATABASE . '/jos_users.csv');
		$dataSet->addTable('jos_usergroups', JPATH_TEST_DATABASE . '/jos_usergroups.csv');
		$dataSet->addTable('jos_user_usergroup_map', JPATH_TEST_DATABASE . '/jos_user_usergroup_map.csv');

		return $dataSet;
	}

	/**
	 * loadUsers returns empty collection for entities without id.
	 *
	 * @return  void
	 */
	public function testLoadUsersReturnsEmptyCollectionForEntitiesWithoutId()
	{
		$entity = new UserGroup;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadUsers');
		$method->setAccessible(true);

		$this->assertEquals(new Collection, $method->invoke($entity));
	}

	/**
	 * loadUsers returns correct collection for entities with id.
	 *
	 * @return  void
	 */
	public function testLoadUsersReturnsCorrectCollectionForEntitiesWithId()
	{
		$users = array(
			333 => array('id' => 333, 'name' => 'Héctor Tilla'),
			666 => array('id' => 666, 'name' => 'Carmelo Cotón'),
			999 => array('id' => 999, 'name' => 'Ricardo Borriquero')
		);

		$items = array(
			(object) $users[666],
			(object) $users[999]
		);

		$usersModel = $this->getMockBuilder('UsersModelMock')
			->disableOriginalConstructor()
			->setMethods(array('getItems'))
			->getMock();

		$usersModel->expects($this->once())
			->method('getItems')
			->willReturn($items);

		$entity = $this->getMockBuilder(UserGroup::class)
			->setMethods(array('usersModel'))
			->getMock();

		$entity->expects($this->once())
			->method('usersModel')
			->willReturn($usersModel);

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 333);

		$method = $reflection->getMethod('loadUsers');
		$method->setAccessible(true);

		$user666 = new User(666);
		$user666->bind($users[666]);
		$user999 = new User(999);
		$user999->bind($users[999]);

		$expected = new Collection(array($user666, $user999));

		$this->assertEquals($expected, $method->invoke($entity));
	}

	/**
	 * table returns correct table instance.
	 *
	 * @return  void
	 */
	public function testTableReturnsCorrectTableInstance()
	{
		$entity = new UserGroup;

		$this->assertInstanceOf('JTableUsergroup', $entity->table());
	}

	/**
	 * usersModel returns correct value.
	 *
	 * @return  void
	 */
	public function testUsersModelReturnsCorrectValue()
	{
		$entity = new Usergroup;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('usersModel');
		$method->setAccessible(true);

		$model = $method->invoke($entity);

		$this->assertInstanceOf('UsersModelUsers', $model);
		$this->assertSame(null, $model->getState('filter.group_id'));

		$entity = new Usergroup(34);

		$model = $method->invoke($entity);

		$this->assertInstanceOf('UsersModelUsers', $model);
		$this->assertSame(34, $model->getState('filter.group_id'));
	}
}
