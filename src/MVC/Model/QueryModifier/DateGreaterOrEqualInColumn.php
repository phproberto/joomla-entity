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
 * Modifier to select all rows with values are in a specified list of values.
 *
 * @since  __DEPLOY_VERSION__
 */
class DateGreaterOrEqualInColumn extends BaseQueryModifier implements QueryModifierInterface
{
	/**
	 * Values to search for.
	 *
	 * @var  array
	 */
	protected $values;

	/**
	 * Column where search for values.
	 *
	 * @var  string
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param   \JDatabaseQuery  $query     Query to modify
	 * @param   array            $values    Values to search for.
	 * @param   string           $column    Column where search for values.
	 * @param   callable|null    $callback  Callback to execute if there are values found.
	 */
	public function __construct(\JDatabaseQuery$query, array $values, string $column, callable $callback = null)
	{
		parent::__construct($query, $callback);

		$this->values   = $values;
		$this->column   = $column;
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
			$this->query->where(sprintf('%s >= %s', $db->qn($this->column), $value));
		}
	}
}
