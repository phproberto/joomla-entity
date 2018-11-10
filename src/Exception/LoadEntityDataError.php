<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
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
class LoadEntityDataError extends \RuntimeException implements ExceptionInterface
{
	/**
	 * Data is empty.
	 *
	 * @param   EntityInterface  $entity  Entity with empty data
	 * @param   string           $error   Error returned by the dable
	 *
	 * @return  static
	 */
	public static function tableError(EntityInterface $entity, $error)
	{
		return new static('Table returned an error loading ' . get_class($entity) . ' (id: `' . $entity->id() . '`) data: ' . $error, 500);
	}
}
