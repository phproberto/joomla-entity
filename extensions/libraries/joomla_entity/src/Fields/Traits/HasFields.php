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
	protected $fields = null;

	/**
	 * Flag to know if the cached fields have values attached or not
	 *
	 * @var  boolean
	 */
	protected $withValues = false;

	/**
	 * Field values
	 *
	 * @var  array
	 */
	protected $fieldValues;

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

		/** @var Field $field */
		$field = $fields->get($id);

		return $field;
	}

	/**
	 * Retrieve a field value.
	 *
	 * @param   integer  $id       Field which value we want to retrieve.
	 * @param   mixed    $default  Value to use as default if value is null
	 * @param   boolean  $raw      Return raw field value
	 *
	 * @return  mixed
	 *
	 * @throws  \InvalidArgumentException  If field value is not set
	 */
	public function fieldValue($id, $default = null, $raw = false)
	{
		$values = $this->fieldValues();

		if (!array_key_exists($id, $values))
		{
			$msg = sprintf('Entity (`%s`) does not have a value assigned for field (`%s`) ', get_class($this), $id);

			throw new \InvalidArgumentException($msg);
		}

		return (null === $values[$id]) ? $default : $values[$id][$raw ? 'rawvalue' : 'value'];
	}

	/**
	 * Retrieve a field value by a given field name
	 *
	 * @param   integer  $name     Name of the field which value we want to retrieve.
	 * @param   mixed    $default  Value to use as default if value is null
	 * @param   boolean  $raw      Return raw field value
	 *
	 * @return  mixed
	 *
	 * @throws  \InvalidArgumentException  If field value is not set
	 */
	public function fieldValueByName($name, $default = null, $raw = false)
	{
		$values = $this->fieldValues();
		$id = array_search($name, array_column($values, 'name'));

		if ($id === false)
		{
			return $default;
		}

		return array_values($values)[$id][$raw ? 'rawvalue' : 'value'];
	}

	/**
	 * Get all the field values for this entity.
	 *
	 * @return  array
	 */
	public function fieldValues()
	{
		// Returns the cached array of values
		if (null !== $this->fieldValues)
		{
			return $this->fieldValues;
		}

		$fields = $this->fields(false, true);

		if ($fields->isEmpty())
		{
			$this->fieldValues = array();

			return $this->fieldValues;
		}

		$this->fieldValues = array();

		/** @var Field $field */
		foreach ($fields as $field)
		{
			$this->fieldValues[$field->id] = array(
				'name' => $field->get('name'),
				'value' => $field->has('value') ? $field->get('value') : '',
				'rawvalue' => $field->has('rawvalue') ? $field->get('rawvalue') : ''
			);
		}

		return $this->fieldValues;
	}

	/**
	 * Get associated fields.
	 *
	 * @param   boolean  $reload        Force data reloading
	 * @param   boolean  $attachValues  Whether to attach values to the fields or not
	 *
	 * @return  Collection
	 */
	public function fields($reload = false, $attachValues = false)
	{
		if ($reload || null === $this->fields || (!$this->withValues && $attachValues))
		{
			$this->fields = $this->loadFields($attachValues);
			$this->withValues |= $attachValues;
		}

		return new Collection($this->fields);
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
	 * @param   boolean  $attachValues  Whether to attach values to the fields or not
	 *
	 * @return  array
	 */
	protected function loadFields($attachValues = false)
	{
		$fields = array_values(
			array_map(
				function ($field)
				{
					return Field::find($field->id)->bind($field);
				},
				$this->getFieldsThroughHelper($this->fieldsContext(), $attachValues)
			)
		);

		return $fields;
	}

	/**
	 * Get fields using the fields helper
	 *
	 * @param   string   $context       Example: com_content.article
	 * @param   boolean  $attachValues  Whether to attach values to the fields or not
	 *
	 * @return  array
	 *
	 * @codeCoverageIgnore
	 */
	protected function getFieldsThroughHelper($context, $attachValues = false)
	{
		\JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		return \FieldsHelper::getFields($context, (object) $this->all(), $attachValues);
	}
}
