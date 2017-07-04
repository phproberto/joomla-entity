<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

/**
 * Trait for entities with metadata. Based on metadata columns.
 *
 * @since   __DEPLOY_VERSION__
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
	 * Get the name of the column that stores metadata.
	 *
	 * @return  string
	 */
	protected function getColumnMetadata()
	{
		return 'metadata';
	}

	/**
	 * Get article metadata.
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  array
	 */
	public function getMetadata($reload = false)
	{
		if ($reload || null === $this->metadata)
		{
			$this->metadata = $this->json($this->getColumnMetadata());
		}

		return $this->metadata;
	}

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	abstract public function getRow();

	/**
	 * Get the content of a column with data stored in JSON.
	 *
	 * @param   string  $property  Name of the column storing data
	 *
	 * @return  array
	 */
	abstract public function json($property);
}
