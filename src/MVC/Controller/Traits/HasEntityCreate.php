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
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\MVC\JSONResponse;
use Phproberto\Joomla\Entity\Helper\ArrayHelper;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Exception\SaveException;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * For controllers with ajax save method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEntityCreate
{
	/**
	 * Get an item from its ID.
	 *
	 * @return  void
	 */
	public function ajaxEntityCreate()
	{
		if (!Request::active()->validateAjaxWithTokenOrCloseApp())
		{
			return;
		}

		return $this->jsonEntityCreate();
	}

	/**
	 * JSON entity update.
	 *
	 * @return  void
	 */
	public function jsonEntityCreate()
	{
		if (!Request::active()->validateHasToken())
		{
			return;
		}

		$response = new JSONResponse;

		$data = $this->entitySaveDataFromRequest();
		$context = $this->option . '.entityCreate.' . $this->context . '.data';
		$entity = $this->entityInstance();

		$isNew = empty($data[$entity->primaryKey()]);
		$entity->bind($data);

		if ($isNew && $entity instanceof Ownerable)
		{
			$entity->assign(
				$entity->columnAlias(UsersColumn::OWNER),
				User::active()->id()
			);
		}

		if ($entity instanceof Aclable && !$entity->acl()->canCreate())
		{
			return $response->setStatusCode(403)
				->setStatusText(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'))
				->send();
		}

		try
		{
			$entity->save();
		}
		catch (SaveException $e)
		{
			return $response->setStatusCode(500)
				->setStatusText($e->getMessage())
				->send();
		}
		catch (ValidationException $e)
		{
			return $response->setStatusCode(400)
				->setStatusText($e->getMessage())
				->send();
		}

		return $response->setData($entity->all())->send();
	}

	/**
	 * Retrieve AJAX save data form the request.
	 *
	 * @return  array
	 */
	public function entityCreateDataFromRequest()
	{
		return $this->input->post->get($this->getModel()->formControl(), [], 'array');
	}

	/**
	 * URL to return if creation works.
	 *
	 * @return  string
	 */
	public function entityCreateReturnError()
	{
		$url = $this->input->get('returnError', null, 'base64');

		if ($url && Uri::isInternal(base64_decode($url)))
		{
			return base64_decode($url);
		}

		return $this->entityCreateReturnOk();
	}

	/**
	 * URL to return if creation works.
	 *
	 * @return  string
	 */
	public function entityCreateReturnOk()
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

	/**
	 * Retrieve the associated entity class.
	 *
	 * @return  string
	 */
	abstract public function entityClass();

	/**
	 * Save entity data.
	 *
	 * @return  boolean
	 */
	public function entityCreate()
	{
		Request::active()->validateHasToken();

		$app = Factory::getApplication();
		$data = $this->entityCreateDataFromRequest();
		$context = $this->option . '.entityCreate.' . $this->context . '.data';

		$this->setRedirect($this->entityCreateReturnError());

		$entity = $this->entityInstance();

		$isNew = empty($data[$entity->primaryKey()]);
		$entity->bind($data);

		// Bind owner from active user
		if ($isNew && $entity instanceof Ownerable)
		{
			$entity->assign(
				$entity->columnAlias(UsersColumn::OWNER),
				User::active()->id()
			);
		}

		if ($entity instanceof Aclable)
		{
			$saveAllowed = $isNew ? $entity->acl()->canCreate() : $entity->acl()->canEdit();

			if (!$saveAllowed)
			{
				$app->setUserState($context, $data);

				$error = Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED');
				$this->setMessage($error, 'error');

				return false;
			}
		}

		try
		{
			$entity->save();
		}
		catch (\Exception $e)
		{
			$app->setUserState($context, $data);

			$this->setMessage($e->getMessage(), 'error');

			return false;
		}

		$this->setRedirect($this->entityCreateReturnOk());

		$app->setUserState($context, null);
		$langKey = $this->{'text_prefix'} . ($isNew && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS';
		$prefix  = Factory::getLanguage()->hasKey($langKey) ? $this->{'text_prefix'} : 'JLIB_APPLICATION';

		$this->setMessage(Text::_($prefix . ($isNew  && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS'));

		return true;
	}

	/**
	 * URL parameters containing primary key value(s).
	 *
	 * @return  string
	 */
	abstract public function entityPrimaryKeyOnUrl();
}
