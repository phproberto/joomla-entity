<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

use Phproberto\Joomla\Traits\HasParams as CommonHasParams;
use Joomla\Registry\Registry;

/**
 * Trait for entities with params. Based on params | attribs columns.
 *
 * @since   __DEPLOY_VERSION__
 */
trait HasParams
{
	use CommonHasParams;

	/**
	 * Load parameters from database.
	 *
	 * @return  Registry
	 */
	protected function loadParams()
	{
		$row = $this->getRow();

		if (array_key_exists('params', $row))
		{
			return new Registry($row['params']);
		}

		if (array_key_exists('attribs', $row))
		{
			return new Registry($row['attribs']);
		}

		return new Registry;
	}

	/**
	 * Save parameters to database.
	 *
	 * @return  boolean
	 */
	public function saveParams()
	{
		$row = $this->getRow();

		$table = $this->getTable();

		$data = [
			$this->primaryKey => $this->getId()
		];

		if (array_key_exists('params', $row))
		{
			$data['params'] = $this->getParams()->toString();

			return $table->save($data);
		}

		if (array_key_exists('attribs', $row))
		{
			$data['attribs'] = $this->getParams()->toString();

			return $table->save($data);
		}

		return true;
	}
}
