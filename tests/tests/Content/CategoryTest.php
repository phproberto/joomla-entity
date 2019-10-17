<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Content\Category as DeprecatedCategory;
use Phproberto\Joomla\Entity\Content\Entity\Category;

/**
 * Category entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class CategoryTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function deprecatedClassExtendsNewClass()
	{
		$entity = new DeprecatedCategory;

		$this->assertInstanceOf(Category::class, $entity);
	}
}
