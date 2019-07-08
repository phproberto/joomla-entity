<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Validation;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;
use Phproberto\Joomla\Entity\Validation\Contracts\Rule as RuleContract;
use Phproberto\Joomla\Entity\Validation\Contracts\Validator as ValidatorContract;

/**
 * Entity validator.
 *
 * @since   1.0.0
 */
class Validator extends Decorator implements ValidatorContract
{
	/**
	 * Validation rules applicable to all columns.
	 *
	 * @var  RuleContract[]
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
	 * @param   RuleContract  $rule  Translation rule
	 *
	 * @return  self
	 */
	public function addGlobalRule(RuleContract $rule)
	{
		$this->globalRules[$rule->id()] = $rule;

		return $this;
	}

	/**
	 * Add an array of global rules.
	 *
	 * @param   array  $rules  Rules to add
	 *
	 * @return  self
	 */
	public function addGlobalRules(array $rules)
	{
		foreach ($rules as $rule)
		{
			$this->addGlobalRule($rule);
		}

		return $this;
	}

	/**
	 * Add a validation rule for a column.
	 *
	 * @param   RuleContract  $rule     Rule
	 * @param   mixed         $columns  String | Array. Columns to apply rule
	 *
	 * @return  self
	 */
	public function addRule(RuleContract $rule, $columns)
	{
		$columns = (array) $columns;

		foreach ($columns as $column)
		{
			if (!isset($this->rules[$column]))
			{
				$this->rules[$column] = array();
			}

			$this->rules[$column][$rule->id()] = $rule;
		}

		return $this;
	}

	/**
	 * Add an array of rules.
	 *
	 * @param   array  $rules  Rules to add
	 *
	 * @return  self
	 */
	public function addRules(array $rules)
	{
		foreach ($rules as $column => $columnRules)
		{
			$columnRules = is_array($columnRules) ? $columnRules : array($columnRules);

			foreach ($columnRules as $rule)
			{
				$this->addRule($rule, array($column));
			}
		}

		return $this;
	}

	/**
	 * Retrieve global translation rules.
	 *
	 * @return  RuleContract[]
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
		catch (ValidationException $e)
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

		$validableColumns = array_unique(
			array_merge(array_keys($data), array_keys($this->rules))
		);

		sort($validableColumns);

		foreach ($validableColumns as $column)
		{
			$value = isset($data[$column]) ? $data[$column] : null;

			try
			{
				$this->validateColumnValue($column, $value);
			}
			catch (ValidationException $e)
			{
				$errors[] = $e->getMessage();
			}
		}

		if (!empty($errors))
		{
			throw ValidationException::invalidEntity($this->entity, $errors);
		}

		return empty($errors);
	}

	/**
	 * Validate a column value.
	 *
	 * @param   string  $column  Column to check value against
	 * @param   mixed   $value   Value for the column. Null to use current column value.
	 *
	 * @return  boolean
	 *
	 * @throws  ValidationException
	 */
	public function validateColumnValue($column, $value)
	{
		$failedRules = array();
		$rules = array_merge($this->globalRules(), $this->rules($column));

		foreach ($rules as $rule)
		{
			if (!$rule->passes($value))
			{
				$failedRules[] = $rule;
			}
		}

		if (!empty($failedRules))
		{
			throw ValidationException::invalidColumn($column, $failedRules);
		}

		return true;
	}
}
