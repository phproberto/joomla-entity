<?php
/**
 * @package     Joomla.Entity
 * @subpackage  Table
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */
namespace Phproberto\Joomla\Entity\Table;

defined('_JEXEC') || die;

/**
 * Describes methods required by tables.
 *
 * @since  __DEPLOY_VERSION__
 */
interface TableInterface
{
	/**
	 * Gets the name of the latest extending class.
	 * For a class named ContentTableArticles will return Articles
	 *
	 * @return  string
	 */
	public function getInstanceName();

	/**
	 * Get the class prefix
	 *
	 * @return  string
	 */
	public function getInstancePrefix();

	/**
	 * Compare this table with another one to see if they contain the same entity.
	 *
	 * @param   TableInterface  $table  Table to compare
	 *
	 * @return  boolean
	 */
	public function hasSameKeys(TableInterface $table);
}
