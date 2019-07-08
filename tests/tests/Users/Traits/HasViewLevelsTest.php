<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\ViewLevel;
use Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs\EntityWithViewLevels;

/**
 * HasViewLevels trait tests.
 *
 * @since   1.2.0
 */
class HasViewLevelsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * clearViewLevels clears viewLevels property.
	 *
	 * @return  void
	 */
	public function testClearViewLevelsClearsViewLevelsProperty()
	{
		$entity = new EntityWithViewLevels;

		$reflection = new \ReflectionClass($entity);

		$viewLevelsProperty = $reflection->getProperty('viewLevels');
		$viewLevelsProperty->setAccessible(true);

		$this->assertSame(null, $viewLevelsProperty->getValue($entity));

		$viewLevels = new Collection(array(ViewLevel::find(333)));

		$viewLevelsProperty->setValue($entity, $viewLevels);

		$this->assertSame($viewLevels, $viewLevelsProperty->getValue($entity));

		$entity->clearViewLevels();

		$this->assertSame(null, $viewLevelsProperty->getValue($entity));
	}

	/**
	 * viewLevels returns cached data.
	 *
	 * @return  void
	 */
	public function testviewLevelsReturnsCachedData()
	{
		$entity = new EntityWithViewLevels;

		$reflection = new \ReflectionClass($entity);

		$viewLevelsProperty = $reflection->getProperty('viewLevels');
		$viewLevelsProperty->setAccessible(true);

		$this->assertSame(null, $viewLevelsProperty->getValue($entity));

		$viewLevels = new Collection(array(ViewLevel::find(333)));

		$viewLevelsProperty->setValue($entity, $viewLevels);

		$this->assertSame($viewLevels, $entity->viewLevels());
	}

	/**
	 * viewLevels returns loadViewLevels result if not cached.
	 *
	 * @return  void
	 */
	public function testViewLevelsReturnsLoadViewLevelsResultIfNotCached()
	{
		$expectedViewLevels = new Collection(
			array(
				ViewLevel::find(333),
				ViewLevel::find(666)
			)
		);

		$entity = new EntityWithViewLevels;
		$entity->loadableViewLevels = $expectedViewLevels;

		$this->assertSame($expectedViewLevels, $entity->viewLevels());
	}

	/**
	 * hasViewLevel returns correct value.
	 *
	 * @return  void
	 */
	public function testHasViewLevelReturnsCorrectValue()
	{
		$entity = new EntityWithViewLevels;
		$entity->loadableViewLevels = new Collection(
			array(
				ViewLevel::find(666),
				ViewLevel::find(999)
			)
		);

		$this->assertTrue($entity->hasViewLevel(666));
		$this->assertFalse($entity->hasViewLevel(333));
		$this->assertTrue($entity->hasViewLevel(999));
	}

	/**
	 * hasViewLevels returns correct value.
	 *
	 * @return  void
	 */
	public function testHasViewLevelsReturnsCorrectValue()
	{
		$entity = new EntityWithViewLevels;
		$entity->loadableViewLevels = new Collection(
			array(
				ViewLevel::find(333),
				ViewLevel::find(666)
			)
		);

		$this->assertTrue($entity->hasViewLevels());

		$entity = new EntityWithViewLevels;
		$entity->loadableViewLevels = new Collection;

		$this->assertFalse($entity->hasViewLevels());
	}
}
