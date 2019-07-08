<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithLevel;

/**
 * HasLevel tests.
 *
 * @since   1.4.0
 */
class HasLevelTest extends \TestCaseDatabase
{
	/**
	 * Name of the column used to store level.
	 *
	 * @const
	 */
	const LEVEL_COLUMN = 'level';

	/**
	 * Preloaded entity for tests.
	 *
	 * @var  EntityWithLevel
	 */
	protected $entity;

	/**
	 * Data provider to test hasLevel method.
	 *
	 * @return  boolean
	 */
	public function hasLevelDataProvider()
	{
		return [
			[1, 1, true],
			[1, 2, false],
			[0, 0, true],
			[2, 0, false],
			[2, 1, false],
			[2, 2, true]
		];
	}

	/**
	 * Data provider to test hasLevelBetween method.
	 *
	 * @return  boolean
	 */
	public function hasLevelBetweenDataProvider()
	{
		return [
			[1, 1, 2, true],
			[1, 2, 2, false],
			[1, 0, 1, true],
			[2, 0, 1, false],
			[2, 1, 1, false],
			[2, 2, 2, true]
		];
	}

	/**
	 * Data provider to test hasLevelGreater method.
	 *
	 * @return  boolean
	 */
	public function hasLevelGreaterDataProvider()
	{
		return [
			[1, 1, false],
			[1, 2, false],
			[0, 0, false],
			[2, 0, true],
			[2, 1, true],
			[2, 2, false],
			[0, 2, false],
			[100, 22, true]
		];
	}

	/**
	 * Data provider to test hasLevelLower method.
	 *
	 * @return  boolean
	 */
	public function hasLevelLowerDataProvider()
	{
		return [
			[1, 1, false],
			[1, 2, true],
			[0, 0, false],
			[2, 0, false],
			[2, 1, false],
			[2, 2, false],
			[0, 2, true],
			[1, 22, true]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  hasLevelBetweenDataProvider
	 *
	 * @param   integer  $level      Level to assign to the entity
	 * @param   integer  $fromLevel  Initial level
	 * @param   integer  $toLevel    Final level
	 * @param   boolean  $expected   Expected result returned by hasLevelBetween()
	 *
	 * @return void
	 */
	public function hasLevelBetweenReturnsExpectedValue(int $level, int $fromLevel, int $toLevel, bool $expected)
	{
		$this->entity->bind(['id' => 1, self::LEVEL_COLUMN => $level]);

		$this->assertSame($expected, $this->entity->hasLevelBetween($fromLevel, $toLevel));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasLevelBetweenReturnFalseForEntitiesWithoutLevelColumn()
	{
		$this->entity->bind(['id' => 1]);

		$this->assertFalse($this->entity->hasLevelBetween(1, 2));
	}

	/**
	 * @test
	 *
	 * @dataProvider  hasLevelGreaterDataProvider
	 *
	 * @param   integer  $level         Level to assign to the entity
	 * @param   integer  $levelToCheck  Level
	 * @param   boolean  $expected      Expected result returned by hasLevelLower()
	 *
	 * @return void
	 */
	public function hasLevelGreaterReturnsExpectedValue(int $level, int $levelToCheck, bool $expected)
	{
		$this->entity->bind(['id' => 1, self::LEVEL_COLUMN => $level]);

		$this->assertSame($expected, $this->entity->hasLevelGreater($levelToCheck));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasLevelGreaterReturnsFalseForEntitiesWithoutLevelColumn()
	{
		$this->entity->bind(['id' => 1]);

		$this->assertFalse($this->entity->hasLevelGreater(1));
	}

	/**
	 * @test
	 *
	 * @dataProvider  hasLevelLowerDataProvider
	 *
	 * @param   integer  $level         Level to assign to the entity
	 * @param   integer  $levelToCheck  Level
	 * @param   boolean  $expected      Expected result returned by hasLevelLower()
	 *
	 * @return void
	 */
	public function hasLevelLowerReturnsExpectedValue(int $level, int $levelToCheck, bool $expected)
	{
		$this->entity->bind(['id' => 1, self::LEVEL_COLUMN => $level]);

		$this->assertSame($expected, $this->entity->hasLevelLower($levelToCheck));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasLevelLowerReturnFalseForEntitiesWithoutLevelColumn()
	{
		$this->entity->bind(['id' => 1]);

		$this->assertFalse($this->entity->hasLevelLower(23));
	}

	/**
	 * @test
	 *
	 * @dataProvider  hasLevelDataProvider
	 *
	 * @param   integer  $level         Level to assign to the entity
	 * @param   integer  $levelToCheck  Level
	 * @param   boolean  $expected      Expected result returned by hasLevel()
	 *
	 * @return void
	 */
	public function hasLevelReturnsExpectedValue(int $level, int $levelToCheck, bool $expected)
	{
		$this->entity->bind(['id' => 1, self::LEVEL_COLUMN => $level]);

		$this->assertSame($expected, $this->entity->hasLevel($levelToCheck));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasLevelReturnsFalseForEntitiesWithoutLevelColumn()
	{
		$this->entity->bind(['id' => 1]);

		$this->assertFalse($this->entity->hasLevel(2));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function levelReturnsZeroForEntitiesWithoutLevelColumn()
	{
		$this->entity->bind(['id' => 1]);
		$this->assertSame(0, $this->entity->level());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function levelReturnsExpectedValue()
	{
		$this->entity->bind(['id' => 1, self::LEVEL_COLUMN => '12aaa']);
		$this->assertSame(12, $this->entity->level());

		$this->entity->bind(['id' => 1, self::LEVEL_COLUMN => null]);
		$this->assertSame(0, $this->entity->level());

		$this->entity->bind(['id' => 1, self::LEVEL_COLUMN => 23]);
		$this->assertSame(23, $this->entity->level());

		$this->entity->bind(['id' => 1, self::LEVEL_COLUMN => '']);
		$this->assertSame(0, $this->entity->level());
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

		$this->entity = $this->getMockBuilder(EntityWithLevel::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$this->entity->method('columnAlias')
			->willReturn(self::LEVEL_COLUMN);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithLevel::clearAll();

		parent::tearDown();
	}
}
