<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Acl\Acl;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\UserGroup;
use Phproberto\Joomla\Entity\Core\Column as CoreColumn;

/**
 * User entity tests.
 *
 * @since   1.1.0
 */
class UserTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function addToUserGroupWorks()
	{
		$user = User::find(42);

		$this->assertEquals([8], $user->userGroupsIds());
		$this->assertEquals([8], $user->userGroups()->ids());

		$user->addToUserGroup(8);

		$this->assertEquals([8], $user->userGroupsIds());
		$this->assertEquals([8], $user->userGroups()->ids());

		$user->addToUserGroup(5);

		$this->assertEquals([5, 8], $user->userGroupsIds());
		$this->assertEquals([5, 8], $user->userGroups()->ids());

		$user->addToUserGroup(1);

		$this->assertEquals([1, 5, 8], $user->userGroupsIds());
		$this->assertEquals([1, 5, 8], $user->userGroups()->ids());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function addtoUserGroupsWorks()
	{
		$user = User::find(42);

		$this->assertEquals([8], $user->userGroupsIds());
		$this->assertEquals([8], $user->userGroups()->ids());

		$user->addToUserGroups([]);

		$this->assertEquals([8], $user->userGroupsIds());
		$this->assertEquals([8], $user->userGroups()->ids());

		$user->addToUserGroups([8]);

		$this->assertEquals([8], $user->userGroupsIds());
		$this->assertEquals([8], $user->userGroups()->ids());

		$user->addToUserGroups([5,1]);

		$this->assertEquals([1, 5, 8], $user->userGroupsIds());
		$this->assertEquals([1, 5, 8], $user->userGroups()->ids());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function removeFromUserGroupWorks()
	{
		$user = User::find(42);
		$user->addToUserGroups([5, 1, 6]);

		$this->assertEquals([1, 5, 6, 8], $user->userGroupsIds());
		$this->assertEquals([1, 5, 6, 8], $user->userGroups()->ids());

		$user->removeFromUserGroup(5);

		$this->assertEquals([1, 6, 8], $user->userGroupsIds());
		$this->assertEquals([1, 6, 8], $user->userGroups()->ids());

		$user->removeFromUserGroup(5);

		$this->assertEquals([1, 6, 8], $user->userGroupsIds());
		$this->assertEquals([1, 6, 8], $user->userGroups()->ids());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function removeFromUserGroupsWorks()
	{
		$user = User::find(42);
		$user->addToUserGroups([5, 1, 6]);

		$this->assertEquals([1, 5, 6, 8], $user->userGroupsIds());
		$this->assertEquals([1, 5, 6, 8], $user->userGroups()->ids());

		$user->removeFromUserGroups([5,1,8]);

		$this->assertEquals([6], $user->userGroupsIds());
		$this->assertEquals([6], $user->userGroups()->ids());

		$user->removeFromUserGroups([4]);

		$this->assertEquals([6], $user->userGroupsIds());
		$this->assertEquals([6], $user->userGroups()->ids());

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
		User::clearAll();

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
		$dataSet->addTable('jos_viewlevels', JPATH_TEST_DATABASE . '/jos_viewlevels.csv');

		return $dataSet;
	}

	/**
	 * Acl can be retrieved.
	 *
	 * @return  void
	 */
	public function testAclCanBeRetrieved()
	{
		$entity = new User(666);
		$user = new User(999);

		$acl = $entity->acl($user);

		$reflection = new \ReflectionClass($acl);
		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);

		$this->assertInstanceOf(Acl::class, $acl);
		$this->assertSame($user, $userProperty->getValue($acl));
		$this->assertSame($entity, $entityProperty->getValue($acl));
	}

	/**
	 * active returns correct value
	 *
	 * @return  void
	 */
	public function testActiveReturnsCorrectValue()
	{
		$this->assertEquals(new User, User::active());

		$mockSession = $this->getMockBuilder('JSession')
			->setMethods(array('_start', 'get'))
			->getMock();

		$mockSession->expects($this->once())
			->method('get')
			->will($this->returnValue(new \JUser(42)));

		\JFactory::$session = $mockSession;

		$this->assertEquals(User::find(42), User::active());
	}

	/**
	 * authorise returns true for root.
	 *
	 * @return  void
	 */
	public function testAuthoriseReturnsTrueForRoot()
	{
		$user = $this->getMockBuilder(User::class)
			->setMethods(array('isRoot', 'joomlaUser'))
			->getMock();

		$user->expects($this->once())
			->method('isRoot')
			->willReturn(true);

		$user->expects($this->exactly(0))
			->method('joomlaUser')
			->willReturn(null);

		$this->assertTrue($user->authorise('sample.action'));
	}

	/**
	 * authorise returns juser authorise.
	 *
	 * @return  void
	 */
	public function testAuthoriseReturnsJUserAuthorise()
	{
		$joomlaUser = $this->getMockBuilder('MockeJoomlaUser')
			->setMethods(array('authorise'))
			->getMock();

		$joomlaUser->expects($this->once())
			->method('authorise')
			->willReturn(true);

		$user = $this->getMockBuilder(User::class)
			->setMethods(array('isRoot', 'joomlaUser'))
			->getMock();

		$user->expects($this->once())
			->method('isRoot')
			->willReturn(false);

		$user->expects($this->once())
			->method('joomlaUser')
			->willReturn($joomlaUser);

		$this->assertTrue($user->authorise('sample.action'));
	}

	/**
	 * authorise will return false if joomlaUser throws exception.
	 *
	 * @return  void
	 */
	public function testAuthoriseWillReturnFalseIfJoomlaUserThrowsException()
	{
		$joomlaUser = $this->getMockBuilder('MockeJoomlaUser')
			->setMethods(array('authorise'))
			->getMock();

		$joomlaUser->expects($this->once())
			->method('authorise')
			->will($this->throwException(new \Exception('User failure')));

		$user = $this->getMockBuilder(User::class)
			->setMethods(array('isRoot', 'joomlaUser'))
			->getMock();

		$user->expects($this->once())
			->method('isRoot')
			->willReturn(false);

		$user->expects($this->once())
			->method('joomlaUser')
			->willReturn($joomlaUser);

		$this->assertFalse($user->authorise('sample.action'));
	}

	/**
	 * entity instance can be retrieved.
	 *
	 * @return  void
	 */
	public function testEntityInstanceRetrieved()
	{
		$user = new User;

		$this->assertInstanceOf(User::class, $user);

		$user = new User(12);

		$this->assertInstanceOf(User::class, $user);
	}

	/**
	 * isActivated returns correct value.
	 *
	 * @return  void
	 */
	public function testIsActivatedReturnsCorrectValue()
	{
		$user = new User;

		$this->assertSame(false, $user->isActivated());

		$user = new User(999);

		$reflection = new \ReflectionClass($user);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($user, array('id' => 999, 'activation' => ''));

		$this->assertSame(true, $user->isActivated());

		$rowProperty->setValue($user, array('id' => 999, 'activation' => 'qweqweqweqwe213412123'));

		$this->assertSame(false, $user->isActivated());

		$rowProperty->setValue($user, array('id' => 999, 'activation' => '0'));

		$this->assertSame(true, $user->isActivated());
	}

	/**
	 * isActive returns correct value.
	 *
	 * @return  void
	 */
	public function testIsActiveReturnsCorrectValue()
	{
		// User is blocked
		$user = $this->getMockBuilder(User::class)
			->setMethods(array('isBlocked'))
			->getMock();

		$user->expects($this->once())
			->method('isBlocked')
			->willReturn(true);

		$this->assertSame(false, $user->isActive());

		// User not blocked but not activated
		$user = $this->getMockBuilder(User::class)
			->setMethods(array('isBlocked', 'isActivated'))
			->getMock();

		$user->expects($this->once())
			->method('isBlocked')
			->willReturn(false);

		$user->expects($this->once())
			->method('isActivated')
			->willReturn(false);

		$this->assertSame(false, $user->isActive());

		// User not blocked and activated
		$user = $this->getMockBuilder(User::class)
			->setMethods(array('isBlocked', 'isActivated'))
			->getMock();

		$user->expects($this->once())
			->method('isBlocked')
			->willReturn(false);

		$user->expects($this->once())
			->method('isActivated')
			->willReturn(true);

		$this->assertSame(true, $user->isActive());
	}

	/**
	 * isBlocked returns correct value.
	 *
	 * @return  void
	 */
	public function testIsBlockedReturnsCorrectValue()
	{
		$user = new User;

		$this->assertSame(false, $user->isBlocked());

		$user = new User(999);

		$reflection = new \ReflectionClass($user);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($user, array('id' => 999, 'block' => '1'));

		$this->assertSame(true, $user->isBlocked());

		$rowProperty->setValue($user, array('id' => 999, 'block' => '0'));

		$this->assertSame(false, $user->isBlocked());
	}

	/**
	 * canAdmin returns true for root.
	 *
	 * @return  void
	 */
	public function testCanAdminReturnsTrueForRoot()
	{
		$user = $this->getMockBuilder(User::class)
			->setMethods(array('isRoot'))
			->getMock();

		$user->expects($this->once())
			->method('isRoot')
			->willReturn(true);

		$this->assertTrue($user->canAdmin('com_phproberto'));
	}

	/**
	 * canAdmin returns authorise result.
	 *
	 * @return  void
	 */
	public function testCanAdminReturnsAuthoriseResult()
	{
		$user = $this->getMockBuilder(User::class)
			->setMethods(array('isRoot', 'authorise'))
			->getMock();

		$user->expects($this->exactly(3))
			->method('isRoot')
			->willReturn(false);

		$user->method('authorise')
			->with($this->equalTo('core.admin'), $this->equalTo('com_phproberto'))
			->will($this->onConsecutiveCalls(false, true, false));

		$this->assertFalse($user->canAdmin('com_phproberto'));
		$this->assertTrue($user->canAdmin('com_phproberto'));
		$this->assertFalse($user->canAdmin('com_phproberto'));
	}

	/**
	 * getAuthorisedViewLevels returns correct values.
	 *
	 * @return  void
	 */
	public function testGetAuthorisedViewLevelsReturnsCorrectValue()
	{
		$joomlaUser = $this->getMockBuilder('MockeJoomlaUser')
			->setMethods(array('getAuthorisedViewLevels'))
			->getMock();

		$joomlaUser->method('getAuthorisedViewLevels')
			->will(
				$this->onConsecutiveCalls(
					array(1, 1, 5),
					array(1, 12, 25),
					array(11, 21, 21)
				)
			);

		$user = $this->getMockBuilder(User::class)
			->setMethods(array('joomlaUser'))
			->getMock();

		$user->method('joomlaUser')
			->willReturn($joomlaUser);

		$this->assertSame(array(1, 5), $user->getAuthorisedViewLevels());
		$this->assertSame(array(1, 12, 25), $user->getAuthorisedViewLevels());
		$this->assertSame(array(11, 21), $user->getAuthorisedViewLevels());
	}

	/**
	 * getAuthorisedViewLevels returns empty array on joomlaUser exception.
	 *
	 * @return  void
	 */
	public function testgetAuthorisedViewLevelsReturnsEmptyArrayOnJoomlaUserException()
	{
		$user = $this->getMockBuilder(User::class)
			->setMethods(array('joomlaUser'))
			->getMock();

		$user->method('joomlaUser')
			->will($this->throwException(new \Exception('User failure')));

		$this->assertSame(array(), $user->getAuthorisedViewLevels());
	}

	/**
	 * juser returns correct instance.
	 *
	 * @return  void
	 */
	public function testJoomlaserReturnsCorrectInstance()
	{
		$user = new User(42);

		$joomlaUser = $user->joomlaUser();

		$this->assertInstanceOf(\JUser::class, $joomlaUser);
		$this->assertSame(42, (int) $joomlaUser->get('id'));
	}

	/**
	 * isGuest returns true for non-loaded user.
	 *
	 * @return  void
	 */
	public function testIsGuestReturnsCorrectValue()
	{
		$user = new User;

		$this->assertSame(true, $user->isGuest());

		$user = new User(42);

		$this->assertSame(false, $user->isGuest());
	}

	/**
	 * isRoot returns cached value.
	 *
	 * @return  void
	 */
	public function testIsRootReturnsCachedValue()
	{
		$user = new User(999);

		$reflection = new \ReflectionClass($user);
		$isRootProperty = $reflection->getProperty('isRoot');
		$isRootProperty->setAccessible(true);

		$isRootProperty->setValue($user, true);
		$this->assertTrue($user->isRoot());

		$isRootProperty->setValue($user, false);
		$this->assertFalse($user->isRoot());

		$isRootProperty->setValue($user, true);
		$this->assertTrue($user->isRoot());
	}

	/**
	 * isRoot returns authorise result.
	 *
	 * @return  void
	 */
	public function testIsRootReturnsAuthoriseResult()
	{
		$joomlaUser = $this->getMockBuilder('MockeJoomlaUser')
			->setMethods(array('authorise'))
			->getMock();

		$joomlaUser->method('authorise')
			->with('core.admin')
			->will($this->onConsecutiveCalls(true, false, true));

		$user = $this->getMockBuilder(User::class)
			->setMethods(array('joomlaUser'))
			->getMock();

		$user->expects($this->exactly(3))
			->method('joomlaUser')
			->willReturn($joomlaUser);

		$reflection = new \ReflectionClass($user);
		$isRootProperty = $reflection->getProperty('isRoot');
		$isRootProperty->setAccessible(true);

		$isRootProperty->setValue($user, null);
		$this->assertTrue($user->isRoot());

		$isRootProperty->setValue($user, null);
		$this->assertFalse($user->isRoot());

		$isRootProperty->setValue($user, null);
		$this->assertTrue($user->isRoot());
	}

	/**
	 * joomlaUser returns guest for missing primary key.
	 *
	 * @return  void
	 */
	public function testJoomlaUserReturnsGuestForMissingPrimaryKey()
	{
		$user = new User;

		$joomlaUser = $user->joomlaUser();

		$this->assertInstanceOf(\JUser::class, $joomlaUser);
		$this->assertSame(0, (int) $joomlaUser->get('id'));
		$this->assertSame(1, $joomlaUser->get('guest'));
	}

	/**
	 * joomlaUser throws exception for non-existing user.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testJoomlaUserThrowsExceptionForNonExistingUser()
	{
		$user = new User(999);

		$joomlaUser = $user->joomlaUser();
	}

	/**
	 * loadUserGroups returns empty collection for entities without id.
	 *
	 * @return  void
	 */
	public function testLoadUserGroupsReturnsEmptyCollectionForEntitiesWithoutId()
	{
		$entity = new User;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadUserGroups');
		$method->setAccessible(true);

		$this->assertEquals(new Collection, $method->invoke($entity));
	}

	/**
	 * loadUserGroupss returns correct collection for entities with id.
	 *
	 * @return  void
	 */
	public function testLoadUserGroupsReturnsCorrectCollectionForEntitiesWithId()
	{
		$relationships = array(
			42 => array(8),
			43 => array(5),
			44 => array(6)
		);

		foreach ($relationships as $userId => $groupsIds)
		{
			$user = new User($userId);

			$reflection = new \ReflectionClass($user);

			$method = $reflection->getMethod('loadUserGroups');
			$method->setAccessible(true);

			$expected = new Collection(
				array_map(
					function ($groupId)
					{
						return UserGroup::load($groupId);
					},
					$groupsIds
				)
			);

			$this->assertEquals($expected, $method->invoke($user));
		}
	}

	/**
	 * params can be retrieved.
	 *
	 * @return  void
	 */
	public function testParamsRetrieved()
	{
		$user = new User(999);

		$reflection = new \ReflectionClass($user);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($user, array('id' => 999, CoreColumn::PARAMS => ''));

		$this->assertEquals(new Registry, $user->params());

		$user = new User(666);

		$rowProperty->setValue($user, array('id' => 666, CoreColumn::PARAMS => '{"timezone":"Europe\/Madrid"}'));

		$this->assertEquals(new Registry(array('timezone' => 'Europe/Madrid')), $user->params(true));
	}

	/**
	 * table returns correct table instance.
	 *
	 * @return  void
	 */
	public function testTableReturnsCorrectTableInstance()
	{
		$user = new User;

		$this->assertInstanceOf('JTableUser', $user->table());
	}

	/**
	 * userGroupsIds() data provider
	 *
	 * @return  array
	 */
	public function userGroupsIdsDataProvider()
	{
		return [
			[[null, '', ' ', '8', 'test', 04], [8, 4]],
			[[], []],
			[null, []]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  userGroupsIdsDataProvider
	 *
	 * @return void
	 */
	public function userGroupsIdsReturnsExpectedValues($provided, $expected)
	{
		$user = new User;
		$user->bind(
			[
				'id'     => 999,
				'name'   => 'Roberto',
				'groups' => $provided
			]
		);

		$this->assertSame($expected, $user->userGroupsIds());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function viewLevelsReturnsExpectedCollection()
	{
		$user = new User;
		$viewLevels = $user->viewLevels();

		$this->assertInstanceOf(Collection::class, $viewLevels);
		$this->assertSame([1], $viewLevels->ids());

		$user = $this->getMockBuilder(User::class)
			->setMethods(array('getAuthorisedViewLevels'))
			->getMock();

		$user->expects($this->once())
			->method('getAuthorisedViewLevels')
			->willReturn([2,8]);

		$this->assertSame([2,8], $user->viewLevels()->ids());
	}
}
