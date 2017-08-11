<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithAcl;

/**
 * HasAcl trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasAclTest extends \PHPUnit\Framework\TestCase
{
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
	 * assetName returns component option when no id.
	 *
	 * @return  void
	 */
	public function testAssetNameReturnsComponentOptionWhenNoId()
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

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('assetName');
		$method->setAccessible(true);

		$this->assertSame('com_phproberto', $method->invoke($entity));
	}

	/**
	 * assetName returns component option when no id.
	 *
	 * @return  void
	 */
	public function testAssetNameReturnsOptionAndNameWhenHasId()
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
		$method = $reflection->getMethod('assetName');
		$method->setAccessible(true);

		$this->assertSame('com_phproberto.sample.999', $method->invoke($entity));
	}

	/**
	 * canCreate returns false if cannot create and not owner.
	 *
	 * @return  void
	 */
	public function testCanCreateReturnsFalseIfCannotCreateAndNotOwner()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->once())
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->once())
			->method('canDo')
			->with($this->equalTo('core.create'))
			->willReturn(false);

		$entity->expects($this->once())
			->method('isOwner')
			->willReturn(false);

		$this->assertFalse($entity->canCreate());
	}

	/**
	 * canCreate returns true for create permission.
	 *
	 * @return  void
	 */
	public function testCanCreateReturnsTrueForCreatePermission()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->once())
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->once())
			->method('canDo')
			->with($this->equalTo('core.create'))
			->willReturn(true);

		$entity->expects($this->exactly(0))
			->method('isOwner')
			->willReturn(false);

		$this->assertTrue($entity->canCreate());
	}

	/**
	 * canCreate returns true when owner and create own allowed.
	 *
	 * @return  void
	 */
	public function testCanCreateReturnsTrueWhenOwnerAndCreateOwnAllowed()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->exactly(2))
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->at(1))
			->method('canDo')
			->with($this->equalTo('core.create'))
			->willReturn(false);

		$entity->expects($this->once())
			->method('isOwner')
			->willReturn(true);

		$entity->expects($this->at(4))
			->method('canDo')
			->with($this->equalTo('core.create.own'))
			->willReturn(true);

		$this->assertTrue($entity->canCreate());
	}

	/**
	 * canDelete returns false for missing id.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsFalseForMissingId()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(false);

		$this->assertFalse($entity->canDelete());
	}

	/**
	 * canDelete returns false when no delete and not owner.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsFalseWhenNoDeleteAllowedAndNotOwner()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->once())
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->once())
			->method('canDo')
			->with($this->equalTo('core.delete'))
			->willReturn(false);

		$entity->expects($this->once())
			->method('isOwner')
			->willReturn(false);

		$this->assertFalse($entity->canDelete());
	}

	/**
	 * canDelete returns false when owner and delete own not allowed.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsFalseWhenOwnerAndDeleteOwnNotAllowed()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->exactly(2))
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->at(2))
			->method('canDo')
			->with($this->equalTo('core.delete'))
			->willReturn(false);

		$entity->expects($this->once())
			->method('isOwner')
			->willReturn(true);

		$entity->expects($this->at(5))
			->method('canDo')
			->with($this->equalTo('core.delete.own'))
			->willReturn(false);

		$this->assertFalse($entity->canDelete());
	}

	/**
	 * canDelete returns true when delete allowed.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsTrueWhenDeleteAllowed()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->once())
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->once())
			->method('canDo')
			->with($this->equalTo('core.delete'))
			->willReturn(true);

		$entity->expects($this->exactly(0))
			->method('isOwner')
			->willReturn(false);

		$this->assertTrue($entity->canDelete());
	}

	/**
	 * canDelete returns true when owner and delete own allowed.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsTrueWhenOwnerAndDeleteOwnAllowed()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->exactly(2))
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->at(2))
			->method('canDo')
			->with($this->equalTo('core.delete'))
			->willReturn(false);

		$entity->expects($this->once())
			->method('isOwner')
			->willReturn(true);

		$entity->expects($this->at(5))
			->method('canDo')
			->with($this->equalTo('core.delete.own'))
			->willReturn(true);

		$this->assertTrue($entity->canDelete());
	}

	/**
	 * canDo returns true when is admin.
	 *
	 * @return  void
	 */
	public function testCanDoReturnsTrueWhenIsAdmin()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('isAdmin'))
			->getMock();

		$entity->expects($this->once())
			->method('isAdmin')
			->willReturn(true);

		$this->assertTrue($entity->canDo('delete'));
	}

	/**
	 * canDo returns true when is admin.
	 *
	 * @return  void
	 */
	public function testCanDoCallsAuthoriseWhenNotAdmin()
	{
		$user = $this->getMockBuilder('UserMock')
			->setMethods(array('authorise'))
			->getMock();

		$user->expects($this->at(0))
			->method('authorise')
			->with($this->equalTo('core.delete'), $this->equalTo('com_phproberto.sample.999'))
			->willReturn(true);

		$user->expects($this->at(1))
			->method('authorise')
			->with($this->equalTo('core.delete'), $this->equalTo('com_phproberto.sample.999'))
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('isAdmin', 'user', 'assetName'))
			->getMock();

		$entity->expects($this->exactly(2))
			->method('isAdmin')
			->willReturn(false);

		$entity->expects($this->exactly(2))
			->method('user')
			->willReturn($user);

		$entity->expects($this->exactly(2))
			->method('assetName')
			->willReturn('com_phproberto.sample.999');

		$this->assertTrue($entity->canDo('core.delete'));
		$this->assertFalse($entity->canDo('core.delete'));
	}

	/**
	 * canEdit returns true for edit permission.
	 *
	 * @return  void
	 */
	public function testCanEditReturnsTrueForEditPermission()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('canDo', 'aclPrefix'))
			->getMock();

		$entity->expects($this->once())
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->once())
			->method('canDo')
			->with($this->equalTo('core.edit'))
			->willReturn(true);

		$this->assertTrue($entity->canEdit());
	}

	/**
	 * canEdit return false for no edit and no owner..
	 *
	 * @return  void
	 */
	public function testCanEditReturnsFalseForNoEditAndNoOwner()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->once())
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->once())
			->method('canDo')
			->with($this->equalTo('core.edit'))
			->willReturn(false);

		$entity->expects($this->once())
			->method('isOwner')
			->willReturn(false);

		$this->assertFalse($entity->canEdit());
	}

	/**
	 * canEdit returns true when owner and owner edit permission.
	 *
	 * @return  void
	 */
	public function testCanEditReturnsTrueWhenOwnerAndOwnerEditPermission()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('canDo', 'aclPrefix', 'isOwner'))
			->getMock();

		$entity->expects($this->exactly(2))
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->at(1))
			->method('canDo')
			->with($this->equalTo('core.edit'))
			->willReturn(false);

		$entity->expects($this->once())
			->method('isOwner')
			->willReturn(true);

		$entity->expects($this->at(4))
			->method('canDo')
			->with($this->equalTo('core.edit.own'))
			->willReturn(true);

		$this->assertTrue($entity->canEdit());
	}

	/**
	 * isAdmin returns cached value.
	 *
	 * @return  void
	 */
	public function testIsAdminReturnsCachedValue()
	{
		$entity = new EntityWithAcl;

		$reflection = new \ReflectionClass($entity);
		$isAdminProperty = $reflection->getProperty('isAdmin');
		$isAdminProperty->setAccessible(true);
		$isAdminProperty->setValue($entity, false);

		$this->assertFalse($entity->isAdmin());

		$isAdminProperty->setValue($entity, true);

		$this->assertTrue($entity->isAdmin());
	}

	/**
	 * isAdmin returns false for guests.
	 *
	 * @return  void
	 */
	public function testIsAdminReturnsFalseForGuests()
	{
		$user = $this->getMockBuilder('MockedUser')
			->setMethods(array('get'))
			->getMock();

		$user->expects($this->once())
			->method('get')
			->with('guest')
			->willReturn(1);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('user'))
			->getMock();

		$entity->expects($this->once())
			->method('user')
			->willReturn($user);

		$this->assertFalse($entity->isAdmin());
	}

	/**
	 * isAdmin returns true for admins.
	 *
	 * @return  void
	 */
	public function testIsAdminReturnsTrueForAdmins()
	{
		$user = $this->getMockBuilder('MockedUser')
			->setMethods(array('get', 'authorise'))
			->getMock();

		$user->expects($this->once())
			->method('get')
			->with('guest')
			->willReturn(0);

		$user->expects($this->once())
			->method('authorise')
			->with('core.admin', 'com_phproberto')
			->willReturn(true);

		$component = $this->getMockBuilder('MockedComponet')
			->setMethods(array('option'))
			->getMock();

		$component->expects($this->once())
			->method('option')
			->willReturn('com_phproberto');

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('component', 'user'))
			->getMock();

		$entity->expects($this->once())
			->method('component')
			->willReturn($component);

		$entity->expects($this->once())
			->method('user')
			->willReturn($user);

		$this->assertTrue($entity->isAdmin());
	}

	/**
	 * isGuest returns correct value.
	 *
	 * @return  void
	 */
	public function testIsGuestReturnsCorrectValue()
	{
		$user = $this->getMockBuilder('MockedUser')
			->setMethods(array('get'))
			->getMock();

		$user->expects($this->at(0))
			->method('get')
			->with('guest')
			->willReturn(0);

		$user->expects($this->at(1))
			->method('get')
			->with('guest')
			->willReturn(1);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('user'))
			->getMock();

		$entity->expects($this->exactly(2))
			->method('user')
			->willReturn($user);

		$this->assertFalse($entity->isGuest());
		$this->assertTrue($entity->isGuest());

	}

	/**
	 * isOwner returns false for missing id.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsFalseForMissingId()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'isGuest'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(false);

		$entity->expects($this->exactly(0))
			->method('isGuest')
			->willReturn(false);

		$this->assertFalse($entity->isOwner());
	}

	/**
	 * isOwner returns false for guests.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsFalseForGuests()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'isGuest'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->once())
			->method('isGuest')
			->willReturn(true);

		$this->assertFalse($entity->isOwner());
	}

	/**
	 * isOwner returns false for guests.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsFalseForEntitiesWithoutCreatedColumn()
	{
		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'isGuest'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->once())
			->method('isGuest')
			->willReturn(false);

		$entity->bind(array('id' => 999, 'modified_by' => 999));

		$this->assertFalse($entity->isOwner());
	}

	/**
	 * isOwner returns false for guests.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsFalseForCreatedNotUserId()
	{
		$user = $this->getMockBuilder('MockedUser')
			->setMethods(array('get'))
			->getMock();

		$user->expects($this->once())
			->method('get')
			->with('id')
			->willReturn(666);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'isGuest', 'user'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->once())
			->method('isGuest')
			->willReturn(false);

		$entity->expects($this->once())
			->method('user')
			->willReturn($user);

		$entity->bind(array('id' => 999, 'created_by' => 999));

		$this->assertFalse($entity->isOwner());
	}

	/**
	 * isOwner returns false for guests.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsTrueForEqualCreatedUserId()
	{
		$user = $this->getMockBuilder('MockedUser')
			->setMethods(array('get'))
			->getMock();

		$user->expects($this->once())
			->method('get')
			->with('id')
			->willReturn(999);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'isGuest', 'user'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->once())
			->method('isGuest')
			->willReturn(false);

		$entity->expects($this->once())
			->method('user')
			->willReturn($user);

		$entity->bind(array('id' => 999, 'created_by' => 999));

		$this->assertTrue($entity->isOwner());
	}
}
