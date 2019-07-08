<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Exception;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Contracts\ExceptionInterface;

/**
 * Invalid entity data errors.
 *
 * @since  1.0.0
 */
class InvalidEntityData extends \RuntimeException implements ExceptionInterface
{
	/**
	 * Data is empty.
	 *
	 * @param   EntityInterface  $entity  Entity with empty data
	 *
	 * @return  static
	 */
	public static function emptyData(EntityInterface $entity)
	{
		return new static('Data for entity ' . get_class($entity) . ' (id: `' . $entity->id() . '`) is empty.', 500);
	}

	/**
	 * Data is empty.
	 *
	 * @param   EntityInterface  $entity  Entity with empty data
	 *
	 * @return  static
	 */
	public static function missingPrimaryKey(EntityInterface $entity)
	{
		return new static(
			'Data for entity ' . get_class($entity) . ' (id: `' . $entity->id() . '`)'
			. ' does not contain primary key (column: ' . $entity->primaryKey() . ')',
			500
		);
	}
}
