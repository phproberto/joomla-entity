<?php
/**
 * @package     Joomla.Entity
 * @subpackage  Table
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */
namespace Phproberto\Joomla\Entity\Table\Traits;

defined('_JEXEC') || die;

/**
 * Table trait to ease properties check.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasCommonChecks
{
	/**
	 * Common validations checker
	 *
	 * @return  boolean
	 */
	protected function checkCommonValidations()
	{
		if (!$this->checkEmptyProperty('name'))
		{
			return false;
		}

		if (!$this->checkExistingAlias())
		{
			return false;
		}

		return true;
	}

	/**
	 * Generic empty property checker
	 *
	 * @param   string  $property  Name of the class property to check
	 *
	 * @return  boolean
	 */
	protected function checkEmptyProperty($property)
	{
		if (!property_exists($this, $property))
		{
			return true;
		}

		$this->{$property} = trim($this->{$property});

		if (empty($this->{$property}))
		{
			$lang = \JFactory::getLanguage();

			$langString = $this->getTextPrefix() . '_ERROR_' . strtoupper($property) . '_CANNOT_BE_EMPTY';

			if ($lang->hasKey($langString))
			{
				$error = \JText::_($langString);
			}
			else
			{
				$error = \JText::sprintf($this->getTextPrefix() . '_ERROR_PROPERTY_CANNOT_BE_EMPTY', $property);
			}

			$this->setError($error);

			return false;
		}

		return true;
	}

	/**
	 * Generic alias existing checker
	 *
	 * @return  boolean
	 */
	protected function checkExistingAlias()
	{
		if (!property_exists($this, 'alias'))
		{
			return true;
		}

		$table = clone $this;
		$loadData = array(
			'alias' => $this->alias
		);

		if (property_exists($this, 'parent_id'))
		{
			$loadData['parent_id'] = $this->{'parent_id'};
		}

		if ($table->load($loadData) && !$this->hasSameKeys($table))
		{
			$this->alias = \JApplication::stringURLSafe($this->alias);

			// Ensure that we don't automatically generate duplicated aliases
			$table = clone $this;

			while ($table->load(array('alias' => $this->alias)) && $table->id != $this->id)
			{
				$this->alias = \JString::increment($this->alias, 'dash');
			}
		}

		return true;
	}
}
