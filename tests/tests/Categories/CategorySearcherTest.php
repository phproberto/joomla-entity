<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Categories\CategorySearcher;
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

/**
 * Category searcher tests.
 *
 * @since   1.4.0
 */
class CategorySearcherTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function extendsCategorySearch()
	{
		$search = new CategorySearcher;

		$this->assertInstanceOf(CategorySearch::class, $search);
	}
}
