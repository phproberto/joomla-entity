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
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\MVC\View\ItemView;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Contracts\AssociatedEntity;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;

/**
 * Base item form view.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ItemFormView extends ItemView
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
		if ('edit' !== $this->getLayout() || !$this instanceof AssociatedEntity)
		{
			return parent::allowLayout($tpl);
		}

		$entityClass = $this->entityClass();
		$entity = new $entityClass;
		$layoutData = $this->getLayoutData();

		if (empty($layoutData['item']->getProperties(true)[$entity->primaryKey()]))
		{
			$entity->bind($layoutData['form']->getData()->toArray());
		}
		else
		{
			$entity->bind($layoutData['item']->getProperties(true));
		}

		if ($entity instanceof Ownerable && !$entity->hasId())
		{
			$ownerColumn = $entity->columnAlias(UsersColumn::OWNER);

			if ($entity->hasEmpty($ownerColumn))
			{
				$userId = Factory::getApplication()->input->getInt($ownerColumn, User::active()->id());
				$entity->assign($ownerColumn, $userId);
			}
		}

		$allowed = $entity->isLoaded() ? $entity->acl()->canEdit() : $entity->acl()->canCreate();

		if (!$allowed)
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
		return array_merge(
			parent::loadLayoutData(),
			[
				'form' => $this->getModel()->getForm()
			]
		);
	}
}
