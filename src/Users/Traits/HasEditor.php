<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\Column;

/**
 * Trait for entities that have an associated editor.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEditor
{
	/**
	 * Entity editor.
	 *
	 * @var  User
	 */
	protected $editor;

	/**
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	abstract public function columnAlias($column);

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is null
	 *
	 * @return  mixed
	 *
	 * @throws  \InvalidArgumentException  Property does not exist
	 */
	abstract public function get($property, $default = null);

	/**
	 * Get this entity author.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  User
	 */
	public function editor($reload = false)
	{
		if ($reload || null === $this->editor)
		{
			$this->editor = $this->loadEditor();
		}

		return $this->editor;
	}

	/**
	 * Check if this entity has an associated editor.
	 *
	 * @return  boolean
	 */
	public function hasEditor()
	{
		$editorId = (int) $this->get($this->columnAlias(Column::EDITOR));

		return !empty($editorId);
	}

	/**
	 * Load entity's editor.
	 *
	 * @return  User
	 *
	 * @throws  \InvalidArgumentException  Editor property not found
	 */
	protected function loadEditor()
	{
		$editorId = (int) $this->get($this->columnAlias(Column::EDITOR));

		return User::find($editorId);
	}
}
