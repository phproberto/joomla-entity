<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithParent;

/**
 * HasParent tests.
 *
 * @since   1.4.0
 */
class HasParentTest extends \TestCaseDatabase
{
	/**
	 * Name of the column used to store parent identifier.
	 *
	 * @const
	 */
	const PARENT_COLUMN = 'parent_id';

	/**
	 * Preloaded entity for tests.
	 *
	 * @var  EntityWithParent
	 */
	protected $entity;

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasParentReturnsExpectedResult()
	{
		$this->assertFalse($this->entity->hasParent());

		$this->entity->assign('parent_id', 12);

		$this->assertTrue($this->entity->hasParent());

		$this->entity->assign('parent_id', 'rr');

		$this->assertFalse($this->entity->hasParent());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentColumnReturnsExpectedValue()
	{
		$entity = $this->getMockBuilder(EntityWithParent::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->expects($this->once())
			->method('columnAlias')
			->with($this->equalTo('parent_id'))
			->willReturn(self::PARENT_COLUMN);

		$this->assertSame(self::PARENT_COLUMN, $entity->parentColumn());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentIdReturnsZeroForNoParentColumn()
	{
		$this->entity->bind(['id' => 23]);

		$this->assertSame(0, $this->entity->parentId());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentIdReturnsExpectedId()
	{
		$this->entity->bind(['id' => 23, self::PARENT_COLUMN => 34]);

		$this->assertSame(34, $this->entity->parentId());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentReturnsCachedInstance()
	{
		$parent = new EntityWithParent(55);
		$entity = new EntityWithParent;
		$entity->bind(['id' => 23]);

		$reflection = new \ReflectionClass($entity);

		$parentProperty = $reflection->getProperty('parent');
		$parentProperty->setAccessible(true);
		$parentProperty->setValue($entity, $parent);

		$this->assertSame($entity->parent(), $parent);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentReturnsExpectedInstance()
	{
		$this->entity->bind(['id' => 23, self::PARENT_COLUMN => 99]);

		$parent = $this->entity->parent();

		$this->assertInstanceOf(EntityWithParent::class, $parent);
		$this->assertSame(99, $parent->id());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentReturnsEmptyInstanceForEntitiesWithoutParentColumn()
	{
		$this->entity->bind(['id' => 43]);

		$parent = $this->entity->parent();

		$this->assertInstanceOf(EntityWithParent::class, $parent);
		$this->assertSame(0, $parent->id());
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->entity = $this->getMockBuilder(EntityWithParent::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$this->entity->method('columnAlias')
			->willReturn(self::PARENT_COLUMN);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithParent::clearAll();

		parent::tearDown();
	}
}
