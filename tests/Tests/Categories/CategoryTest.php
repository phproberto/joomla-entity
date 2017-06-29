<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories;

use Phproberto\Joomla\Entity\Categories\Category;

/**
 * Category entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class CategoryTest extends \TestCase
{
	/**
	 * getTable returns correct instance.
	 *
	 * @return  void
	 */
	public function testGetTableReturnsCorrectInstance()
	{
		$category = new Category;

		$this->assertInstanceOf('CategoriesTableCategory', $category->getTable());
	}
}
