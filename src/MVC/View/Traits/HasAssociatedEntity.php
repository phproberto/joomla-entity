<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\View\Traits;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * For views with an associated entity.
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
		 * 		Expects views namespaces like:
		 * 			MyNamesPace/View/ArticleController
		 * 		Expects entities namespaces like:
		 * 			MyNamesPace/Entity/Article
		 */
		if (false !== strpos($class, '\\'))
		{
			$controllerNamespace = $reflection->getNamespaceName();
			$commonNamespace = strstr($controllerNamespace, '\View', true);
			$entityNamespace = $commonNamespace . '\Entity';

			return $entityNamespace . '\\' . str_replace('View', '', $name);
		}

		return str_replace('View', '', strstr($name, 'View'));
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

		$id = Factory::getApplication()->input->getInt($primaryKey);

		return $entityClass::load($id);
	}
}
