<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\View;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Phproberto\Joomla\Entity\MVC\View\HTMLView;
use Phproberto\Joomla\Entity\Contracts\AssociatedEntity;

/**
 * Base item view.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ItemView extends HTMLView
{
	/**
	 * Allow to check access to the view in child classes.
	 *
	 * @param   string  $tpl  Layout being rendered
	 *
	 * @return  boolean
	 */
	protected function allowLayout($tpl = null)
	{
		if (!$this instanceof AssociatedEntity)
		{
			return true;
		}

		$entityClass = $this->entityClass();
		$entity = new $entityClass;
		$entity->bind($this->getLayoutData()['item']->getProperties(true));

		if (!$entity->acl()->canView())
		{
			$msg = Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');

			$this->addMessage($msg, 'error');

			return false;
		}

		return true;
	}


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
				'item'   => $model->getItem(),
				'return' => Factory::getApplication()->input->getString('return', ''),
				'model'  => $model
			]
		);

		if ($this instanceof AssociatedEntity)
		{
			$entityClass = $this->entityClass();
			$parts = explode('\\', $entityClass);
			$entityName = lcfirst(end($parts));
			$data['entity'] = $entityClass::find($data['item']->id)->bind($data['item']->getProperties(true));
			$data[$entityName] = $data['entity'];
		}

		return $data;
	}
}
