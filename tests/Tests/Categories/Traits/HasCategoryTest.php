<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories\Traits;

use Phproberto\Joomla\Entity\Tests\Categories\Traits\Stubs\ClassWithCategory;

/**
 * HasCategory trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasCategoryTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getCategory works for catid column.
	 *
	 * @return  void
	 */
	public function testGetCategoryWorksForCatidColumn()
	{
		$class = new ClassWithCategory;

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, ['id' => 999, 'catid' => 666]);

		$this->assertSame(666, $class->getCategory()->getId());
	}

	/**
	 * getCategory works for category_id column.
	 *
	 * @return  void
	 */
	public function testGetCategoryWorksForCategoryIdColumn()
	{
		$class = new ClassWithCategory;

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, ['id' => 999, 'category_id' => 666]);

		$this->assertSame(666, $class->getCategory()->getId());
	}
}
