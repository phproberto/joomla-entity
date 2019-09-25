<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller\Traits;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\Helper\ArrayHelper;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;

/**
 * For controllers with ajax delete method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasAjaxDelete
{
	/**
	 * Removes an item.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function ajaxDelete(string $method = 'post')
	{
		if (!Request::active()->validateAjaxWithTokenOrCloseApp($method))
		{
			return;
		}

		$app = Factory::getApplication();

		$cid = ArrayHelper::toPositiveIntegers($this->input->get('cid', [], 'array'));

		if (!$cid)
		{
			$app->setHeader('status', '400');
			$app->sendHeaders();
			echo Text::_('JERROR_NO_ITEMS_SELECTED');
			$app->close();

			return;
		}

		$app = Factory::getApplication();

		foreach ($cid as $id)
		{
			if (!$this->allowDelete($id))
			{
				$app->setHeader('status', '400');
				$app->sendHeaders();
				echo Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED');
				$app->close();

				return;
			}
		}

		try
		{
			$entityClass = $this->entityClassOrFail();
			$entityClass::delete($cid);
		}
		catch (\Exception $e)
		{
			$app->setHeader('status', '500');
			$app->sendHeaders();
			echo $e->getMessage();
			$app->close();

				return;
		}

		$app->sendHeaders();
		echo json_encode($cid);
		$app->close();
	}


	/**
	 * Method to check if you can delete an item?
	 *
	 * @param   integer  $id  ID of the record to delete
	 *
	 * @return  boolean
	 */
	protected function allowDelete($id)
	{
		try
		{
			$entityClass = $this->entityClassOrFail();
			$entity = $entityClass::load($id);
		}
		catch (\Exception $e)
		{
			return false;
		}

		if ($entity instanceof Aclable)
		{
			return $entity->acl()->canDelete();
		}

		$user = Factory::getUser();

		return $user->authorise('core.delete', $this->option);
	}

	/**
	 * Retrieve entity class and fail if it does not exist.
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException
	 */
	abstract public function entityClassOrFail();
}
