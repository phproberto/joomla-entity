<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Entity;

defined('_JEXEC') || die;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Fields\Field;
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Content\Entity\Category;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Categories\Column as CategoriesColumn;
use Phproberto\Joomla\Entity\Tags\Traits\HasTags;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Fields\Traits\HasFields;
use Phproberto\Joomla\Entity\Core\CoreColumn;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Core\Contracts\Publishable;
use Phproberto\Joomla\Entity\Users\Traits as UsersTraits;
use Phproberto\Joomla\Entity\Categories\Traits\HasCategory;
use Phproberto\Joomla\Entity\Validation\Contracts\Validable;
use Phproberto\Joomla\Entity\Validation\Traits\HasValidation;
use Phproberto\Joomla\Entity\Translation\Contracts\Translatable;
use Phproberto\Joomla\Entity\Translation\Traits\HasTranslations;
use Phproberto\Joomla\Entity\Content\Validation\ArticleValidator;

/**
 * Article entity.
 *
 * @since   1.0.0
 */
class Article extends ComponentEntity implements Aclable, Ownerable, Publishable, Translatable, Validable
{
	use HasAcl, HasCategory, HasFields, HasTags, HasTranslations, HasValidation;
	use CoreTraits\HasAccess, CoreTraits\HasAsset, CoreTraits\HasAssociations, CoreTraits\HasFeatured, CoreTraits\HasMetadata;
	use CoreTraits\HasImages, CoreTraits\HasLink, CoreTraits\HasParams, CoreTraits\HasPublishDown, CoreTraits\HasPublishUp, CoreTraits\HasState;
	use CoreTraits\HasUrls;
	use UsersTraits\HasAuthor, UsersTraits\HasEditor, UsersTraits\HasOwner;

	/**
	 * Get the list of column aliases.
	 *
	 * @return  array
	 */
	public function columnAliases()
	{
		return array(
			'category_id' => 'catid',
			'params'      => 'attribs'
		);
	}

	/**
	 * Retrieve the alias of content type associated with this entity.
	 *
	 * @return  string
	 *
	 * @since   1.6.0
	 */
	public static function contentTypeAlias()
	{
		return 'com_content.article';
	}

	/**
	 * Default data for new instances.
	 *
	 * @return  []
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function defaults()
	{
		$accessColumn = $this->columnAlias(CoreColumn::ACCESS);
		$categoryColumn = $this->columnAlias(CategoriesColumn::CATEGORY);

		$category = $this->hasCategory() ? $this->category() : Category::uncategorised();

		return [
			$accessColumn   => (int) ($this->hasCategory() ? $this->category()->get($accessColumn) : 1),
			$categoryColumn => $category->id(),
		];
	}

	/**
	 * Get an instance of the articles model.
	 *
	 * @param   array  $state  State to populate in the model
	 *
	 * @return  \JModelList
	 */
	protected function getArticlesModel(array $state = array())
	{
		\JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

		$model = \JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		$params = isset($state['params']) ? $state['params'] : new Registry;

		$model->setState('params', $params);

		foreach ($state as $key => $value)
		{
			$model->setState($key, $value);
		}

		return $model;
	}

	/**
	 * Check if this entity is published.
	 *
	 * @return  boolean
	 */
	public function isPublished()
	{
		if (!$this->isOnState(self::STATE_PUBLISHED))
		{
			return false;
		}

		if (!$this->isPublishedUp() || $this->isPublishedDown())
		{
			return false;
		}

		return $this->category()->isPublished();
	}

	/**
	 * Load associations from DB.
	 *
	 * @return  \stdClass[]
	 *
	 * @codeCoverageIgnore
	 */
	protected function loadAssociations()
	{
		if (!$this->hasId())
		{
			return array();
		}

		return \JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $this->id());
	}

	/**
	 * Load the category from the database.
	 *
	 * @return  Category
	 */
	protected function loadCategory()
	{
		$column = $this->columnAlias('category_id');
		$data   = $this->all();

		if (array_key_exists($column, $data))
		{
			return Category::find($data[$column]);
		}

		return new Category;
	}

	/**
	 * Load the link to this entity.
	 *
	 * @return  atring
	 *
	 * @codeCoverageIgnore
	 */
	protected function loadLink()
	{
		$slug = $this->slug();

		if (!$slug)
		{
			return null;
		}

		\JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

		return \JRoute::_(\ContentHelperRoute::getArticleRoute($slug, (int) $this->get('catid'), $this->get('language')));
	}

	/**
	 * Load associated translations from DB.
	 *
	 * @return  Collection
	 */
	protected function loadTranslations()
	{
		$ids = $this->associationsIds();

		if (empty($ids))
		{
			return new Collection;
		}

		$state = array(
			'filter.article_id' => array_values($ids)
		);

		$articles = array_map(
			function ($item)
			{
				return static::find($item->id)->bind($item);
			},
			$this->getArticlesModel($state)->getItems() ?: array()
		);

		return new Collection($articles);
	}

	/**
	 * Get a table instance. Defauts to \JTableContent.
	 *
	 * @param   string  $name     Table name. Optional.
	 * @param   string  $prefix   Class prefix. Optional.
	 * @param   array   $options  Configuration array for the table. Optional.
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$name   = $name ?: 'Content';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}

	/**
	 * Retrieve entity validator.
	 *
	 * @return  ArticleValidator
	 */
	public function validator()
	{
		if (null === $this->validator)
		{
			$this->validator = new ArticleValidator($this);
		}

		return $this->validator;
	}
}
