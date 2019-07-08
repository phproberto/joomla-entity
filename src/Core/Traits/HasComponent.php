<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Extension\Component;

/**
 * Trait for entities with an associated component.
 *
 * @since   1.0.0
 */
trait HasComponent
{
	/**
	 * Entity component
	 *
	 * @var  Component
	 */
	protected $component;

	/**
	 * Component option.
	 *
	 * @var string
	 */
	protected $componentOption;

	/**
	 * Retrieve the associated component.
	 *
	 * @return  Component
	 */
	public function component()
	{
		if (null === $this->component)
		{
			$this->component = $this->loadComponent();
		}

		return $this->component;
	}

	/**
	 * Try to guess component option from class prefix
	 *
	 * @return  mixed  null (not found) | string (found)
	 */
	protected function componentOption()
	{
		if (null === $this->componentOption)
		{
			$this->componentOption = $this->componentOptionFromClass();
		}

		return $this->componentOption;
	}

	/**
	 * Try to guess component option from class.
	 *
	 * @return  string
	 */
	protected function componentOptionFromClass()
	{
		$class = get_class($this);

		if (false !== strpos($class, '\\'))
		{
			$suffix = rtrim(strstr($class, 'Entity'), '\\');
			$parts = explode("\\", $suffix);

			return array_key_exists(1, $parts) ? 'com_' . strtolower($parts[1]) : null;
		}

		return  'com_' . strtolower(strstr($class, 'Entity', true));
	}

	/**
	 * Load associated component
	 *
	 * @return  Component
	 *
	 * @throws  \InvalidArgumentException  Wrong option received
	 * @throws  \RuntimeException          Component not found
	 */
	protected function loadComponent()
	{
		return Component::fromOption($this->componentOption());
	}
}
