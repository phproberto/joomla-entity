<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Searcher;

defined('_JEXEC') || die;

/**
 * Searcher interface.
 *
 * @since  1.4.0
 */
interface SearcherInterface
{
	/**
	 * Execute the search.
	 *
	 * @return  array
	 */
	public function search();
}
