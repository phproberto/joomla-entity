<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Decorator;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Core\Contracts\Translator as TranslatorInterface;
use Phproberto\Joomla\Entity\Core\Contracts\Translatable;

/**
 * Entity translation.
 *
 * @since   __DEPLOY_VERSION__
 */
class Translator extends Decorator implements TranslatorInterface
{
	/**
	 * Values that will be detected as empty.
	 *
	 * @var  array
	 */
	protected $emptyValues;

	/**
	 * Translation language tag.
	 *
	 * @var  string
	 */
	protected $langTag;

	/**
	 * Global translation rules.
	 *
	 * @var  array
	 */
	protected $globalRules = array();

	/**
	 * Column specific translation rules.
	 *
	 * @var  array
	 */
	protected $rules = array();

	/**
	 * Entity translation.
	 *
	 * @var  EntityInterface
	 */
	protected $translation;

	/**
	 * Constructor.
	 *
	 * @param   EntityInterface  $entity   Entity to decorate.
	 * @param   string           $langTag  Language tag. Example: es-ES
	 */
	public function __construct(Translatable $entity, $langTag = null)
	{
		parent::__construct($entity);

		$this->langTag = $langTag ?: $this->activeLanguage()->getTag();
	}

	/**
	 * Get the active language.
	 *
	 * @return  \Joomla\CMS\Language\Language
	 *
	 * @codeCoverageIgnore
	 */
	public function activeLanguage()
	{
		return \JFactory::getLanguage();
	}

	/**
	 * Fast proxy to
	 *
	 * @param   callable  $rule  Translation rule
	 * @param   string    $name  [optional] Name for this rule. Defaults to object hash
	 *
	 * @return  self
	 */
	public function addGlobalRule(callable $rule, $name = null)
	{
		$name = $name ?: spl_object_hash($rule);

		$this->globalRules[$name] = $rule;

		return $this;
	}

	/**
	 * Add a translation rule for a column.
	 *
	 * @param   callable  $rule    Translation rule
	 * @param   string    $column  Column name
	 * @param   string    $name    [optional] Name for this rule. Defaults to object hash
	 *
	 * @return  self
	 */
	public function addRule(callable $rule, $column, $name = null)
	{
		if (!isset($this->rules[$column]))
		{
			$this->rules[$column] = array();
		}

		$name = $name ?: spl_object_hash($rule);

		$this->rules[$column][$name] = $rule;

		return $this;
	}

	/**
	 * Default values recognised as empty by the translator.
	 *
	 * @return  array
	 */
	protected function defaultEmptyValues()
	{
		return array(null, '', $this->nullDate());
	}

	/**
	 * Values that will cause translation to return default.
	 *
	 * @return  array
	 */
	protected function emptyValues()
	{
		if (null === $this->emptyValues)
		{
			return $this->defaultEmptyValues();
		}

		return $this->emptyValues;
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
	 * Check if column has a translation rule.
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
	 * Check if there are rules set.
	 *
	 * @return  boolean
	 */
	public function hasRules()
	{
		return !empty($this->rules);
	}

	/**
	 * Prevent that empty values are returned by the translator for a specific column.
	 *
	 * @param   string  $column  Column where empty values will be disabled
	 *
	 * @return  self
	 */
	public function noEmptyColumnValues($column)
	{
		$this->addRule(
			function ($value)
			{
				return !in_array($value, $this->emptyValues(), true);
			},
			$column,
			'noEmptyColumnValues'
		);

		return $this;
	}

	/**
	 * Prevent globally that empty values are returned by the translator.
	 *
	 * @return  self
	 */
	public function noEmptyValues()
	{
		$this->addGlobalRule(
			function ($value)
			{
				return !in_array($value, $this->emptyValues(), true);
			},
			'noEmptyValues'
		);

		return $this;
	}

	/**
	 * Get the empty driver for the active DB driver.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function nullDate()
	{
		return \JFactory::getDbo()->getNullDate();
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
	 * Unset a rule by its name.
	 *
	 * @param   string  $name    Name of the rule to unset
	 * @param   string  $column  Specific column to unset rules
	 *
	 * @return  self
	 */
	public function removeRule($name, $column)
	{
		unset($this->rules[$column][$name]);

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
	 * Set the values that will be detected as empty.
	 *
	 * @param   array  $values  Values to use as empty
	 *
	 * @return  self
	 */
	public function setEmptyValues(array $values)
	{
		$this->emptyValues = $values;

		return $this;
	}

	/**
	 * Translate a column.
	 *
	 * @param   string  $column   Column to translate
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public function translate($column, $default = null)
	{
		$value = $this->translation()->get($column);

		return $this->isValidValue($value, $column) ? $value : $default;
	}

	/**
	 * Retrieve translation entity.
	 *
	 * @return  EntityInterface
	 */
	protected function translation()
	{
		return $this->entity->translation($this->langTag);
	}

	/**
	 * Check if a value is valid for a specific column.
	 *
	 * @param   mixed   $value   Value to check
	 * @param   string  $column  Column to validate against
	 *
	 * @return  boolean
	 */
	protected function isValidValue($value, $column)
	{
		foreach ($this->globalRules() as $rule)
		{
			if (!$rule($value))
			{
				return false;
			}
		}

		foreach ($this->rules($column) as $rule)
		{
			if (!$rule($value))
			{
				return false;
			}
		}

		return true;
	}
}
