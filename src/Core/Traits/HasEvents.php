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
 * Methods for entities that have events.
 *
 * @since  1.0.0
 */
trait HasEvents
{
	/**
	 * Check if
	 *
	 * @var  boolean
	 */
	protected $eventsPluginsImported = array();

	/**
	 * Get the event dispatcher.
	 *
	 * @return  \JEventDispatcher
	 */
	protected function dispatcher()
	{
		return \JEventDispatcher::getInstance();
	}

	/**
	 * Get the plugin types that will be used by this entity.
	 *
	 * @return  array
	 */
	protected function eventsPlugins()
	{
		return array('joomla_entity');
	}

	/**
	 * Import a plugin type for triggered events.
	 *
	 * @param   string  $pluginType  Folder of the plugin
	 *
	 * @return  self
	 */
	public function importPlugin($pluginType)
	{
		if (!in_array($pluginType, $this->eventsPluginsImported))
		{
			$this->eventsPluginsImported[] = $pluginType;

			$this->importJoomlaPlugin($pluginType);
		}

		return $this;
	}

	/**
	 * Import Joomla plugin. Isolated for tests.
	 *
	 * @param   string  $pluginType  Plugin type to import
	 *
	 * @return  boolean
	 *
	 * @codeCoverageIgnore
	 */
	protected function importJoomlaPlugin($pluginType)
	{
		return \JPluginHelper::importPlugin($pluginType);
	}

	/**
	 * Import available plugins.
	 *
	 * @return  void
	 */
	protected function importPlugins()
	{
		foreach ($this->eventsPlugins() as $plugin)
		{
			$this->importPlugin($plugin);
		}
	}

	/**
	 * Trigger an entity event.
	 *
	 * @param   string  $event   Event to trigger
	 * @param   array   $params  Optional parameters for the event
	 *
	 * @return  array
	 */
	public function trigger($event, $params = array())
	{
		$this->importPlugins();

		array_unshift($params, $this);

		return $this->dispatcher()->trigger($event, $params);
	}
}
