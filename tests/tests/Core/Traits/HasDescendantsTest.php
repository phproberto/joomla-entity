<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithDescendants;

/**
 * HasDescendants tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasDescendantsTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function descendantReturnsSpecificDescendant()
	{
		$descendant = $this->entity->descendant(5003);

		$this->assertInstanceOf(EntityWithDescendants::class, $descendant);
		$this->assertSame(5003, $descendant->id());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function descendantsReturnsExpectedDescendants()
	{
		$descendants = $this->entity->descendants();

		$this->assertInstanceOf(Collection::class, $descendants);
		$this->assertEquals([5001, 5003, 5005, 5002], $descendants->ids());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasDescendantReturnsExpectedValue()
	{
		$this->assertFalse($this->entity->hasDescendant(5000));
		$this->assertTrue($this->entity->hasDescendant(5001));
		$this->assertFalse($this->entity->hasDescendant(5004));
		$this->assertTrue($this->entity->hasDescendant(5002));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function descendantsReturnsExpectedValue()
	{
		$entity = new EntityWithDescendants;

		$this->assertFalse($entity->hasDescendants());

		$this->assertTrue($this->entity->hasDescendants());
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

		$this->entity = new EntityWithDescendants;
		$this->entity->bind(['id' => 666, 'name' => 'Testing entity']);
		$this->entity->loadableDescendants = new Collection(
			array_map(
				function ($data)
				{
					$descendant = new EntityWithDescendants;
					$descendant->bind($data);

					return $descendant;
				},
				[
					['id' => 5001, 'name' => 'First descendant'],
					['id' => 5003, 'name' => 'Second descendant'],
					['id' => 5005, 'name' => 'Third descendant'],
					['id' => 5002, 'name' => 'Fourth descendant'],
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
		EntityWithDescendants::clearAll();

		parent::tearDown();
	}
}
