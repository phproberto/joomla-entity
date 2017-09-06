<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Validation\Contracts\Rule;
use Phproberto\Joomla\Entity\Validation\Contracts\Validator as ValidatorContract;

/**
 * Entity validator.
 *
 * @since   __DEPLOY_VERSION__
 */
class Validator extends Decorator implements ValidatorContract
{
	/**
	 * Validation rules applicable to all columns.
	 *
	 * @var  array
	 */
	protected $globalRules = array();

	/**
	 * Column specific validation rules.
	 *
	 * @var  array
	 */
	protected $rules = array();

	/**
	 * Fast proxy to
	 *
	 * @param   Rule    $rule  Translation rule
	 * @param   string  $name  [optional] Name for this rule. Defaults to object hash
	 *
	 * @return  self
	 */
	public function addGlobalRule(Rule $rule, $name = null)
	{
		$name = $name ?: get_class($rule);

		$this->globalRules[$name] = $rule;

		return $this;
	}

	/**
	 * Add a validation rule for a column.
	 *
	 * @param   Rule      $rule     Rule
	 * @param   mixed     $columns  String | Array. Columns to apply rule
	 * @param   string    $name     [optional] Name for this rule. Defaults to object hash
	 *
	 * @return  self
	 */
	public function addRule(Rule $rule, $columns, $name = null)
	{
		$columns = (array) $columns;

		foreach ($columns as $column)
		{
			if (!isset($this->rules[$column]))
			{
				$this->rules[$column] = array();
			}

			$name = $name ?: get_class($rule);

			$this->rules[$column][$name] = $rule;
		}

		return $this;
	}

	/**
	 * Retrieve global translation rules.
	 *
	 * @return  array
	 */
	public function globalRules()
	{
		return $this->globalRules;
	}
	/**
	 * Check if there is a global translation rule with a specific name.
	 *
	 * @param   string  $name  Name of the rule
	 *
	 * @return  boolean
	 */
	public function hasGlobalRule($name)
	{
		return isset($this->globalRules[$name]);
	}

	/**
	 * Check if there are global rules.
	 *
	 * @return  boolean
	 */
	public function hasGlobalRules()
	{
		return !empty($this->globalRules);
	}

	/**
	 * Check if column has a validation rule.
	 *
	 * @param   string  $name    Name of the rule
	 * @param   string  $column  Column to check for rule
	 *
	 * @return  boolean
	 */
	public function hasRule($name, $column)
	{
		return !empty($this->rules[$column][$name]);
	}

	/**
	 * Check if there are validation rules set.
	 *
	 * @return  boolean
	 */
	public function hasRules()
	{
		return !empty($this->rules);
	}

	/**
	 * Check if the entity is valid.
	 *
	 * @return  boolean
	 */
	public function isValid()
	{
		try
		{
			$this->validate();
		}
		catch (\Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if a value is valid for a specific column.
	 *
	 * @param   string  $column  Column to validate against
	 * @param   mixed   $value   Value to check
	 *
	 * @return  boolean
	 */
	public function isValidColumnValue($column, $value)
	{
		try
		{
			$this->validateColumnValue($column, $value);
		}
		catch (\Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Remove a global translation rule by its name.
	 *
	 * @param   string  $name  Name of the rule to unset
	 *
	 * @return  self
	 */
	public function removeGlobalRule($name)
	{
		unset($this->globalRules[$name]);

		return $this;
	}

	/**
	 * Remove all the global rules.
	 *
	 * @return  self
	 */
	public function removeGlobalRules()
	{
		$this->globalRules = array();

		return $this;
	}

	/**
	 * Unset a rule by its name.
	 *
	 * @param   string  $column  Specific column to unset rules
	 * @param   string  $name    Name of the rule to unset
	 *
	 * @return  self
	 */
	public function removeRule($column, $name)
	{
		unset($this->rules[$column][$name]);

		return $this;
	}

	/**
	 * Remove all the column translation rules.
	 *
	 * @return  self
	 */
	public function removeRules()
	{
		$this->rules = array();

		return $this;
	}

	/**
	 * Retrieve translation rules.
	 *
	 * @param   string  $column  [optional] Only retrieve rules for specified column
	 *
	 * @return  array
	 */
	public function rules($column = null)
	{
		if (!$column)
		{
			return $this->rules;
		}

		return isset($this->rules[$column]) ? $this->rules[$column] : array();
	}

	/**
	 * Validate entity.
	 *
	 * @return  boolean
	 *
	 * @throws  \Exception
	 */
	public function validate()
	{
		$errors = array();

		$data = $this->entity->all();

		$validableColumns = array_merge(array_keys($data), array_keys($this->rules));

		foreach ($validableColumns as $column)
		{
			$value = isset($data[$column]) ? $data[$column] : null;

			try
			{
				$this->validateColumnValue($column, $value);
			}
			catch (\Exception $e)
			{
				$errors[] = $e->getMessage();
			}
		}

		if (!empty($errors))
		{
			$msg = sprintf("Entity `%s` is not valid:\n\t* ", $this->entity->name() . '::' . $this->entity->id())
				. implode("\n\t* ", $errors);

			throw new \Exception($msg);
		}

		return empty($error);
	}

	/**
	 * Validate a column value.
	 *
	 * @param   string  $column  Column to check value against
	 * @param   mixed   $value   Value for the column. Null to use current column value.
	 *
	 * @return  boolean
	 *
	 * @throws  \Exception
	 */
	public function validateColumnValue($column, $value)
	{
		foreach ($this->globalRules() as $name => $rule)
		{
			if (!$rule->passes($value))
			{
				$msg = sprintf("Column `%s` value does not pass `%s` validation rule", $column, $name);

				throw new \Exception($msg);
			}
		}

		foreach ($this->rules($column) as $name => $rule)
		{
			if (!$rule->passes($value))
			{
				$msg = sprintf("Column `%s` value does not pass `%s` validation rule", $column, $name);

				throw new \Exception($msg);
			}
		}

		return true;
	}
}
