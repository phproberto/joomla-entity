<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation\Exception;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Validation\Contracts\Rule as RuleContract;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Contracts\ExceptionInterface;

/**
 * Validation errors.
 *
 * @since  1.0.0
 */
class ValidationException extends \RuntimeException implements ExceptionInterface
{
	/**
	 * Entity did not pass validation.
	 *
	 * @param   EntityInterface  $entity  Entity with empty data
	 * @param   array            $errors  Validation errors
	 *
	 * @return  static
	 */
	public static function invalidEntity(EntityInterface $entity, array $errors = array())
	{
		$entityName = $entity->name() . ($entity->hasId() ? '::' . $entity->id() : null);
		$msg = sprintf("`%s` is not valid:</br>* ", $entityName);

		if (count($errors))
		{
			$msg .= implode("</br>* ", $errors);
		}

		return new static($msg, 500);
	}

	/**
	 * Entity did not pass validation.
	 *
	 * @param   string          $column       Entity with empty data
	 * @param   RuleContract[]  $failedRules  Rule failed
	 *
	 * @return  static
	 */
	public static function invalidColumn($column, array $failedRules)
	{
		$errors = array();

		foreach ($failedRules as $rule)
		{
			$errors[] = sprintf("`%s` does not pass `%s` validation rule", $column, $rule->name());
		}

		return new static(implode("</br>* ", $errors), 500);
	}
}
