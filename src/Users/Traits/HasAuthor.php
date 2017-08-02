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
 * Trait for entities that have an associated author.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasAuthor
{
	/**
	 * Entity author.
	 *
	 * @var  User
	 */
	protected $author;

	/**
	 * Get this entity author.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  User
	 */
	public function author($reload = false)
	{
		if ($reload || null === $this->author)
		{
			$this->author = $this->loadAuthor();
		}

		return $this->author;
	}

	/**
	 * Get the name of the column that stores state.
	 *
	 * @return  string
	 */
	protected function columnAuthor()
	{
		return $this->table()->getColumnAlias('created_by');
	}

	/**
	 * Check if this entity has an associated author.
	 *
	 * @return  boolean
	 */
	public function hasAuthor()
	{
		$authorId = (int) $this->get($this->columnAuthor());

		return !empty($authorId);
	}

	/**
	 * Load entity's author.
	 *
	 * @return  User
	 *
	 * @throws  \InvalidArgumentException  Author property not found
	 */
	protected function loadAuthor()
	{
		$authorId = (int) $this->get($this->columnAuthor());

		return User::instance($authorId);
	}
}
