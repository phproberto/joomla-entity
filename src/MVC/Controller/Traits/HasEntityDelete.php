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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\MVC\JSONResponse;
use Phproberto\Joomla\Entity\Helper\ArrayHelper;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * For controllers with entity delete method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEntityDelete
{
	/**
	 * Check if an entity can be deleted.
	 *
	 * @param   EntityInterface  $entity  Entity to check permission
	 *
	 * @return  boolean
	 */
	public function activeUserCanDeleteEntity(EntityInterface $entity)
	{
		if ($entity instanceof Aclable)
		{
			return $entity->acl()->canDelete();
		}

		return Factory::getUser()->authorise('core.delete', $this->option);
	}

	/**
	 * Removes an item.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function ajaxEntityDelete(string $method = 'get')
	{
		if (!Request::active()->validateAjaxWithTokenOrCloseApp($method))
		{
			return;
		}

		return $this->jsonEntityDelete($method);
	}

	/**
	 * Removes an item.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function jsonEntityDelete(string $method = 'get')
	{
		if (!Request::active()->validateHasToken('get'))
		{
			return;
		}

		$response = new JSONResponse;

		$idRequestVar = $this->entityPrimaryKeyOnUrl();
		$ids = ArrayHelper::toPositiveIntegers($this->input->get($idRequestVar, [], 'array'));

		if (!$ids)
		{
			return $response->setStatusCode(400)
				->setErrorMessage(Text::_('JERROR_NO_ITEMS_SELECTED'))
				->send();
		}

		foreach ($ids as $id)
		{
			$entity = $this->entityInstance($id);

			if (!$this->activeUserCanDeleteEntity($entity))
			{
				return $response->setStatusCode(403)
					->setErrorMessage(Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'))
					->send();
			}
		}

		try
		{
			$entityClass = $this->entityClassOrFail();
			$entityClass::delete($ids);
		}
		catch (\Exception $e)
		{
			return $response->setStatusCode(500)
				->setErrorMessage($e->getMessage())
				->send();
		}

		return $response->setData($ids)->send();
	}

	/**
	 * Retrieve entity class and fail if it does not exist.
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException
	 */
	abstract public function entityClassOrFail();

	/**
	 * Delete one or more entities..
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  boolean
	 */
	public function entityDelete(string $method = 'get')
	{
		Request::active()->validateHasToken($method);

		$idRequestVar = $this->entityPrimaryKeyOnUrl();
		$ids = ArrayHelper::toPositiveIntegers($this->input->get($idRequestVar, [], 'array'));

		$this->setRedirect($this->entityDeleteReturnError());

		if (!$ids)
		{
			$error = Text::_('JERROR_NO_ITEMS_SELECTED');
			$this->setMessage($error, 'error');

			return false;
		}

		foreach ($ids as $id)
		{
			$entity = $this->entityInstance($id);

			if (!$this->activeUserCanDeleteEntity($entity))
			{
				$error = Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED');
				$this->setMessage($error, 'error');

				return false;
			}
		}

		try
		{
			$entityClass = $this->entityClassOrFail();
			$entityClass::delete($ids);
		}
		catch (\Exception $e)
		{
			$this->setMessage($e->getMessage(), 'error');

			return false;
		}

		$this->setRedirect($this->entityDeleteReturnOk());
		$this->setMessage(Text::plural($this->{'text_prefix'} . '_N_ITEMS_DELETED', count($ids)));

		return true;
	}

	/**
	 * URL to return if deletion works.
	 *
	 * @return  string
	 */
	public function entityDeleteReturnError()
	{
		$url = $this->input->get('returnError', null, 'base64');

		if ($url && Uri::isInternal(base64_decode($url)))
		{
			return base64_decode($url);
		}

		return $this->entityDeleteReturnOk();
	}

	/**
	 * URL to return if deletion works.
	 *
	 * @return  string
	 */
	public function entityDeleteReturnOk()
	{
		$url = $this->input->get('return', null, 'base64');

		if ($url && Uri::isInternal(base64_decode($url)))
		{
			return base64_decode($url);
		}

		// Do not tie behavior to joomla's controllers but support them
		if (!property_exists($this, 'view_list'))
		{
			return Uri::root();
		}

		$url = 'index.php?option=' . $this->option . '&view=' . $this->{'view_list'};

		// Do not tie behavior to joomla's controllers but support them
		if (method_exists($this, 'getRedirectToListAppend'))
		{
			$url .= $this->getRedirectToListAppend();
		}

		return Route::_($url);
	}
}
