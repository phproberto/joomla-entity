<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Tests\Stubs\Entity;

/**
 * Base decorator tests.
 *
 * @since   1.1.0
 */
class DecoratorTest extends \TestCase
{
	/**
	 * Constructor sets entity.
	 *
	 * @return  void
	 */
	public function testConstructorSetsEntity()
	{
		$entity = new Entity;

		$decorator = $this->getMockBuilder(Decorator::class)
			->setConstructorArgs(array($entity))
			->getMockForAbstractClass();

		$reflection = new \ReflectionClass($decorator);
		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);

		$this->assertSame($entity, $entityProperty->getValue($decorator));
	}
}
