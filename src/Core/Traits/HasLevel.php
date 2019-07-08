<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Phproberto\Joomla\Entity\Core\Column;

defined('_JEXEC') or die;

/**
 * Trait for entities with a level column.
 *
 * @since  1.4.0
 */
trait HasLevel
{
	/**
	 * Check if this entity has a specific level.
	 *
	 * @param   int  $level  Level to check for.
	 *
	 * @return  boolean
	 */
	public function hasLevel($level)
	{
		$column = $this->levelColumn();
		$data = $this->all();

		if (!array_key_exists($column, $data))
		{
			return false;
		}

		return (int) $data[$column] === (int) $level;
	}


	/**
	 * Check if this entity is between 2 levels.
	 *
	 * @param   int  $minLevel  Minimum level
	 * @param   int  $maxLevel  Maximum level
	 *
	 * @return  boolean
	 */
	public function hasLevelBetween($minLevel, $maxLevel)
	{
		$column = $this->levelColumn();
		$data = $this->all();

		if (!array_key_exists($column, $data))
		{
			return false;
		}

		if ($this->hasLevelLower($minLevel))
		{
			return false;
		}

		return !$this->hasLevelGreater($maxLevel);
	}

	/**
	 * Check if this entity has a level greater than specified.
	 *
	 * @param   int  $level  Level to chec for.
	 *
	 * @return  boolean
	 */
	public function hasLevelGreater($level)
	{
		$column = $this->levelColumn();
		$data = $this->all();

		if (!array_key_exists($column, $data))
		{
			return false;
		}

		return (int) $data[$column] > (int) $level;
	}

	/**
	 * Check if this entity has a level lower than specified.
	 *
	 * @param   int  $level  Level to chec for.
	 *
	 * @return  boolean
	 */
	public function hasLevelLower($level)
	{
		$column = $this->levelColumn();
		$data = $this->all();

		if (!array_key_exists($column, $data))
		{
			return false;
		}

		return (int) $data[$column] < (int) $level;
	}

	/**
	 * Retrieve this entity level.
	 *
	 * @return  integer
	 *
	 * @since   1.4.1
	 */
	public function level()
	{
		$column = $this->levelColumn();

		if (!$this->has($column))
		{
			return 0;
		}

		return (int) $this->get($column);
	}

	/**
	 * Retrieve the column that will be used for level.
	 *
	 * @return  string
	 */
	protected function levelColumn()
	{
		return $this->columnAlias(Column::LEVEL);
	}
}
