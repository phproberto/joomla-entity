<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Command\Database;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Command\BaseCommand;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * Executes a SQL file.
 *
 * @since  __DEPLOY_VERSION__
 */
final class ExecuteSQLFile extends BaseCommand implements CommandInterface
{
	/**
	 * Database driver.
	 *
	 * @var  \JDatabaseDriver
	 */
	private $db;

	/**
	 * Path to the file to execute.
	 *
	 * @var  string
	 */
	private $file;

	/**
	 * Constructor.
	 *
	 * @param   string  $filePath  File to execute.
	 * @param   array   $options   Additional settings
	 */
	public function __construct(string $filePath, array $options = [])
	{
		$this->file = $filePath;
		$this->db = isset($options['db']) ? $options['db'] : Factory::getDbo();

		unset($options['db']);

		parent::__construct($options);
	}

	/**
	 * Execute the command.
	 *
	 * @return  mixed
	 */
	public function execute()
	{
		$this->db->setQuery(
			file_get_contents($this->file)
		);

		$this->db->execute();
	}
}
