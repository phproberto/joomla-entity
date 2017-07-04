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
	 * Get article metadata.
	 *
	 * @return  array
	 */
	public function getMetadata()
	{
		if (null === $this->metadata)
		{
			$this->metadata = $this->loadMetadata();
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
	 * Load metadata from db.
	 *
	 * @return  array
	 */
	protected function loadMetadata()
	{
		$data = [];
		$row = $this->getRow();

		if (empty($row['metadata']))
		{
			return $data;
		}

		foreach ((array) json_decode($row['metadata']) as $property => $value)
		{
			if ($value === '')
			{
				continue;
			}

			$data[$property] = $value;
		}

		return $data;
	}
}
