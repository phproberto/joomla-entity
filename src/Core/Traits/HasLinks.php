<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

use Joomla\String\Inflector;
use Phproberto\Joomla\Entity\Core\CoreColumn;
use Phproberto\Joomla\Entity\Routing\RouteGenerator;

/**
 * Trait for entities that have associated URLs.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasLinks
{
	/**
	 * Component option.
	 *
	 * @return  mixed  null (not found) | string (found)
	 */
	abstract protected function componentOption();

	/**
	 * Generate the item slug for URLs
	 *
	 * @return  string
	 */
	public function slug()
	{
		$slug = (string) $this->id();

		if (!$slug)
		{
			return null;
		}

		$aliasColumn = $this->columnAlias(CoreColumn::ALIAS);

		$data = $this->all();

		if (!empty($data[$aliasColumn]))
		{
			$slug .= '-' . $data[$aliasColumn];
		}

		return $slug;
	}

	/**
	 * Generate link.
	 *
	 * @param   array  $vars     Vars for the URL.
	 * @param   array  $options  Optional settings
	 *                            - addToken -> (bool) Add a token to the URL. Default: false
	 *                            - itemId -> (int)  Specify a custom itemId if needed. Default: use active itemid
	 *                            - return -> (string) Url to return to. Default: current url.
	 *                            - routed -> (bool) Use JRoute to process url. Default: true
	 *                            - xhtml  -> (bool) Replace & by &amp; for XML compliance. Default: true
	 *
	 * @return  string
	 */
	public function link($vars = [], $options = [])
	{
		$vars['view'] = isset($vars['view']) ? $vars['view'] : $this->linkViewName();

		return RouteGenerator::getInstance($this->componentOption())->generateUrl($vars, $options);
	}

	/**
	 * Retrieve the URL to create an entity.
	 *
	 * @param   array  $vars     Vars for the URL.
	 * @param   array  $options  Optional settings
	 *                           - itemId -> Specify a custom itemId if needed. Default: use active itemid
	 *                           - routed -> Use JRoute to process url. Default: true
	 *                           - xhtml  -> Replace & by &amp; for XML compliance. Default: true
	 *                           - return -> Url to return to. Default: current url.
	 *
	 * @return  string
	 */
	public function linkCreate($vars = [], $options = [])
	{
		$vars['task'] = $this->linkTaskPrefix() . '.add';

		$options = array_merge(
			[
				'addToken' => true,
				'xhtml'    => false
			],
			$options
		);

		return $this->link($vars, $options);
	}

	/**
	 * Retrieve the URL to delete this entity.
	 *
	 * @param   array  $vars     Vars for the URL.
	 * @param   array  $options  Optional settings
	 *                           - itemId -> Specify a custom itemId if needed. Default: use active itemid
	 *                           - routed -> Use JRoute to process url. Default: true
	 *                           - xhtml  -> Replace & by &amp; for XML compliance. Default: true
	 *                           - return -> Url to return to. Default: current url.
	 *
	 * @return  string
	 */
	public function linkDelete($vars = [], $options = [])
	{
		if (!$this->hasId())
		{
			return null;
		}

		$vars['task'] = $this->linkTaskPrefix() . '.delete';
		$vars[$this->primaryKey()] = $this->slug();

		$options = array_merge(
			[
				'addToken' => true,
				'xhtml'    => false
			],
			$options
		);

		return $this->link($vars, $options);
	}

	/**
	 * Retrieve the URL to edit this entity.
	 *
	 * @param   array  $vars     Vars for the URL.
	 * @param   array  $options  Optional settings
	 *                           - itemId -> Specify a custom itemId if needed. Default: use active itemid
	 *                           - routed -> Use JRoute to process url. Default: true
	 *                           - xhtml  -> Replace & by &amp; for XML compliance. Default: true
	 *                           - return -> Url to return to. Default: current url.
	 *
	 * @return  string
	 */
	public function linkEdit($vars = [], $options = [])
	{
		if (!$this->hasId())
		{
			return null;
		}

		$vars['task'] = $this->linkTaskPrefix() . '.edit';
		$vars[$this->primaryKey()] = $this->slug();

		$options = array_merge(
			[
				'addToken' => true,
				'xhtml'    => false
			],
			$options
		);

		return $this->link($vars, $options);
	}

	/**
	 * Base name to be used in URLs.
	 *
	 * @return  string
	 */
	public function linkTaskPrefix()
	{
		return $this->name();
	}

	/**
	 * Get the url to view this entity.
	 *
	 * @param   array  $vars     Vars for the URL.
	 * @param   array  $options  Optional settings
	 *                           - itemId -> Specify a custom itemId if needed. Default: use active itemid
	 *                           - routed -> Use JRoute to process url. Default: true
	 *                           - xhtml  -> Replace & by &amp; for XML compliance. Default: true
	 *                           - return -> Url to return to. Default: current url.
	 *
	 * @return  string
	 */
	public function linkView($vars = [], $options = [])
	{
		if (!$this->hasId())
		{
			return null;
		}

		$vars[$this->primaryKey()] = $this->slug();
		$options = array_merge(
			[
				'xhtml'    => false
			],
			$options
		);

		return $this->link($vars, $options);
	}

	/**
	 * Base name to be used in URLs.
	 *
	 * @return  string
	 */
	public function linkViewName()
	{
		return $this->name();
	}
}
