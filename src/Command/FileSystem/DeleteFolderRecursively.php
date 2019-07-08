<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Command\FileSystem;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Command\BaseCommand;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * Delete a folder and all its contents.
 *
 * @since  1.8
 */
class DeleteFolderRecursively extends BaseCommand implements CommandInterface
{
	/**
	 * Folder to delete.
	 *
	 * @var  string
	 */
	private $folder;

	/**
	 * Constructor.
	 *
	 * @param   string  $folder   Folder to delete
	 * @param   array   $options  Array with other options
	 */
	public function __construct(string $folder, array $options = [])
	{
		$this->folder = trim($folder);

		parent::__construct($options);
	}

	/**
	 * Execute the command.
	 *
	 * @return  boolean
	 */
	public function execute()
	{
		if (!is_dir($this->folder))
		{
			return true;
		}

		$files = glob($this->folder . '/*');

		foreach ($files as $file)
		{
			if (is_dir($file))
			{
				self::instance([$file])->execute();

				continue;
			}

			if (!@unlink($file))
			{
				$error = sprintf("Error deleting file: `%s`", $file);

				throw new \RuntimeException($error);
			}
		}

		if (!rmdir($this->folder))
		{
			$error = sprintf("Error deleting folder: `%s`", $this->folder);

			throw new \RuntimeException($error);
		}

		return true;
	}
}
