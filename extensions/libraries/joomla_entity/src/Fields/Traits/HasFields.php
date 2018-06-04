<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Fields\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Core\Extension\Component;
use Phproberto\Joomla\Entity\Fields\Field;

/**
 * Trait for entities that have associated fields.
 *
 * @since  1.0.0
 */
trait HasFields
{
	/**
	 * Associated fields
	 *
	 * @var  Collection
	 */
	protected $fields;

	/**
	 * Retrieve the associated component.
	 *
	 * @return  Component
	 */
	abstract public function component();

	/**
	 * Get a specfic entity field.
	 *
	 * @param   integer  $id  Field identifier
	 *
	 * @return  Field
	 *
	 * @throws  \InvalidArgumentException  Entity does not have specified field
	 */
	public function field($id)
	{
		$fields = $this->fields();

		if (!$fields->has($id))
		{
			$msg = sprintf('Entity %s does not have field %s', get_class($this), $id);

			throw new \InvalidArgumentException($msg);
		}

		return $fields->get($id);
	}

	/**
	 * Get a specific entity field by its name.
	 *
	 * @param   string  $name  Field name
	 *
	 * @return  Field
	 *
	 * @throws  \InvalidArgumentException  Entity does not have specified field
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function fieldByName($name)
	{
		foreach ($this->fields() as $field)
		{
			if ($name === $field->get('name'))
			{
				return $field;
			}
		}

		$msg = sprintf('Entity %s does not have field %s', get_class($this), $name);

		throw new \InvalidArgumentException($msg);
	}

	/**
	 * Deprecated function for getting a single field value
	 *
	 * @param   integer  $id       Field which value we want to retrieve.
	 * @param   mixed    $default  Value to use as default if value is null
	 * @param   boolean  $raw      Return raw field value
	 *
	 * @return  mixed
	 *
	 * @deprecated   Use field($id)->value() or field($id)->rawValue()
	 */
	public function fieldValue($id, $default = null, $raw = false)
	{
		if ($raw)
		{
			$value = $this->field($id)->rawValue();

			return (is_null($value) ? $default : $value);
		}

		$value = $this->field($id)->value();

		return (is_null($value) ? $default : $value);
	}

	/**
	 * Get all the field values for this entity.
	 *
	 * @param   boolean  $raw  Return raw field values
	 *
	 * @return  array
	 */
	public function fieldValues($raw = false)
	{
		$values = array();
		$fields = $this->fields();

		if ($fields->isEmpty())
		{
			return $values;
		}

		foreach ($fields as $field)
		{
			$property = $raw ? 'rawvalue' : 'value';
			$values[$field->id()] = $field->get($property);
		}

		return $values;
	}

	/**
	 * Get associated fields.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  Collection
	 */
	public function fields($reload = false)
	{
		if ($reload || null === $this->fields)
		{
			$this->fields = $this->loadFields();
		}

		return $this->fields;
	}

	/**
	 * Get the applicate context to load fields for this entity.
	 *
	 * @return  string
	 */
	protected function fieldsContext()
	{
		return $this->component()->option() . '.' . $this->name();
	}

	/**
	 * Check if this entity has a field.
	 *
	 * @param   integer  $id  Field identifier
	 *
	 * @return  boolean
	 */
	public function hasField($id)
	{
		return $this->fields()->has($id);
	}

	/**
	 * Load associated fields from DB.
	 *
	 * @return  Collection
	 */
	protected function loadFields()
	{
		$fields = array_values(
			array_map(
				function ($field)
				{
					return Field::find($field->id)->bind($field);
				},
				$this->getFieldsThroughHelper($this->fieldsContext())
			)
		);

		return new Collection($fields);
	}

	/**
	 * Get fields using the fields helper
	 *
	 * @param   string   $context  Example: com_content.article
	 *
	 * @return  array
	 *
	 * @codeCoverageIgnore
	 */
	protected function getFieldsThroughHelper($context)
	{
		\JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		return \FieldsHelper::getFields($context, (object) $this->all(), true);
	}
}
