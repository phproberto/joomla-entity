<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Messages;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Messages\Message;

/**
 * Message entity tests.
 *
 * @since   1.1.0
 */
class MessageTest extends \TestCaseDatabase
{
	/**
	 * Preloaded message for tests.
	 *
	 * @var  Message
	 */
	private $message;

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_messages', JPATH_TESTS_PHPROBERTO . '/tests/Messages/Stubs/Database/messages.csv');

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

		$this->message = Message::find(1);
	}

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$sqlFiles = [
			JPATH_TESTS_PHPROBERTO . '/tests/Messages/Stubs/Database/schema/messages.sql'
		];

		foreach ($sqlFiles as $sqlFile)
		{
			static::$driver->setQuery(
				file_get_contents($sqlFile)
			);

			static::$driver->execute();
		}

		Factory::$database = static::$driver;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		$this->message = null;
		Message::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function messageIsLoaded()
	{
		$this->assertNotSame(0, $this->message->get('user_id_from'));
	}
}
