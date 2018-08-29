<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\EntityWithAncestors;

/**
 * HasAncestors tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasAncestorsTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function ancestorReturnsSpecificAncestor()
	{
		$ancestor = $this->entity->ancestor(1003);

		$this->assertInstanceOf(EntityWithAncestors::class, $ancestor);
		$this->assertSame(1003, $ancestor->id());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ancestorsReturnsExpectedAncestors()
	{
		$ancestors = $this->entity->ancestors();

		$this->assertInstanceOf(Collection::class, $ancestors);
		$this->assertEquals([1001, 1003, 1005, 1002], $ancestors->ids());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasAncestorReturnsExpectedValue()
	{
		$this->assertFalse($this->entity->hasAncestor(1000));
		$this->assertTrue($this->entity->hasAncestor(1001));
		$this->assertFalse($this->entity->hasAncestor(1004));
		$this->assertTrue($this->entity->hasAncestor(1002));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ancestorsReturnsExpectedValue()
	{
		$entity = new EntityWithAncestors;

		$this->assertFalse($entity->hasAncestors());

		$this->assertTrue($this->entity->hasAncestors());
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

		$this->entity = new EntityWithAncestors;
		$this->entity->bind(['id' => 666, 'name' => 'Testing entity']);
		$this->entity->loadableAncestors = new Collection(
			array_map(
				function ($data)
				{
					$ancestor = new EntityWithAncestors;
					$ancestor->bind($data);

					return $ancestor;
				},
				[
					['id' => 1001, 'name' => 'Top ancestor'],
					['id' => 1003, 'name' => 'An ancestor'],
					['id' => 1005, 'name' => 'Another ancestor'],
					['id' => 1002, 'name' => 'Yet another ancestor'],
				]
			)
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithAncestors::clearAll();

		parent::tearDown();
	}
}
