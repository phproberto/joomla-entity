<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

use Phproberto\Joomla\Entity\Users\User;

defined('JPATH_PLATFORM') || die;

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
	 * Get the name of the column that stores editor id.
	 *
	 * @return  string
	 */
	protected function columnEditor()
	{
		return $this->table()->getColumnAlias('modified_by');
	}

	/**
	 * Check if this entity has an associated editor.
	 *
	 * @return  boolean
	 */
	public function hasEditor()
	{
		$editorId = (int) $this->get($this->columnEditor());

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
		$editorId = (int) $this->get($this->columnEditor());

		return User::instance($editorId);
	}
}
