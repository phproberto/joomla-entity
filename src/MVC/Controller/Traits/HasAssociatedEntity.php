<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Helper\ClassName;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * For controllers with an associated entity.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasAssociatedEntity
{
	/**
	 * Retrieve the associated entity class.
	 *
	 * @return  string
	 */
	public function entityClass()
	{
		if (!ClassName::inNamespace($this))
		{
			return str_replace('Controller', '', strstr(get_class($this), 'Controller'));
		}

		/**
		 * Namespaced entities:
		 * 		Expects controllers namespaces like:
		 * 			MyNamesPace/Controller/ArticleController
		 * 		Expects entity in:
		 * 			MyNamesPace/Article
		 * 		or:
		 * 			MyNamesPace/Entity/Article
		 */
		$entityClassName = str_replace('Controller', '', ClassName::withoutNamespace($this));

		$commonNamespace = ClassName::parentNamespace($this);

		$guessedClass = $commonNamespace . '\\' . $entityClassName;

		if (class_exists($guessedClass))
		{
			return $guessedClass;
		}

		return $commonNamespace . '\\Entity\\' . $entityClassName;
	}

	/**
	 * Retrieve an instance of the associated entity.
	 *
	 * @param   integer  $id  Identifier
	 *
	 * @return  EntityInterface
	 */
	public function entityInstance(int $id = null)
	{
		$entityClass = $this->entityClassOrFail();

		return $id ? $entityClass::find($id) : new $entityClass;
	}

	/**
	 * Retrieve entity class and fail if it does not exist.
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException
	 */
	public function entityClassOrFail()
	{
		$class = $this->entityClass();

		if (!class_exists($class))
		{
			throw new \RuntimeException(sprintf("Entity class not found `%s`", $class));
		}

		return $class;
	}

	/**
	 * URL parameters containing primary key value(s).
	 *
	 * @return  string
	 */
	public function entityPrimaryKeyOnUrl()
	{
		return 'id';
	}

	/**
	 * Retrieve an entity from the request.
	 *
	 * @param   string  $primaryKey  Column storing entity identifier. Defaults to entity primary key.
	 *
	 * @return  EntityInterface
	 *
	 * @throws  LoadEntityDataError  Table error loading row
	 * @throws  InvalidEntityData    Incorrect data received
	 */
	public function loadEntityFromRequest(string $primaryKey = null)
	{
		$entityClass = $this->entityClass();

		if (!$primaryKey)
		{
			$entity = new $entityClass;
			$primaryKey = $entity->primaryKey();
		}

		$id = $this->input->getInt($primaryKey);

		return $entityClass::load($id);
	}
}
