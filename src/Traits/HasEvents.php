<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

defined('_JEXEC') or die;

/**
 * Methods for entities that have events.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEvents
{
	/**
	 * Plugins to import for events triggered on this entity.
	 *
	 * @var  array
	 */
	protected $eventsPlugins = array('entity');

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
	protected function getDispatcher()
	{
		return \JEventDispatcher::getInstance();
	}

	/**
	 * Import a plugin type for events.
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
	private function importPlugins()
	{
		foreach ($this->eventsPlugins as $plugin)
		{
			$this->importPlugin($plugin);
		}
	}

	/**
	 * Trigger an event.
	 *
	 * @param   string  $event   Event to trigger
	 * @param   array   $params  Optional parameters
	 *
	 * @return  mixed
	 */
	public function trigger($event, $params = array())
	{
		$this->importPlugins();

		array_unshift($params, $this);

		return $this->getDispatcher()->trigger($event, $params);
	}
}
