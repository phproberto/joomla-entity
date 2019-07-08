<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Command\FileSystem;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;
use Phproberto\Joomla\Entity\Command\FileSystem\DeleteFolderRecursively;

/**
 * DeleteFolderRecursivelyTest tests.
 *
 * @since   1.8
 */
class DeleteFolderRecursivelyTestTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function implementsCommandInterface()
	{
		$command = new DeleteFolderRecursively('test');

		$this->assertTrue($command instanceof CommandInterface);
	}

	/**
	 * Create a test folder structure.
	 *
	 * @return  void
	 */
	private function createTestFolder()
	{
		$tmpFolder = $this->tmpFolder();

		if (is_dir($tmpFolder))
		{
			$this->deleteTestFolder();
		}

		$childFolder = $tmpFolder . '/child-folder';

		mkdir($tmpFolder);
		touch($tmpFolder . '/delete-me.txt');
		mkdir($childFolder);
		touch($childFolder . '/delete-me-too.txt');
	}

	/**
	 * Delete the test folder.
	 *
	 * @return  void
	 */
	private function deleteTestFolder()
	{
		$tmpFolder = $this->tmpFolder();
		$childFolder = $tmpFolder . '/child-folder';

		@unlink($tmpFolder . '/delete-me.txt');
		@unlink($childFolder . '/delete-me-too.txt');
		@rmdir($childFolder);
		@rmdir($tmpFolder);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deletingUnexistingFolderReturnsTrue()
	{
		$this->assertTrue(
			DeleteFolderRecursively::instance([__DIR__ . '/does-not-exist'])->execute()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function folderIsDeleted()
	{
		$this->createTestFolder();

		$tmpFolder = $this->tmpFolder();

		$this->assertTrue(is_dir($tmpFolder));

		$command = new DeleteFolderRecursively($tmpFolder);
		$command->execute();

		$this->assertFalse(is_dir($tmpFolder));
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
