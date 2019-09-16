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
use Phproberto\Joomla\Entity\Helper\ClassName;
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
		if (!ClassName::inNamespace($this))
		{
			return str_replace('View', '', strstr(get_class($this), 'View'));
		}

		/**
		 * Namespaced entities:
		 * 		Expects views namespaces like:
		 * 			MyNamesPace/View/ArticleView
		 * 		Expects entity in:
		 * 			MyNamesPace/Article
		 * 		or:
		 * 			MyNamesPace/Entity/Article
		 */
		$entityClassName = str_replace('View', '', ClassName::withoutNamespace($this));

		$commonNamespace = ClassName::parentNamespace($this);

		$guessedClass = $commonNamespace . '\\' . $entityClassName;

		if (class_exists($guessedClass))
		{
			return $guessedClass;
		}

		return $commonNamespace . '\\Entity\\' . $entityClassName;
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
