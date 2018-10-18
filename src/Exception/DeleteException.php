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
 * Errors deleting an entity.
 *
 * @since  1.2.0
 */
class DeleteException extends \RuntimeException implements ExceptionInterface
{
	/**
	 * Entity cannot be saved.
	 *
	 * @param   EntityInterface  $entity  Entity with empty data
	 * @param   \JTable          $table   Table containing the entity data
	 *
	 * @return  static
	 */
	public static function fromTable(EntityInterface $entity, \JTable $table)
	{
		$msg = sprintf("Delete failed trying to delete `%s`:</br> %s", $entity->name() . '::' . $entity->id(), $table->getError());

		return new static($msg, 500);
	}
}
