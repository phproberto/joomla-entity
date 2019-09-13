<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\MVC\Model\State\FilteredState;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter;
use Phproberto\Joomla\Entity\MVC\Model\State\FilteredProperty;
use Phproberto\Joomla\Entity\MVC\Model\State\PopulableProperty;

/**
 * For list models with search functions.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasFilteredState
{
	/**
	 * Retrieve the model state.
	 *
	 * @return  FilteredState
	 */
	public function state()
	{
		return new FilteredState($this, $this->stateProperties());
	}

	/**
	 * Get the properties that will be available in this model state.
	 *
	 * @return  array
	 */
	protected function stateProperties()
	{
		return [
			'list.limit' => new FilteredProperty(
				new PopulableProperty('list.limit'),
				new Filter\Integer
			),
			'list.start' => new FilteredProperty(
				new PopulableProperty('list.start'),
				new Filter\Integer
			)
		];
	}

}
