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
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Contracts\AssociatedEntity;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * For controllers with ajax save method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEntitySave
{
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = array())
	{
		$entity = $this->entityInstance();

		if (!$entity instanceof Aclable)
		{
			return parent::allowAdd($data);
		}

		$entity->bind($data);

		if ($entity instanceof Ownerable)
		{
			$ownerColumn = $entity->columnAlias(UsersColumn::OWNER);

			$userId = $this->input->getInt($ownerColumn, User::active()->id());
			$entity->assign($ownerColumn, $userId);
		}

		return $entity->acl()->canCreate();
	}

	/**
	 * Method to check if you can edit an existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$entity = $this->entityInstance();

		$id = empty($data[$entity->primaryKey()]) ? (int) $data[$key] : (int) $data[$entity->primaryKey()];

		if (!$id)
		{
			return false;
		}

		$entity = $this->entityInstance($id);

		if (!$entity instanceof Aclable)
		{
			return parent::allowEdit($data, $key);
		}

		if ($entity instanceof Ownerable)
		{
			$ownerColumn = $entity->columnAlias(UsersColumn::OWNER);

			$userId = $this->input->getInt($ownerColumn, User::active()->id());
			$entity->assign($ownerColumn, $userId);
		}

		$entity->bind($data);

		return $entity->acl()->canEdit();
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = null)
	{
		Request::active()->validateHasToken();

		$app = Factory::getApplication();
		$data = $this->saveDataFromRequest();
		$context = $this->option . '.edit.' . $this->context . '.data';

		$entity = $this->entityInstance();
		$primaryKey = $entity->primaryKey();

		$isNew = empty($data[$primaryKey]);
		$entity->bind($data);

		$this->setRedirect($this->saveReturnError($entity));

		// Bind owner from active user
		if ($isNew && $entity instanceof Ownerable)
		{
			$entity->assign(
				$entity->columnAlias(UsersColumn::OWNER),
				User::active()->id()
			);
		}

		$allowed = $isNew ? $this->allowAdd($data) : $this->allowEdit($data);

		if (!$allowed)
		{
			$app->setUserState($context, $data);

			$error = Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED');
			$this->setMessage($error, 'error');

			return false;
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

		$app->setUserState($context, null);
		$this->setRedirect($this->saveReturnOk($entity));

		$langKey = $this->{'text_prefix'} . ($isNew && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS';
		$prefix  = Factory::getLanguage()->hasKey($langKey) ? $this->{'text_prefix'} : 'JLIB_APPLICATION';

		$this->setMessage(Text::_($prefix . ($isNew  && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS'));

		return true;
	}

	/**
	 * Retrieve AJAX save data form the request.
	 *
	 * @return  array
	 */
	public function saveDataFromRequest()
	{
		return $this->input->post->get('jform', [], 'array');
	}

	/**
	 * URL to return if creation works.
	 *
	 * @param   EntityInterface  $entity  Entity being saved
	 *
	 * @return  string
	 */
	public function saveReturnError(EntityInterface $entity)
	{
		$url = $this->input->get('returnError', null, 'base64');

		if ($url && Uri::isInternal(base64_decode($url)))
		{
			return base64_decode($url);
		}

		// Do not tie behavior to joomla's controllers but support them
		if (!property_exists($this, 'view_item'))
		{
			return $this->saveReturnOk($entity);
		}

		$url = 'index.php?option=' . $this->option . '&view=' . $this->{'view_item'};

		// Do not tie behavior to joomla's controllers but support them
		if (method_exists($this, 'getRedirectToItemAppend'))
		{
			$url .= $this->getRedirectToItemAppend($entity->id(), $entity->primaryKey());
		}

		return Route::_($url);
	}

	/**
	 * URL to return if creation works.
	 *
	 * @param   EntityInterface  $entity  Saved entity
	 *
	 * @return  string
	 */
	public function saveReturnOk(EntityInterface $entity)
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
