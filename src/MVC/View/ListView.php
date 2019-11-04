<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\View;

defined('_JEXEC') || die;

use Joomla\String\Inflector;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\MVC\View\HTMLView;
use Phproberto\Joomla\Entity\Contracts\AssociatedEntity;

/**
 * Base list view.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ListView extends HTMLView
{
	/**
	 * Load layout data.
	 *
	 * @return  self
	 */
	protected function loadLayoutData()
	{
		$model = $this->getModel();

		$data = array_merge(
			parent::loadLayoutData(),
			[
				'items'         => $model->getItems(),
				'state'         => $model->getState(),
				'pagination'    => $model->getPagination(),
				'filterForm'    => $model->getFilterForm(),
				'activeFilters' => $model->getActiveFilters(),
				'model'         => $model
			]
		);

		if ($this instanceof AssociatedEntity)
		{
			$entityClass = $this->entityClass();
			$parts = explode('\\', $entityClass);
			$entityName = lcfirst(end($parts));
			$entitiesName = Inflector::getInstance()->toPlural($entityName);

			$data['entities'] = new Collection(
				array_map(
					function ($itemData) use ($entityClass)
					{
						return $entityClass::find($itemData->{'id'})->bind($itemData);
					},
					$data['items']
				)
			);

			$data[$entitiesName] = $data['entities'];
		}

		foreach ($data as $key => $value)
		{
			if ('view' === $key)
			{
				continue;
			}

			$this->{$key} = $value;
		}

		return $data;
	}
}
