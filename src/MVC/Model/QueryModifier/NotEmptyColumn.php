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
 * Modifier to check that a column is not empty.
 *
 * @since  __DEPLOY_VERSION__
 */
class NotEmptyColumn extends BaseQueryModifier implements QueryModifierInterface
{
	/**
	 * Column where search for values.
	 *
	 * @var  string
	 */
	protected $column;

	/**
	 * Vales that will be considered empty.
	 *
	 * @var  array
	 */
	protected $emptyValues = [
		'', '0', '0000-00-00', '0000-00-00 00:00:00'
	];

	/**
	 * Constructor.
	 *
	 * @param   \JDatabaseQuery  $query     Query to modify
	 * @param   string           $column    Column where search for NULL value.
	 * @param   callable|null    $callback  Callback to execute if there are values found.
	 */
	public function __construct(\JDatabaseQuery$query, string $column, callable $callback = null)
	{
		parent::__construct($query, $callback);

		$this->column = $column;
	}

	/**
	 * Apply the query filter.
	 *
	 * @return  void
	 */
	public function apply()
	{
		$this->callback();

		$db = $this->getDbo();

		$values = array_map([$db, 'quote'], $this->emptyValues);

		$this->query->where($db->qn($this->column) . ' IS NOT NULL')
			->where($db->qn($this->column) . ' NOT IN (' . implode(', ', $values) . ')');
	}
}
