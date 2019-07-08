<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Column;

/**
 * Trait for entities with metadata. Based on metadata columns.
 *
 * @since   1.0.0
 */
trait HasMetadata
{
	/**
	 * Metadata
	 *
	 * @var  array
	 */
	protected $metadata;

	/**
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	abstract public function columnAlias($column);

	/**
	 * Get the content of a column with data stored in JSON.
	 *
	 * @param   string  $property  Name of the column storing data
	 *
	 * @return  array
	 */
	abstract public function json($property);

	/**
	 * Get article metadata.
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  array
	 */
	public function metadata($reload = false)
	{
		if ($reload || null === $this->metadata)
		{
			$this->metadata = $this->json($this->columnAlias(Column::METADATA));
		}

		return $this->metadata;
	}
}
