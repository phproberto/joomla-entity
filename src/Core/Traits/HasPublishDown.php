<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Column;

/**
 * Trait for entities that have an associated publish down column.
 *
 * @since  1.0.0
 */
trait HasPublishDown
{
	/**
	 * Get the empty date for the active DB driver.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	abstract protected function nullDate();

	/**
	 * Get the publish down date.
	 *
	 * @return  string
	 */
	public function getPublishDown()
	{
		return $this->get($this->columnAlias(Column::PUBLISH_DOWN));
	}

	/**
	 * Has this entity a publish down date?
	 *
	 * @return  boolean
	 */
	public function hasPublishDown()
	{
		$publishDown = $this->getPublishDown();

		return !empty($publishDown) && $publishDown !== $this->nullDate();
	}

	/**
	 * Check if this entity is published down.
	 *
	 * @return  boolean
	 */
	public function isPublishedDown()
	{
		if (!$this->hasPublishDown())
		{
			return false;
		}

		return \JFactory::getDate($this->getPublishDown()) <= \JFactory::getDate();
	}
}
