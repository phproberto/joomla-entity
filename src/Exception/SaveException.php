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
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * Errors saving entity.
 *
 * @since  1.0.0
 */
class SaveException extends \RuntimeException implements ExceptionInterface
{
	/**
	 * Entity cannot be saved.
	 *
	 * @param   EntityInterface  $entity  Entity with empty data
	 * @param   \JTable          $table   Table containing the entity data
	 *
	 * @return  static
	 */
	public static function table(EntityInterface $entity, \JTable $table)
	{
		if (!$entity->hasId())
		{
			$msg = sprintf("Save failed trying to create `%s`:</br> %s", $entity->name(), $table->getError());

			return new static($msg, 500);
		}

		$msg = sprintf("Save failed trying to save `%s`:</br> %s", $entity->name() . '::' . $entity->id(), $table->getError());

		return new static($msg, 500);
	}

	/**
	 * Entity did not pass validation.
	 *
	 * @param   EntityInterface      $entity     Entity with empty data
	 * @param   ValidationException  $exception  Validation exception
	 *
	 * @return  static
	 */
	public static function validation(EntityInterface $entity, ValidationException $exception)
	{
		if (!$entity->hasId())
		{
			$msg = sprintf("Validation failed trying to create `%s`:</br> %s", $entity->name(), $exception->getMessage());

			return new static($msg, 500);
		}

		$msg = sprintf("Validation failed trying to save `%s`:</br> %s", $entity->name() . '::' . $entity->id(), $exception->getMessage());

		return new static($msg, 500);
	}
}
