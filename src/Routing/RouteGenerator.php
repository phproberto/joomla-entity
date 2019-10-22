<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Routing;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;

/**
 * Route generator.
 *
 * @since  __DEPLOY_VERSION__
 */
class RouteGenerator
{
	/**
	 * @var  string
	 */
	protected $option;

	/**
	 * Cached instances
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Component menu items
	 *
	 * @var  array
	 */
	protected $menuItems;

	/**
	 * Constructor
	 *
	 * @param   string  $option  Option of the component to route. Example: com_content
	 */
	public function __construct($option)
	{
		$this->option = $option;
	}

	/**
	 * Format a link
	 *
	 * @param   string   $url    Url to format
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compliance.
	 *
	 * @return  string
	 */
	protected function formatUrl($url, $xhtml = true)
	{
		return Route::_($url, $xhtml);
	}

	/**
	 * Generate link.
	 *
	 * @param   array  $vars     Variables for the URL
	 * @param   array  $options  Optional settings
	 *                            - addToken -> (bool) Add a token to the URL. Default: false
	 *                            - itemId -> (int)  Specify a custom itemId if needed. Default: use active itemid
	 *                            - return -> (string) Url to return to. Default: current url.
	 *                            - routed -> (bool) Use JRoute to process url. Default: true
	 *                            - xhtml  -> (bool) Replace & by &amp; for XML compliance. Default: true
	 *
	 * @return  string
	 */
	public function generateUrl($vars, $options = [])
	{
		$addToken = isset($options['addToken']) ? (bool) $options['addToken'] : false;

		// Allow to receive itemId in with typos
		$itemId   = isset($vars['Itemid']) ? $vars['Itemid'] : (isset($vars['itemId']) ? $vars['itemId'] : null);

		unset($vars['Itemid']);
		unset($vars['itemId']);

		// Any itemId received in options will override current vars
		if (array_key_exists('itemId', $options))
		{
			$itemId = $options['itemId'];
		}

		$return   = isset($options['return']) ? $options['return'] : null;
		$routed   = isset($options['routed']) ? (bool) $options['routed'] : true;
		$xhtml    = isset($options['xhtml']) ? (bool) $options['xhtml'] : true;

		$url = 'index.php?option=' . $this->option;

		if (!in_array($itemId, [null, 'inherit'], true))
		{
			$url .= '&Itemid=' . (int) $itemId;
		}

		foreach ($vars as $varName => $varValue)
		{
			$url .= '&' . $varName . '=' . $varValue;
		}

		if ($addToken)
		{
			$url .= '&' . Session::getFormToken() . '=1';
		}

		if ($return)
		{
			if ($return === 'current')
			{
				$return = Uri::getInstance()->toString();
			}

			$url .= '&return=' . base64_encode($return);
		}

		if (!$routed)
		{
			return $url;
		}

		return $this->formatUrl($url, $xhtml);
	}

	/**
	 * Create and return a cached instance
	 *
	 * @param   string  $option  Option of the component to route
	 *
	 * @return  static
	 */
	public static function getInstance($option = null)
	{
		$class = get_called_class();

		if (null === $option)
		{
			$option = ApplicationHelper::getComponentName();
		}

		if (empty(static::$instances[$class][$option]))
		{
			static::$instances[$class][$option] = new static($option);
		}

		return static::$instances[$class][$option];
	}

	/**
	 * Generate the Itemid string part for URLs
	 *
	 * @param   mixed  $itemId  inherit or desired itemId. Use 0 to not inherit active itemId
	 *
	 * @return  string
	 */
	protected function getLinkItemIdString($itemId = 'inherit')
	{
		if ($itemId === 'inherit')
		{
			return null;
		}

		return '&Itemid=' . (int) $itemId;
	}

	/**
	 * Get the component menu items
	 *
	 * @return  array
	 */
	protected function getMenuItems()
	{
		if (null === $this->menuItems)
		{
			$this->menuItems = $this->loadMenuItems();
		}

		return $this->menuItems;
	}

	/**
	 * Load menu menu items from database
	 *
	 * @return  array
	 */
	protected function loadMenuItems()
	{
		$menu	= Factory::getApplication()->getMenu();
		$com	= ComponentHelper::getComponent($this->getComponent());
		$items = $menu->getItems('component_id', $com->id);

		if ($items)
		{
			return $items;
		}

		return array();
	}
}
