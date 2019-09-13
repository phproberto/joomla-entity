<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\QueryModifier;

defined('_JEXEC') || die;

/**
 * Modifier to select all rows with values like in a specified list of values.
 *
 * @since  __DEPLOY_VERSION__
 */
class SearchInColumns extends BaseQueryModifier implements QueryModifierInterface
{
	/**
	 * Values to search for.
	 *
	 * @var  array
	 */
	protected $values;

	/**
	 * Columns where search for values.
	 *
	 * @var  array
	 */
	protected $columns;

	/**
	 * Constructor.
	 *
	 * @param   \JDatabaseQuery  $query     Query to modify
	 * @param   array            $values    Values to search for.
	 * @param   string           $columns   Columns where search for values.
	 * @param   callable|null    $callback  Callback to execute if there are values found.
	 */
	public function __construct(\JDatabaseQuery$query, array $values, array $columns, callable $callback = null)
	{
		parent::__construct($query, $callback);

		$this->values   = $values;
		$this->columns  = $columns;
	}

	/**
	 * Apply the query filter.
	 *
	 * @return  void
	 */
	public function apply()
	{
		if (!$this->values)
		{
			return;
		}

		$this->callback();

		$db = $this->getDbo();

		foreach ($this->values as $value)
		{
			$like = $db->quote('%' . trim($value, "'") . '%');
			$whereParts = array();

			foreach ($this->columns as $column)
			{
				$whereParts[] = sprintf('%s', $db->qn($column) . ' LIKE ' . $like);
			}

			$where = sprintf('(%s)', implode(' OR ', $whereParts));

			$this->query->where($where);
		}
	}
}
