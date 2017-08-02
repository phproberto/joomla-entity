<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Users\User;

/**
 * User entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class UserTest extends \TestCaseDatabase
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
		User::clearAllInstances();

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

		return $dataSet;
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

		$this->assertEquals(User::instance(42), User::active());
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
	 * joomlaUser throws exception for no primary key.
	 *
	 * @return  void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testJoomlaUserThrowsExceptionForNoPrimaryKey()
	{
		$user = new User;

		$joomlaUser = $user->joomlaUser();
	}

	/**
	 * joomlaUser throws exception for non-existing user.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testJoomlaUserThrowsExceptionForNonExistinUser()
	{
		$user = new User(999);

		$joomlaUser = $user->joomlaUser();
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

		$rowProperty->setValue($user, array('id' => 999));

		$this->assertEquals(new Registry, $user->params());

		$rowProperty->setValue($user, array('id' => 999, 'params' => '{"timezone":"Europe\/Madrid"}'));

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
}
