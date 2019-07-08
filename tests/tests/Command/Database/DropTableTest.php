<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Command\Database;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Command\Database\DropTable;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * DropTabletests.
 *
 * @since   __DEPLOY_VERSION__
 */
class DropTableTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function implementsCommandInterface()
	{
		$command = new DropTable('test');

		$this->assertTrue($command instanceof CommandInterface);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function tableIsDropped()
	{
		static::$driver->setQuery(
			'CREATE TABLE `droptable_test` (
				`id` INTEGER PRIMARY KEY NOT NULL
			)'
		);

		static::$driver->execute();
		Factory::$database = static::$driver;
		$db = Factory::getDbo();

		$this->assertTrue(in_array('droptable_test', $db->getTableList(), true));

		$command = new DropTable('droptable_test');
		$command->execute();

		$this->assertFalse(in_array('droptable_test', $db->getTableList(), true));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function exceptionIsThrownOnError()
	{
		$db2 = \JDatabaseDriver::getInstance(
			[
				'driver' => 'mysqli',
				'database' => 'test',
				'prefix' => 'ddd'
			]
		);

		$error = '';

		try
		{
			$command = new DropTable('inexistent', ['db' => $db2]);
			$command->execute();
		}
		catch (\RuntimeException $e)
		{
			$error = $e->getMessage();
		}

		$this->assertSame('Error dropping DB table `inexistent`: Could not connect to MySQL server.', $error);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function customDriverIsUsed()
	{
		@unlink(dirname(JPATH_TESTS_PHPROBERTO) . '/:droptable_test:');

		$db2 = \JDatabaseDriver::getInstance(
			[
				'driver' => 'sqlite',
				'database' => ':droptable_test:',
				'prefix' => 'ddd'
			]
		);

		$db2->setQuery(
			'CREATE TABLE `droptable_test2` (
				`id` INTEGER PRIMARY KEY NOT NULL
			)'
		);
		$db2->execute();

		$this->assertTrue(in_array('droptable_test2', $db2->getTableList(), true));

		$command = new DropTable('droptable_test2', ['db' => $db2]);
		$command->execute();

		$this->assertFalse(in_array('droptable_test2', $db2->getTableList(), true));

		$db2->disconnect();

		unlink(dirname(JPATH_TESTS_PHPROBERTO) . '/:droptable_test:');
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

		Factory::$config      = $this->getMockConfig();
		Factory::$application = $this->getMockCmsApp();
		Factory::$session     = $this->getMockSession();
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
}
