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
 * Tables with fixed getProperties method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasFixedGetProperties
{
	/**
	 * Override the shit JObject getProperties method
	 *
	 * @param   boolean  $public  Load only public properties?
	 *
	 * @return  array
	 */
	public function getProperties($public = true)
	{
		if ($public)
		{
			$vars = [];
			$object = new \ReflectionObject($this);
			$properties = $object->getProperties(\ReflectionProperty::IS_PUBLIC);

			foreach ($properties as $property)
			{
				// For B/C we will keep the shit underscore private identification
				if ('_' == substr($property->name, 0, 1))
				{
					continue;
				}

				$vars[$property->name] = $this->{$property->name};
			}

			return $vars;
		}

		return get_object_vars($this);
	}
}
