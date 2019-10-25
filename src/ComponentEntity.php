<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

defined('_JEXEC') || die;

use Joomla\CMS\Table\Table;
use Phproberto\Joomla\Entity\Contracts\ComponentEntityInterface;
use Phproberto\Joomla\Entity\Extensions\Entity\Traits\HasComponent;

/**
 * Entity class.
 *
 * @since   1.0.0
 */
abstract class ComponentEntity extends Entity implements ComponentEntityInterface
{
	use HasComponent;

	/**
	 * Get a table.
	 *
	 * @param   string  $name     Table name. Optional.
	 * @param   string  $prefix   Class prefix. Optional.
	 * @param   array   $options  Configuration array for the table. Optional.
	 *
	 * @return  Table
	 *
	 * @throws  \InvalidArgumentException
	 *
	 * @codeCoverageIgnore
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		try
		{
			Table::addIncludePath($this->component()->tablesFolder());
		}
		catch (\Exception $e)
		{
			// Avoid folder not found exceptions and hope joomla resolves the table
		}

		return parent::table($name, $prefix, $options);
	}

	/**
	 * Associated table prefix.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tablePrefix()
	{
		return $this->component()->prefix();
	}
}
