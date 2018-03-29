<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Categories\Traits;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Categories\Category;
use Phproberto\Joomla\Entity\Tests\Unit\Categories\Traits\Stubs\ClassWithCategories;

/**
 * HasCategories trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasCategoriesTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		ClassWithCategories::clearAll();

		parent::tearDown();
	}

	/**
	 * clearCategories clears categories property.
	 *
	 * @return  void
	 */
	public function testClearCategoriesClearsCategoriesProperty()
	{
		$entity = new ClassWithCategories;

		$reflection = new \ReflectionClass($entity);
		$categoriesProperty = $reflection->getProperty('categories');
		$categoriesProperty->setAccessible(true);

		$this->assertEquals(null, $categoriesProperty->getValue($entity));

		$categories = new Collection(
			array(
				new Category(23),
				new Category(24),
				new Category(25)
			)
		);

		$categoriesProperty->setValue($entity, $categories);
		$this->assertEquals($categories, $categoriesProperty->getValue($entity));

		$entity->clearCategories();
		$this->assertEquals(null, $categoriesProperty->getValue($entity));
	}

	/**
	 * clearCategories is chainable.
	 *
	 * @return  void
	 */
	public function testClearCategoriesIsChainable()
	{
		$entity = new ClassWithCategories;

		$this->assertTrue($entity->clearCategories() instanceof ClassWithCategories);
	}

	/**
	 * getCategories returns correct data.
	 *
	 * @return  void
	 */
	public function testGetCategoriesReturnsCorrectData()
	{
		$entity = new ClassWithCategories;

		$this->assertEquals(new Collection, $entity->categories());

		$entity->categoriesIds = array(999);

		// Previous data with no reload
		$this->assertEquals(new Collection, $entity->categories());
		$this->assertEquals(new Collection(array(new Category(999))), $entity->categories(true));
	}

	/**
	 * hasCategory returns correct value.
	 *
	 * @return  void
	 */
	public function testHasCategoryReturnsCorrectValue()
	{
		$entity = new ClassWithCategories;

		$entity->categoriesIds = array(999, 1001, 1003);

		$this->assertFalse($entity->hasCategory(998));
		$this->assertTrue($entity->hasCategory(999));
		$this->assertFalse($entity->hasCategory(1000));
		$this->assertTrue($entity->hasCategory(1001));
		$this->assertFalse($entity->hasCategory(1002));
		$this->assertTrue($entity->hasCategory(1003));
	}

	/**
	 * hasCategories returns correct value.
	 *
	 * @return  void
	 */
	public function testHasCategoriesReturnsCorrectValue()
	{
		$entity = new ClassWithCategories;

		$this->assertFalse($entity->hasCategories());

		$entity = new ClassWithCategories;
		$entity->categoriesIds = array(999, 1001, 1003);

		$this->assertTrue($entity->hasCategories());
	}
}
