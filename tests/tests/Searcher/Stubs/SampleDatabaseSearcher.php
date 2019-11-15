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
	 * Mocked search query.
	 *
	 * @var  \JDatabaseQuery
	 */
	public $searchQuery;

	/**
	 * Retrieve the search query.
	 *
	 * @return  \JDatabaseQuery
	 */
	public function searchQuery()
	{
		if (null !== $this->searchQuery)
		{
			return $this->searchQuery;
		}

		return $this->db->getQuery(true);
	}
}
