<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Decorator;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Core\Column;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;

/**
 * Entity ACL.
 *
 * @since   __DEPLOY_VERSION__
 */
class Acl extends Decorator
{
	/**
	 * Associated asset name.
	 *
	 * @var  string
	 */
	protected $assetName;

	/**
	 * Can user administrate entity?
	 *
	 * @var  boolean
	 */
	protected $canAdmin;

	/**
	 * User to check against.
	 *
	 * @var  User
	 */
	protected $user;

	/**
	 * Constructor.
	 *
	 * @param   EntityInterface  $entity  Entity to decorate.
	 * @param   User             $user    User to check permissions.
	 */
	public function __construct(EntityInterface $entity, User $user = null)
	{
		$this->entity = $entity;
		$this->user = $user ?: User::active();
	}

	/**
	 * Check permission.
	 *
	 * @param   string  $action  Action to check. Example: core.create
	 *
	 * @return  boolean
	 */
	public function can($action)
	{
		if ($this->user->isRoot() || $this->canAdmin())
		{
			return true;
		}

		$action = $this->entity->aclPrefix() . '.' . $action;

		return $this->user->authorise($action, $this->entity->aclAssetName());
	}

	/**
	 * Check can administrate entity.
	 *
	 * @return  boolean
	 */
	public function canAdmin()
	{
		if (null === $this->canAdmin)
		{
			$this->canAdmin = $this->user->authorise('core.admin',	$this->entity->aclAssetName());
		}

		return $this->canAdmin;
	}

	/**
	 * Check if user can create an entity.
	 *
	 * @return  boolean
	 */
	public function canCreate()
	{
		return $this->can('create') || ($this->isOwner() && $this->can('create.own'));
	}

	/**
	 * Check if user can delete associated entity.
	 *
	 * @return  boolean
	 */
	public function canDelete()
	{
		if (!$this->entity->hasId())
		{
			return false;
		}

		return $this->can('delete') || ($this->isOwner() && $this->can('delete.own'));
	}

	/**
	 * Check if current user can edit this entity.
	 *
	 * @return  boolean
	 */
	public function canEdit()
	{
		if (!$this->entity->hasId())
		{
			return false;
		}

		return $this->can('edit') || ($this->isOwner() && $this->can('edit.own'));
	}

	/**
	 * Check if user can edit this entity state.
	 *
	 * @return  boolean
	 */
	public function canEditState()
	{
		if (!$this->entity->hasId())
		{
			return false;
		}

		return $this->can('edit.state') || ($this->isOwner() && $this->can('edit.state.own'));
	}

	/**
	 * Check if user can view this entity.
	 *
	 * @return  boolean
	 */
	public function canView()
	{
		if (!$this->entity->hasId())
		{
			return false;
		}

		if ($this->canEdit() || $this->canEditState())
		{
			return true;
		}

		if (method_exists($this->entity, 'isPublished') && !$this->entity->isPublished())
		{
			return false;
		}

		return !$this->entity->has(Column::ACCESS) || in_array($this->entity->get(Column::ACCESS), $this->user->getAuthorisedViewLevels());
	}

	/**
	 * Check if user is the owner of the entity.
	 *
	 * @return  boolean
	 */
	public function isOwner()
	{
		if (!$this->entity instanceof Ownerable)
		{
			return false;
		}

		return $this->entity->isOwner();
	}
}
