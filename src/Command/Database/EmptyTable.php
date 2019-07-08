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
 * Empty a database table.
 *
 * @since  1.8
 */
final class EmptyTable extends BaseCommand implements CommandInterface
{
	/**
	 * Database driver.
	 *
	 * @var  \JDatabaseDriver
	 */
	private $db;

	/**
	 * Name of the table to empty.
	 *
	 * @var  string
	 */
	private $tableName;

	/**
	 * Constructor.
	 *
	 * @param   string  $name     Name of the table to empty.
	 * @param   array   $options  Additional settings
	 */
	public function __construct(string $name, array $options = [])
	{
		$this->tableName = $name;
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
		try
		{
			$query = $this->db->getQuery(true)
				->delete($this->db->qn($this->tableName));

			$this->db->setQuery($query);
			$this->db->execute();
		}
		catch (\RuntimeException $e)
		{
			throw new \RuntimeException(sprintf('Error emptying DB table `%s`: %s', $this->tableName, $e->getMessage()));
		}
	}
}
