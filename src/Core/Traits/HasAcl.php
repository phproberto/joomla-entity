<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

/**
 * Trait for entities with ACL.
 *
 * @since   __DEPLOY_VERSION__
 */
trait HasAcl
{
	/**
	 * Is current user admin of this entity?
	 *
	 * @var  boolean
	 */
	private $isAdmin;

	/**
	 * Retrieve the associated component.
	 *
	 * @return  Component
	 */
	abstract public function component();

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is not set or is null
	 *
	 * @return  mixed
	 */
	abstract public function get($property, $default = null);

	/**
	 * Check if entity has a property.
	 *
	 * @param   string   $property  Entity property name
	 *
	 * @return  boolean
	 */
	abstract public function has($property);

	/**
	 * Check if this entity has an id.
	 *
	 * @return  boolean
	 */
	abstract public function hasId();

	/**
	 * Get the entity identifier.
	 *
	 * @return  integer
	 */
	abstract public function id();

	/**
	 * Get this entity name.
	 *
	 * @return  string
	 */
	abstract public function name();

	/**
	 * Get the ACL prefix applied to this entity
	 *
	 * @return  string
	 */
	protected function aclPrefix()
	{
		return 'core';
	}

	/**
	 * Get the identifier of the project asset
	 *
	 * @return  string
	 */
	protected function assetName()
	{
		if ($this->hasId())
		{
			return $this->component()->option() . '.' . $this->name() . '.' . $this->id();
		}

		return $this->component()->option();
	}

	/**
	 * Check if current user has permission to perform an action
	 *
	 * @param   string  $action  The action. Example: core.create
	 *
	 * @return  boolean
	 */
	public function canDo($action)
	{
		if ($this->isAdmin())
		{
			return true;
		}

		return $this->user()->authorise($action, $this->assetName());
	}

	/**
	 * Check if current user can edit this entity.
	 *
	 * @return  boolean
	 */
	public function canEdit()
	{
		if ($this->canDo($this->aclPrefix() . '.edit'))
		{
			return true;
		}

		if (!$this->isOwner())
		{
			return false;
		}

		return $this->canDo($this->aclPrefix() . '.edit.own');
	}

	/**
	 * Check if active user is admin for this component.
	 *
	 * @return  boolean
	 */
	public function isAdmin()
	{
		if (null === $this->isAdmin)
		{
			$user = $this->user();

			$this->isAdmin = $user->get('guest') ? false : $user->authorise('core.admin', $this->component()->option());
		}

		return $this->isAdmin;
	}

	/**
	 * Check if current user is guest.
	 *
	 * @return  boolean
	 */
	public function isGuest()
	{
		return (boolean) $this->user()->get('guest');
	}

	/**
	 * Check if current user is the owner of this entity.
	 *
	 * @return  boolean
	 */
	public function isOwner()
	{
		if (!$this->hasId())
		{
			return false;
		}

		if ($this->isGuest())
		{
			return false;
		}

		if ($this->has('created_by'))
		{
			return (int) $this->get('created_by') === (int) $this->user()->get('id');
		}

		return false;
	}

	/**
	 * Get active joomla user.
	 *
	 * @return  \JUser
	 *
	 * @codeCoverageIgnore
	 */
	protected function user()
	{
		return \JFactory::getUser();
	}
}
