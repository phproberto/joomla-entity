<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') or die;

use Phproberto\Joomla\Entity\Core\Column;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * Trait for entities with a parent entity.
 *
 * @since  1.4.0
 */
trait HasParent
{
	/**
	 * Entitiy parent.
	 *
	 * @var  EntityInterface
	 */
	protected $parent;

	/**
	 * Load the parent entity.
	 *
	 * @return  EntityInterface
	 */
	protected function loadParent()
	{
		$column = $this->parentColumn();
		$data = $this->all();

		if (empty($data[$column]))
		{
			return new static;
		}

		return static::find($data[$column]);
	}

	/**
	 * Retrieve the parent entity.
	 *
	 * @return  EntityInterface
	 */
	public function parent()
	{
		if (null === $this->parent)
		{
			$this->parent = $this->loadParent();
		}

		return $this->parent;
	}

	/**
	 * Retrieve parent identifier.
	 *
	 * @return  integer
	 */
	public function parentId()
	{
		$column = $this->parentColumn();

		if (!$this->has($column))
		{
			return 0;
		}

		return (int) $this->get($column);
	}

	/**
	 * Column used to store the parent identifier.
	 *
	 * @return  string
	 */
	public function parentColumn()
	{
		return $this->columnAlias(Column::PARENT);
	}
}
