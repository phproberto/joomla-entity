<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Extension;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Exception\InvalidEntityData;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;

/**
 * Component entity.
 *
 * @since   1.0.0
 */
class ActiveComponent extends Component
{
	/**
	 * Load the entity from the database.
	 *
	 * @return  array
	 *
	 * @throws  LoadEntityDataError  Table error loading row
	 * @throws  InvalidEntityData    Incorrect data received
	 */
	protected function fetchRow()
	{
		$option = $this->option();

		if (!$option)
		{
			throw new \RuntimeException('Unable to detect active component option');
		}

		$table = $this->table();

		if (!$table->load(array('element' => $option, 'type' => 'component')))
		{
			throw LoadEntityDataError::tableError($this, $table->getError());
		}

		$data = $table->getProperties(true);

		if (!array_key_exists($this->primaryKey(), $data))
		{
			throw InvalidEntityData::missingPrimaryKey($this);
		}

		$this->id = (int) $data[$this->primaryKey()];

		return $data;
	}

	/**
	 * Get the active option.
	 *
	 * @return  string
	 */
	public function option()
	{
		return \JApplicationHelper::getComponentName();
	}
}
