<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Acl\Traits;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Acl\Acl;
use Phproberto\Joomla\Entity\Tests\Acl\Stubs\EntityWithAcl;

/**
 * HasAcl trait tests.
 *
 * @since   1.1.0
 */
class HasAclTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * acl returns Acl instance.
	 *
	 * @return  void
	 */
	public function testAclReturnsAclInstance()
	{
		$entity = new EntityWithAcl(666);
		$user = new User(999);

		$acl = $entity->acl($user);

		$reflection = new \ReflectionClass($acl);
		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);

		$this->assertInstanceOf(Acl::class, $acl);
		$this->assertSame($user, $userProperty->getValue($acl));
		$this->assertSame($entity, $entityProperty->getValue($acl));
	}

	/**
	 * aclPrefix returns correct value.
	 *
	 * @return  void
	 */
	public function testAclPrefixReturnsCorrectValue()
	{
		$entity = new EntityWithAcl;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('aclPrefix');
		$method->setAccessible(true);

		$this->assertSame('core', $method->invoke($entity));
	}

	/**
	 * aclAssetName returns component option when no id.
	 *
	 * @return  void
	 */
	public function testAclAssetNameReturnsComponentOptionWhenNoId()
	{
		$component = $this->getMockBuilder(Component::class)
			->setMethods(array('option'))
			->getMock();

		$component->expects($this->once())
			->method('option')
			->willReturn('com_phproberto');

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('component'))
			->getMock();

		$entity->expects($this->once())
			->method('component')
			->willReturn($component);

		$this->assertSame('com_phproberto', $entity->aclAssetName());
	}

	/**
	 * aclAssetName returns component option when no id.
	 *
	 * @return  void
	 */
	public function testAclAssetNameReturnsOptionAndNameWhenHasId()
	{
		$component = $this->getMockBuilder(Component::class)
			->setMethods(array('option'))
			->getMock();

		$component->expects($this->once())
			->method('option')
			->willReturn('com_phproberto');

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('component', 'name'))
			->getMock();

		$entity->expects($this->once())
			->method('component')
			->willReturn($component);

		$entity->expects($this->once())
			->method('name')
			->willReturn('sample');

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$this->assertSame('com_phproberto.sample.999', $entity->aclAssetName());
	}
}
