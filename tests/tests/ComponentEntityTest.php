<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Core\Extension\Component;

/**
 * Component Entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ComponentEntityTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function tablePrefixReturnsComponentPrefix()
	{
		$component = $this->getMockBuilder(Component::class)
			->setMethods(array('prefix'))
			->getMock();

		$component->expects($this->once())
			->method('prefix')
			->willReturn('Custom_Prefix');

		$entity = $this->getMockBuilder(ComponentEntity::class)
			->setMethods(array('component'))
			->getMock();

		$entity->expects($this->once())
			->method('component')
			->willReturn($component);

		$this->assertSame('Custom_Prefix', $entity->tablePrefix());
	}
}
