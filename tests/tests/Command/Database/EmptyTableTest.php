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
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * EmptyTable tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class EmptyTableTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function implementsCommandInterface()
	{
		$command = new EmptyTable('test');

		$this->assertTrue($command instanceof CommandInterface);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function tableIsEmptied()
	{
		$db = static::$driver;
		$db->setQuery(
			'CREATE TABLE `emptytable_test` (
				`id` INTEGER PRIMARY KEY NOT NULL,
				`name` TEXT NOT NULL
			)'
		);

		$db->execute();

		$query = $db->getQuery(true)
			->insert($db->qn('emptytable_test'))
			->columns(['name'])
			->values(
				[
					$db->q('One item'),
					$db->q('Another item'),
					$db->q('Yet another item')
				]
			);

		$db->setQuery($query);
		$db->execute();

		Factory::$database = static::$driver;

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('emptytable_test'));

		$db->setQuery($query);

		$this->assertSame(3, count($db->loadObjectList()));

		$command = new EmptyTable('emptytable_test');
		$command->execute();

		$this->assertSame(0, count($db->loadObjectList()));
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
			$command = new EmptyTable('inexistent', ['db' => $db2]);
			$command->execute();
		}
		catch (\RuntimeException $e)
		{
			$error = $e->getMessage();
		}

		$this->assertSame('Error emptying DB table `inexistent`: Could not connect to MySQL server.', $error);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function customDriverIsUsed()
	{
		@unlink(dirname(JPATH_TESTS_PHPROBERTO) . '/:emptytable_test:');

		$db2 = \JDatabaseDriver::getInstance(
			[
				'driver' => 'sqlite',
				'database' => ':emptytable_test:',
				'prefix' => 'ddd'
			]
		);

		$db2->setQuery(
			'CREATE TABLE `emptytable_test2` (
				`id` INTEGER PRIMARY KEY NOT NULL,
				`name` TEXT NOT NULL
			)'
		);
		$db2->execute();

		$query = $db2->getQuery(true)
			->insert($db2->qn('emptytable_test2'))
			->columns(['name'])
			->values(
				[
					$db2->q('One item'),
					$db2->q('Another item'),
					$db2->q('Yet another item')
				]
			);

		$db2->setQuery($query);
		$db2->execute();

		$query = $db2->getQuery(true)
			->select($db2->qn('id'))
			->from($db2->qn('emptytable_test2'));

		$db2->setQuery($query);

		$this->assertSame(3, count($db2->loadObjectList()));

		$command = new EmptyTable('emptytable_test2', ['db' => $db2]);
		$command->execute();

		$this->assertSame(0, count($db2->loadObjectList()));

		$db2->disconnect();

		unlink(dirname(JPATH_TESTS_PHPROBERTO) . '/:emptytable_test:');
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
