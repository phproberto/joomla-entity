<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories\Traits;

use Phproberto\Joomla\Entity\Categories\Category;
use Phproberto\Joomla\Entity\Tests\Categories\Traits\Stubs\ClassWithCategory;

/**
 * HasCategory trait tests.
 *
 * @since   1.1.0
 */
class HasCategoryTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function categoryIdReturnsZeroForUnexistingColumn()
	{
		$entity = new ClassWithCategory;

		$this->assertSame(0, $entity->categoryId());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function categoryIdReturnsExpectedValue()
	{
		$entity = new ClassWithCategory;
		$entity->bind(
			[
				'id' => 45,
				'category_id' => 12
			]
		);

		$this->assertSame(12, $entity->categoryId());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function categoryIdReturnsExpectedValueWithCustomCategoryColumn()
	{
		$entity = $this->getMockBuilder(ClassWithCategory::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('catid');

		$entity->bind(
			[
				'id' => 45,
				'catid' => 23
			]
		);

		$this->assertSame(23, $entity->categoryId());
	}

	/**
	 * getCategory works for catid column.
	 *
	 * @return  void
	 */
	public function testGetCategoryWorksForCatidColumn()
	{
		$class = $this->getMockBuilder(ClassWithCategory::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$class->method('columnAlias')
			->willReturn('catid');

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, 'catid' => 666));

		$this->assertSame(666, $class->category()->id());
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

		$rowProperty->setValue($class, array('id' => 999, 'category_id' => 666));

		$this->assertEquals(new Category(666), $class->category());
	}

	/**
	 * getCategory reload works.
	 *
	 * @return  void
	 */
	public function testGetCategoryReloadWorks()
	{
		$class = new ClassWithCategory;

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, 'category_id' => 666));

		$this->assertEquals(new Category(666), $class->category());

		$rowProperty->setValue($class, array('id' => 999, 'category_id' => 667));

		$this->assertEquals(new Category(666), $class->category());
		$this->assertEquals(new Category(667), $class->category(true));
	}

	/**
	 * getCategory returns empty category for unset column.
	 *
	 * @return  void
	 */
	public function testGetCategoryReturnsEmptyCategoryForUnsetColumn()
	{
		$class = new ClassWithCategory;

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, 'name' => 'Sample class'));

		$this->assertEquals(new Category, $class->category());
	}
}
