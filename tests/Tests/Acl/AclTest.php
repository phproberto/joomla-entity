<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Acl;

use Phproberto\Joomla\Entity\Acl\Acl;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Tests\Stubs\Entity;
use Phproberto\Joomla\Entity\Tests\Acl\Stubs\EntityWithAcl;
use Phproberto\Joomla\Entity\Tests\Acl\Stubs\OwnerableEntityWithAcl;
use Phproberto\Joomla\Entity\Tests\Acl\Stubs\PublishableEntityWithAcl;

/**
 * Acl decorator tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class AclTest extends \TestCase
{
	/**
	 * can returns true when can administrate component.
	 *
	 * @return  void
	 */
	public function testCanReturnsTrueWhenCanAdmin()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('canAdmin'))
			->getMock();

		$acl->expects($this->once())
			->method('canAdmin')
			->willReturn(true);

		$user = $this->getMockBuilder('MockedUser')
			->setMethods(array('isRoot'))
			->getMock();

		$user->expects($this->once())
			->method('isRoot')
			->willReturn(false);

		$reflection = new \ReflectionClass($acl);
		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($acl, $user);

		$this->assertTrue($acl->can('delete'));
	}


	/**
	 * can calls authorise when cannot admin.
	 *
	 * @return  void
	 */
	public function testCanCallsAuthoriseWhenNotAdmin()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('canAdmin'))
			->getMock();

		$acl->expects($this->exactly(2))
			->method('canAdmin')
			->willReturn(false);

		$user = $this->getMockBuilder('UserMock')
			->setMethods(array('authorise', 'isRoot'))
			->getMock();

		$user->expects($this->at(1))
			->method('authorise')
			->with($this->equalTo('core.delete'), $this->equalTo('com_phproberto.sample.999'))
			->willReturn(true);

		$user->expects($this->exactly(2))
			->method('isRoot')
			->willReturn(false);

		$user->expects($this->at(3))
			->method('authorise')
			->with($this->equalTo('core.delete'), $this->equalTo('com_phproberto.sample.999'))
			->willReturn(false);

		$reflection = new \ReflectionClass($acl);
		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($acl, $user);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('aclPrefix', 'aclAssetName'))
			->getMock();

		$entity->expects($this->exactly(2))
			->method('aclPrefix')
			->willReturn('core');

		$entity->expects($this->exactly(2))
			->method('aclAssetName')
			->willReturn('com_phproberto.sample.999');

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($acl, $entity);

		$this->assertTrue($acl->can('delete'));
		$this->assertFalse($acl->can('delete'));
	}

	/**
	 * canAdmin returns cached value.
	 *
	 * @return  void
	 */
	public function testCanAdminReturnsCachedValue()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can'))
			->getMock();

		$reflection = new \ReflectionClass($acl);
		$canAdminProperty = $reflection->getProperty('canAdmin');
		$canAdminProperty->setAccessible(true);
		$canAdminProperty->setValue($acl, false);

		$this->assertFalse($acl->canAdmin());

		$canAdminProperty->setValue($acl, true);

		$this->assertTrue($acl->canAdmin());
	}

	/**
	 * canAdmin returns true for admins.
	 *
	 * @return  void
	 */
	public function testCanAdminReturnsTrueForAdmins()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can'))
			->getMock();

		$user = $this->getMockBuilder('MockedUser')
			->setMethods(array('authorise'))
			->getMock();

		$user->expects($this->once())
			->method('authorise')
			->with('core.admin', 'com_phproberto.sample.999')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($acl, $user);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('aclAssetName'))
			->getMock();

		$entity->expects($this->once())
			->method('aclAssetName')
			->willReturn('com_phproberto.sample.999');

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canAdmin());
	}

	/**
	 * canCreate returns false if cannot create and not owner.
	 *
	 * @return  void
	 */
	public function testCanCreateReturnsFalseIfCannotCreateAndNotOwner()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->once())
			->method('can')
			->with($this->equalTo('create'))
			->willReturn(false);

		$acl->expects($this->once())
			->method('isOwner')
			->willReturn(false);

		$this->assertFalse($acl->canCreate());
	}

	/**
	 * canCreate returns true for create permission.
	 *
	 * @return  void
	 */
	public function testCanCreateReturnsTrueForCreatePermission()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->once())
			->method('can')
			->with($this->equalTo('create'))
			->willReturn(true);

		$acl->expects($this->exactly(0))
			->method('isOwner')
			->willReturn(false);

		$this->assertTrue($acl->canCreate());
	}

	/**
	 * canCreate returns true when owner and create own allowed.
	 *
	 * @return  void
	 */
	public function testCanCreateReturnsTrueWhenOwnerAndCreateOwnAllowed()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->at(0))
			->method('can')
			->with($this->equalTo('create'))
			->willReturn(false);

		$acl->expects($this->at(1))
			->method('isOwner')
			->willReturn(true);

		$acl->expects($this->at(2))
			->method('can')
			->with($this->equalTo('create.own'))
			->willReturn(true);

		$this->assertTrue($acl->canCreate());
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

		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can'))
			->getMock();

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canDelete());
	}

	/**
	 * canDelete returns false when no delete and not owner.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsFalseWhenNoDeleteAllowedAndNotOwner()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->once())
			->method('can')
			->with($this->equalTo('delete'))
			->willReturn(false);

		$acl->expects($this->once())
			->method('isOwner')
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canDelete());
	}

	/**
	 * canDelete returns false when owner and delete own not allowed.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsFalseWhenOwnerAndDeleteOwnNotAllowed()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->at(0))
			->method('can')
			->with($this->equalTo('delete'))
			->willReturn(false);

		$acl->expects($this->at(1))
			->method('isOwner')
			->willReturn(true);

		$acl->expects($this->at(2))
			->method('can')
			->with($this->equalTo('delete.own'))
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canDelete());
	}

	/**
	 * canDelete returns true when delete allowed.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsTrueWhenDeleteAllowed()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->once())
			->method('can')
			->with($this->equalTo('delete'))
			->willReturn(true);

		$acl->expects($this->exactly(0))
			->method('isOwner')
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canDelete());
	}

	/**
	 * canDelete returns true when owner and delete own allowed.
	 *
	 * @return  void
	 */
	public function testCanDeleteReturnsTrueWhenOwnerAndDeleteOwnAllowed()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->at(0))
			->method('can')
			->with($this->equalTo('delete'))
			->willReturn(false);

		$acl->expects($this->at(1))
			->method('isOwner')
			->willReturn(true);

		$acl->expects($this->at(2))
			->method('can')
			->with($this->equalTo('delete.own'))
			->willReturn(true);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canDelete());
	}

	/**
	 * canEdit return false for no edit and no owner..
	 *
	 * @return  void
	 */
	public function testCanEditReturnsFalseForNoEditAndNoOwner()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->once())
			->method('can')
			->with($this->equalTo('edit'))
			->willReturn(false);

		$acl->expects($this->once())
			->method('isOwner')
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canEdit());
	}

	/**
	 * canEdit return false for no id..
	 *
	 * @return  void
	 */
	public function testCanEditReturnsFalseForNoId()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can'))
			->getMock();

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(false);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canEdit());
	}

	/**
	 * canEdit returns true for edit permission.
	 *
	 * @return  void
	 */
	public function testCanEditReturnsTrueForEditPermission()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->once())
			->method('can')
			->with($this->equalTo('edit'))
			->willReturn(true);

		$acl->expects($this->exactly(0))
			->method('isOwner')
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canEdit());
	}

	/**
	 * canEdit returns true when owner and owner edit permission.
	 *
	 * @return  void
	 */
	public function testCanEditReturnsTrueWhenOwnerAndOwnerEditPermission()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->at(0))
			->method('can')
			->with($this->equalTo('edit'))
			->willReturn(false);

		$acl->expects($this->at(1))
			->method('isOwner')
			->willReturn(true);

		$acl->expects($this->at(2))
			->method('can')
			->with($this->equalTo('edit.own'))
			->willReturn(true);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canEdit());
	}

	/**
	 * canEditState returns false for no id.
	 *
	 * @return  void
	 */
	public function testCanEditStateReturnsFalseForNoId()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can'))
			->getMock();

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(false);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canEditState());
	}

	/**
	 * canEditState return false for no edit and no owner..
	 *
	 * @return  void
	 */
	public function testCanEditStateReturnsFalseForNoEditAndNoOwner()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->once())
			->method('can')
			->with($this->equalTo('edit.state'))
			->willReturn(false);

		$acl->expects($this->once())
			->method('isOwner')
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canEditState());
	}

	/**
	 * canEdit returns true for edit permission.
	 *
	 * @return  void
	 */
	public function testCanEditStateReturnsTrueForEditPermission()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->once())
			->method('can')
			->with($this->equalTo('edit.state'))
			->willReturn(true);

		$acl->expects($this->exactly(0))
			->method('isOwner')
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canEditState());
	}

	/**
	 * canEdit returns true when owner and owner edit permission.
	 *
	 * @return  void
	 */
	public function testCanEditStateReturnsTrueWhenOwnerAndOwnerEditPermission()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can', 'isOwner'))
			->getMock();

		$acl->expects($this->at(0))
			->method('can')
			->with($this->equalTo('edit.state'))
			->willReturn(false);

		$acl->expects($this->at(1))
			->method('isOwner')
			->willReturn(true);

		$acl->expects($this->at(2))
			->method('can')
			->with($this->equalTo('edit.state.own'))
			->willReturn(true);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canEditState());
	}

	/**
	 * canView returns false for no id.
	 *
	 * @return  void
	 */
	public function testCanViewReturnsFalseForNoId()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can'))
			->getMock();

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(false);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canView());
	}

	/**
	 * canView returns true for canEdit permission.
	 *
	 * @return  void
	 */
	public function testCanViewReturnsTrueForCanEditPermission()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('canEdit'))
			->getMock();

		$acl->expects($this->once())
			->method('canEdit')
			->willReturn(true);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canView());
	}

	/**
	 * canView returns true for canEdit permission.
	 *
	 * @return  void
	 */
	public function testCanViewReturnsTrueForCanEditStatePermission()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('canEdit', 'canEditState'))
			->getMock();

		$acl->expects($this->once())
			->method('canEdit')
			->willReturn(false);

		$acl->expects($this->once())
			->method('canEditState')
			->willReturn(true);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canView());
	}

	/**
	 * canView returns false if publishable entity not published.
	 *
	 * @return  void
	 */
	public function testCanViewReturnsFalseIfPublishableEntityNotPublished()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('isPublishedEntity'))
			->getMock();

		$acl->expects($this->once())
			->method('isPublishedEntity')
			->willReturn(false);

		$entity = $this->getMockBuilder(PublishableEntityWithAcl::class)
			->setMethods(array('hasId'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($acl, $entity);

		$this->assertFalse($acl->canView());
	}

	/**
	 * canView returns tre for no access column.
	 *
	 * @return  void
	 */
	public function testCanViewReturnsTrueForNoAccessColumn()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('canEdit', 'canEditState'))
			->getMock();

		$acl->expects($this->once())
			->method('canEdit')
			->willReturn(false);

		$acl->expects($this->once())
			->method('canEditState')
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'has'))
			->getMock();

		$entity->expects($this->once())
			->method('hasId')
			->willReturn(true);

		$entity->expects($this->once())
			->method('has')
			->with($this->equalTo('access'))
			->willReturn(false);

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$this->assertTrue($acl->canView());
	}

	/**
	 * canView returns false if access not in viewLevels.
	 *
	 * @return  void
	 */
	public function testCanViewReturnsFalseIfAccessNotInViewLevels()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('canEdit', 'canEditState'))
			->getMock();

		$acl->method('canEdit')
			->willReturn(false);

		$acl->method('canEditState')
			->willReturn(false);

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('hasId', 'isPublished', 'has', 'get'))
			->getMock();

		$entity->method('hasId')
			->willReturn(true);

		$entity->method('isPublished')
			->willReturn(true);

		$entity->method('has')
			->with($this->equalTo('access'))
			->willReturn(true);

		$entity
			->method('get')
			->with($this->equalTo('access'))
			->will($this->onConsecutiveCalls(5, 7, 12));

		$reflection = new \ReflectionClass($acl);
		$aclProperty = $reflection->getProperty('entity');
		$aclProperty->setAccessible(true);
		$aclProperty->setValue($acl, $entity);

		$user = $this->getMockBuilder('MockedUser')
			->setMethods(array('getAuthorisedViewLevels'))
			->getMock();

		$user->method('getAuthorisedViewLevels')
			->willReturn(array(2, 5, 12));

		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($acl, $user);

		$this->assertTrue($acl->canView());
		$this->assertFalse($acl->canView());
		$this->assertTrue($acl->canView());
	}

	/**
	 * Constructor sets entity.
	 *
	 * @return  void
	 */
	public function testConstructorSetsEntityAndUser()
	{
		$entity = new EntityWithAcl(666);

		$user = new User(999);

		$decorator = $this->getMockBuilder(Acl::class)
			->setConstructorArgs(array($entity, $user))
			->getMockForAbstractClass();

		$reflection = new \ReflectionClass($decorator);
		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);

		$this->assertSame($entity, $entityProperty->getValue($decorator));
		$this->assertSame($user, $userProperty->getValue($decorator));
	}

	/**
	 * isOwner returns false if no Ownerable instance.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsFalseIfNoOwnerableInstance()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can'))
			->getMock();

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(array('isOwner'))
			->getMock();

		$entity->expects($this->exactly(0))
			->method('isOwner')
			->willReturn(true);

		$reflection = new \ReflectionClass($acl);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($acl, $entity);

		$isOwnerMethod = $reflection->getMethod('isOwner');
		$isOwnerMethod->setAccessible(true);

		$this->assertFalse($isOwnerMethod->invoke($acl));
	}

	/**
	 * isOwner returns entity isOwner if Ownerable.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsEntityIsOwnerIfOwnerable()
	{
		$acl = $this->getMockBuilder(Acl::class)
			->disableOriginalConstructor()
			->setMethods(array('can'))
			->getMock();

		$entity = $this->getMockBuilder(OwnerableEntityWithAcl::class)
			->setMethods(array('isOwner'))
			->getMock();

		$entity
			->method('isOwner')
			->will($this->onConsecutiveCalls(false, true, false));

		$reflection = new \ReflectionClass($acl);

		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$entityProperty->setValue($acl, $entity);

		$isOwnerMethod = $reflection->getMethod('isOwner');
		$isOwnerMethod->setAccessible(true);

		$this->assertFalse($isOwnerMethod->invoke($acl));
		$this->assertTrue($isOwnerMethod->invoke($acl));
		$this->assertFalse($isOwnerMethod->invoke($acl));
	}
}
