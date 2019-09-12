<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller\Traits;

defined('_JEXEC') || die;

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
		$class = get_class($this);
		$reflection = new \ReflectionClass($this);

		$name = $reflection->getShortName();

		/**
		 * Namespaced entities:
		 * 		Expects controllers namespaces like:
		 * 			MyNamesPace/Controller/ArticleController
		 * 		Expects entities namespaces like:
		 * 			MyNamesPace/Entity/Article
		 */
		if (false !== strpos($class, '\\'))
		{
			$controllerNamespace = $reflection->getNamespaceName();
			$commonNamespace = strstr($controllerNamespace, '\Controller', true);
			$entityNamespace = $commonNamespace . '\Entity';

			return $entityNamespace . '\\' . str_replace('Controller', '', $name);
		}

		return str_replace('Controller', '', strstr($name, 'Controller'));
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
