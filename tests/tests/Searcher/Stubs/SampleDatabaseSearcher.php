<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Searcher\Stubs;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Searcher\DatabaseSearcher;

/**
 * Sampl searcher for tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class SampleDatabaseSearcher extends DatabaseSearcher
{
	/**
	 * Mocked cache hash.
	 *
	 * @var  array
	 */
	public $mockedCacheHash = [];

	/**
	 * Mocked search query.
	 *
	 * @var  \JDatabaseQuery
	 */
	public $mockedSearchQuery;

	/**
	 * Mocked static cache.
	 *
	 * @var  array
	 */
	public $mockedStaticCache;

	/**
	 * Get the hash for a prefix.
	 *
	 * @param   string  $prefix  Prefix to use to generate a custom hash.
	 *
	 * @return string
	 */
	protected function cacheHash($prefix)
	{
		$prefix = $prefix ? $prefix : get_class($this);

		if (array_key_exists($prefix, $this->mockedCacheHash))
		{
			return $this->mockedCacheHash[$prefix];
		}

		return parent::cacheHash($prefix);
	}

	/**
	 * Gets static cache for this class
	 *
	 * @return  array
	 */
	protected function &getStaticCache(): array
	{
		if ($this->mockedStaticCache)
		{
			return $this->mockedStaticCache;
		}

		return parent::getStaticCache();
	}

	/**
	 * Retrieve the search query.
	 *
	 * @return  \JDatabaseQuery
	 */
	public function searchQuery()
	{
		if ($this->mockedSearchQuery)
		{
			return $this->mockedSearchQuery;
		}

		return $this->db->getQuery(true);
	}
}
