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
use Phproberto\Joomla\Entity\Command\Database\ExecuteSQLFile;
use Phproberto\Joomla\Entity\Command\FileSystem\DeleteFolderRecursively;

/**
 * ExecuteSQLFile tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ExecuteSQLFileTest extends \TestCaseDatabase
{
	/**
	 * Create the test SQL file.
	 *
	 * @return  string
	 */
	private function createTestSQLFile()
	{
		$tmpFolder = $this->tmpFolder();

		if (is_dir($tmpFolder))
		{
			$this->deleteTmpFolder();
		}

		mkdir($tmpFolder);
		$file = $tmpFolder . '/execute-sql-test.sql';
		touch($file);
		$sql = "-- This is a comment"
			. "\n"
			. "CREATE TABLE `execute-sql-test` (`id` INTEGER PRIMARY KEY NOT NULL, `name` TEXT NOT NULL)"
			. "\n"
			. "/* And another comment */";

		$handle = fopen($file,'w+');
		fwrite($handle, $sql);
		fclose($handle);

		return $file;
	}
	/**
	 * Delete the test folder.
	 *
	 * @return  void
	 */
	private function deleteTmpFolder()
	{
		DeleteFolderRecursively::instance([$this->tmpFolder()])->execute();
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function fileIsExecuted()
	{
		$file = $this->createTestSQLFile();

		$this->assertTrue(file_exists($file));

		$db = Factory::getDbo();

		$this->assertFalse(in_array('execute-sql-test', $db->getTableList(), true));

		$command = new ExecuteSQLFile($file);
		$command->execute();

		$this->assertTrue(in_array('execute-sql-test', $db->getTableList(), true));

		$this->deleteTmpFolder();
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

	/**
	 * Route to the temporary folder used to test this command.
	 *
	 * @return  string
	 */
	private function tmpFolder()
	{
		return __DIR__ . '/tmp';
	}
}
