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
use Joomla\CMS\Pagination\Pagination;
use Phproberto\Joomla\Entity\Searcher\BaseSearcher;
use Phproberto\Joomla\Entity\Traits\HasStaticCache;

/**
 * Database finder.
 *
 * @since  1.4.0
 */
abstract class DatabaseSearcher extends BaseSearcher
{
	use HasStaticCache;

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
		$this->db = isset($options['db']) ? $options['db'] : Factory::getDbo();

		unset($options['db']);
		parent::__construct($options);
	}

	/**
	 * Get the hash for a prefix.
	 *
	 * @param   string  $prefix  Prefix to use to generate a custom hash.
	 *
	 * @return string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function cacheHash($prefix)
	{
		$prefix = $prefix ? $prefix : get_class($this);

		$options = $this->options->toArray();

		ksort($options);

		return md5($prefix . ':' . json_encode($options));
	}



	/**
	 * Count of found items. Basically a copy of:
	 * Joomla\CMS\MVC\Model\BaseDatabaseModel::_getListCount()
	 *
	 * @return  integer
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function count()
	{
		$key = $this->cacheHash('count');

		return $this->getFromStaticCacheOrStore($key, [$this, 'loadCount']);
	}

	/**
	 * Default options for this finder.
	 *
	 * @return  array
	 */
	public function defaultOptions()
	{
		return array_merge(
			parent::defaultOptions(),
			[
				'list.start' => 0,
				'list.limit' => 20
			]
		);
	}

	/**
	 * Number of items to retrieve in the search.
	 *
	 * @return  integer
	 */
	public function limit()
	{
		return (int) $this->options->get('list.limit', 20);
	}

	/**
	 * Load the count of items in the search.
	 *
	 * @return  integer
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function loadCount()
	{
		$query = $this->searchQuery();

		// Use fast COUNT(*) on \JDatabaseQuery objects if there is no GROUP BY or HAVING clause:
		if ($query instanceof \JDatabaseQuery
			&& $query->type == 'select'
			&& $query->group === null
			&& $query->union === null
			&& $query->unionAll === null
			&& $query->having === null)
		{
			$query = clone $query;
			$query->clear('select')->clear('order')->clear('limit')->clear('offset')->select('COUNT(*)');

			$this->db->setQuery($query);

			return (int) $this->db->loadResult();
		}

		// Otherwise fall back to inefficient way of counting all results.

		// Remove the limit and offset part if it's a \JDatabaseQuery object
		if ($query instanceof \JDatabaseQuery)
		{
			$query = clone $query;
			$query->clear('limit')->clear('offset');
		}

		$this->db->setQuery($query);
		$this->db->execute();

		return (int) $this->db->getNumRows();
	}

	/**
	 * Pagination object.
	 *
	 * @return  Pagination
	 */
	public function pagination()
	{
		return new Pagination($this->count(), $this->start(), $this->limit());
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
		$key = $this->cacheHash('search');

		return $this->getFromStaticCacheOrStore($key, [$this, 'searchFresh']);
	}

	/**
	 * Search without using cache.
	 *
	 * @return  array
	 */
	public function searchFresh()
	{
		$this->db->setQuery(
			$this->searchQuery(),
			$this->start(),
			$this->limit()
		);

		return $this->db->loadAssocList() ?: [];
	}

	/**
	 * Get the starting item.
	 *
	 * @return  integer
	 */
	public function start()
	{
		$start = (int) $this->options->get('list.start');

		if ($start > 0)
		{
			$limit = $this->limit();
			$total = $this->count();

			if ($start > $total - $limit)
			{
				$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
			}
		}

		return (int) $start;
	}
}
