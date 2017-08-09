<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Exception;

use Phproberto\Joomla\Entity\Contracts\EntityInterface;

defined('_JEXEC') || die;

/**
 * Invalid entity data errors.
 *
 * @since  __DEPLOY_VERISON__
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
