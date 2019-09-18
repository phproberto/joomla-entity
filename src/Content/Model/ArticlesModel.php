<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Model;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Phproberto\Joomla\Entity\MVC\Model\ListModel;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter;
use Phproberto\Joomla\Entity\MVC\Model\State\Property;
use Phproberto\Joomla\Entity\MVC\Model\State\FilteredProperty;
use Phproberto\Joomla\Entity\MVC\Model\State\PopulableProperty;
use Phproberto\Joomla\Entity\MVC\Model\QueryModifier;

/**
 * Articles Model
 *
 * @since  __DEPLOY_VERSION__
 */
class ArticlesModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  [description]
	 *
	 * @see  JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'images', 'a.images',
				'urls', 'a.urls',
				'filter_tag',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @param   bool  $newQuery  use new query or not to improve performance
	 *
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function getListQuery($newQuery = true)
	{
		$user = Factory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery($newQuery);

		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.introtext, a.fulltext, ' .
				'a.checked_out, a.checked_out_time, ' .
				'a.catid, a.created, a.created_by, a.created_by_alias, ' .
				// Published/archived article in archive category is treats as archive article
				// If category is not published then force 0
				'CASE WHEN c.published = 2 AND a.state > 0 THEN 2 WHEN c.published != 1 THEN 0 ELSE a.state END as state,' .
				// Use created if modified is 0
				'CASE WHEN a.modified = ' . $db->quote($db->getNullDate()) . ' THEN a.created ELSE a.modified END as modified, ' .
				'a.modified_by, uam.name as modified_by_name,' .
				// Use created if publish_up is 0
				'CASE WHEN a.publish_up = ' . $db->quote($db->getNullDate()) . ' THEN a.created ELSE a.publish_up END as publish_up,' .
				'a.publish_down, a.images, a.urls, a.attribs, a.metadata, a.metakey, a.metadesc, a.access, ' .
				'a.hits, a.xreference, a.featured, a.language, ' . $query->length('a.fulltext') . ' AS readmore, a.ordering'
			)
		);

		$query->from($db->qn('#__content', 'a'));

		// Join over the categories.
		$query->select('c.title AS category_title, c.path AS category_route, c.access AS category_access, c.alias AS category_alias')
			->select('c.published, c.published AS parents_published, c.lft')
			->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Join over the users for the author and modified_by names.
		$query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author")
			->select('ua.email AS author_email')
			->join('LEFT', '#__users AS ua ON ua.id = a.created_by')
			->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

		// Join over the categories to get parent category titles
		$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias')
			->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');

		if (PluginHelper::isEnabled('content', 'vote'))
		{
			// Join on voting table
			$query->select(
				'COALESCE(NULLIF(ROUND(v.rating_sum  / v.rating_count, 0), 0), 0) AS rating,'
				. ' COALESCE(NULLIF(v.rating_count, 0), 0) as rating_count'
			)->leftJoin('#__content_rating AS v ON a.id = v.content_id');
		}

		$this->applyQueryModifiers(
			[
				new QueryModifier\ValuesInColumn($query, $this->state()->get('filter.id'), 'a.id'),
				new QueryModifier\ValuesNotInColumn($query, $this->state()->get('filter.not_id'), 'a.id'),
				new QueryModifier\ValuesInColumn($query, $this->state()->get('filter.state'), 'a.state'),
				new QueryModifier\ValuesNotInColumn($query, $this->state()->get('filter.not_state'), 'a.state')
			]
		);

		// Filter by access level.
		if ($this->state('filter.access', true))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')')
				->where('c.access IN (' . $groups . ')');
		}

		// Get the ordering modifiers
		$orderCol = $this->state->get('list.ordering') ?: 'a.ordering';
		$orderDirn = $this->state->get('list.direction') ?: 'ASC';
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Model properties.
	 *
	 * @return  array
	 */
	protected function stateProperties()
	{
		return array_merge(
			parent::stateProperties(),
			[
				'filter.id' => new FilteredProperty(
					new PopulableProperty('filter.id'),
					new Filter\PositiveInteger
				),
				'filter.notid' => new FilteredProperty(
					new PopulableProperty('filter.not_id'),
					new Filter\PositiveInteger
				),
				'filter.search' => new FilteredProperty(
					new Property('filter.search'),
					new Filter\StringQuoted
				),
				'filter.state' => new FilteredProperty(
					new PopulableProperty('filter.state'),
					new Filter\Integer
				),
				'filter.not_state' => new FilteredProperty(
					new PopulableProperty('filter.not_state'),
					new Filter\PositiveInteger
				),
				'filter.access' => new FilteredProperty(
					new Property('filter.access'),
					new Filter\Boolean
				),
				'filter.user_id' => new FilteredProperty(
					new PopulableProperty('filter.user_id'),
					new Filter\PositiveInteger
				)
			]
		);
	}
}
