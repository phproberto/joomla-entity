<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Searcher;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Searcher\BaseSearcher;

/**
 * Database finder.
 *
 * @since  1.4.0
 */
abstract class DatabaseSearcher extends BaseSearcher
{
	/**
	 * Database driver.
	 *
	 * @var  \JDatabaseDriver
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param   array  $options  Find options
	 */
	public function __construct(array $options = [])
	{
		parent::__construct($options);

		$this->db = $this->options->get('db', Factory::getDbo());
	}

	/**
	 * Default options for this finder.
	 *
	 * @return  array
	 */
	public function defaultOptions()
	{
		return [
			'list.start' => 0,
			'list.limit' => 20
		];
	}

	/**
	 * Retrieve the search query.
	 *
	 * @return  \JDatabaseQuery
	 */
	abstract public function searchQuery();

	/**
	 * Execute the search.
	 *
	 * @return  array
	 */
	public function search()
	{
		$this->db->setQuery(
			$this->searchQuery(),
			(int) $this->options->get('list.start'),
			(int) $this->options->get('list.limit', 20)
		);

		return $this->db->loadAssocList() ?: [];
	}
}
