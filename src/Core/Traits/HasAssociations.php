<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

/**
 * Trait for entities with associations.
 *
 * @since   1.0.0
 */
trait HasAssociations
{
	/**
	 * Language associations.
	 *
	 * @var  array
	 */
	protected $associations;

	/**
	 * Get an association by its language tag.
	 *
	 * @param   string  $langTag  Language tag
	 *
	 * @return  \stdClass
	 */
	public function association($langTag)
	{
		$associations = $this->associations();

		if (!array_key_exists($langTag, $associations))
		{
			$msg = sprintf('Entity %d does not have %s association', $this->id(), $langTag);

			throw new \InvalidArgumentException($msg);
		}

		return $associations[$langTag];
	}

	/**
	 * Get entity's language associations.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  \stdClass[]
	 */
	public function associations($reload = false)
	{
		if ($reload || null === $this->associations)
		{
			$this->associations = $this->loadAssociations();
		}

		return $this->associations;
	}

	/**
	 * Get the ids of the entity's language associations.
	 *
	 * @return  array
	 */
	public function associationsIds()
	{
		return array_filter(
			array_map(
				function ($association)
				{
					return (int) $association->id;
				},
				$this->associations()
			)
		);
	}

	/**
	 * Check if this entity has a specific association.
	 *
	 * @param   string   $langTag  Language tag
	 *
	 * @return  boolean
	 */
	public function hasAssociation($langTag)
	{
		return array_key_exists($langTag, $this->associations());
	}

	/**
	 * Check if this entity has an association by its id.
	 *
	 * @param   integer  $id  Association identifier
	 *
	 * @return  boolean
	 */
	public function hasAssociationById($id)
	{
		return in_array((int) $id, $this->associationsIds(), true);
	}

	/**
	 * Check if this entity has associations.
	 *
	 * @return  boolean
	 */
	public function hasAssociations()
	{
		return !empty($this->associations());
	}

	/**
	 * Load associations from DB.
	 *
	 * @return  \stdClass[]
	 */
	abstract protected function loadAssociations();
}
