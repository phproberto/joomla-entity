<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation\Exception;

use Phproberto\Joomla\Entity\Validation\Contracts\Rule as RuleContract;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Contracts\ExceptionInterface;

defined('_JEXEC') || die;

/**
 * Validation errors.
 *
 * @since  __DEPLOY_VERISON__
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
		$msg = sprintf("Entity `%s` is not valid:\n\t* ", $entity->name() . '::' . $entity->id());

		if ($errors)
		{
			$msg .= implode("\n\t* ", $errors);
		}

		return new static($msg, 500);
	}

	/**
	 * Entity did not pass validation.
	 *
	 * @param   string        $column  Entity with empty data
	 * @param   RuleContract  $rule    Rule failed
	 *
	 * @return  static
	 */
	public static function columnRuleFailed($column, RuleContract $rule)
	{
		$msg = sprintf("`%s` does not pass `%s` validation rule", $column, $rule->name());

		return new static($msg, 500);
	}


}
