<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Categories\Traits as CategoriesTraits;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Traits as EntityTraits;

/**
 * Stub to test Entity class.
 *
 * @since   __DEPLOY_VERSION__
 */
class Article extends Entity
{
	use CategoriesTraits\HasCategory, CoreTraits\HasAsset, EntityTraits\HasParams;

	/**
	 * Images.
	 *
	 * @var  array
	 */
	protected $images;

	/**
	 * Get the name of the column that stores category.
	 *
	 * @return  string
	 */
	protected function getColumnCategory()
	{
		return 'catid';
	}

	/**
	 * Get the name of the column that stores params.
	 *
	 * @return  string
	 */
	protected function getColumnParams()
	{
		return 'attribs';
	}

	/**
	 * Get the full text image.
	 *
	 * @return  array
	 */
	public function getFullTextImage()
	{
		$images = $this->getImages();

		return array_key_exists('full', $images) ? $images['full'] : [];
	}

	/**
	 * Get article images.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  array
	 */
	public function getImages($reload = false)
	{
		if ($reload || null === $this->images)
		{
			$this->images = $this->loadImages();
		}

		return $this->images;
	}

	/**
	 * Get the article intro image.
	 *
	 * @return  array
	 */
	public function getIntroImage()
	{
		$images = $this->getImages();

		return array_key_exists('intro', $images) ? $images['intro'] : [];
	}

	/**
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable
	 *
	 * @codeCoverageIgnore
	 */
	public function getTable($name = '', $prefix = null, $options = array())
	{
		$name = $name ?: 'Content';
		$prefix = $prefix ?: 'JTable';

		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Has this article an full text image?
	 *
	 * @return  boolean
	 */
	public function hasFullTextImage()
	{
		$images = $this->getImages();

		return array_key_exists('full', $this->getImages());
	}

	/**
	 * Has this article an intro image?
	 *
	 * @return  boolean
	 */
	public function hasIntroImage()
	{
		$images = $this->getImages();

		return array_key_exists('intro', $this->getImages());
	}

	/**
	 * Is this article featured?
	 *
	 * @return  boolean
	 */
	public function isFeatured()
	{
		$row = $this->getRow();

		if (empty($row['featured']))
		{
			return false;
		}

		return 1 === (int) $row['featured'];
	}

	/**
	 * Load images information.
	 *
	 * @return  array
	 */
	protected function loadImages()
	{
		$row = $this->getRow();

		if (empty($row['images']))
		{
			return [];
		}

		$data = (array) json_decode($row['images']);

		$images = [];

		if ($introImage = $this->parseImageIntro($data))
		{
			$images['intro'] = $introImage;
		}

		if ($fullImage = $this->parseImageFull($data))
		{
			$images['full'] = $fullImage;
		}

		return $images;
	}

	/**
	 * Parse full image information from images array.
	 *
	 * @param   array   $data  Images data
	 *
	 * @return  mixed   array
	 */
	private function parseImageFull(array $data)
	{
		if (empty($data['image_fulltext']))
		{
			return [];
		}

		$image = [
			'url' => $data['image_fulltext']
		];

		if (!empty($data['float_fulltext']))
		{
			$image['float'] = $data['float_fulltext'];
		}

		if (!empty($data['image_fulltext_alt']))
		{
			$image['alt'] = $data['image_fulltext_alt'];
		}

		if (!empty($data['image_fulltext_caption']))
		{
			$image['caption'] = $data['image_fulltext_caption'];
		}

		return $image;
	}

	/**
	 * Parse intro image information from images array.
	 *
	 * @param   array   $data  Images data
	 *
	 * @return  mixed   array
	 */
	private function parseImageIntro(array $data)
	{
		if (empty($data['image_intro']))
		{
			return [];
		}

		$image = [
			'url' => $data['image_intro']
		];

		if (!empty($data['float_intro']))
		{
			$image['float'] = $data['float_intro'];
		}

		if (!empty($data['image_intro_alt']))
		{
			$image['alt'] = $data['image_intro_alt'];
		}

		if (!empty($data['image_intro_caption']))
		{
			$image['caption'] = $data['image_intro_caption'];
		}

		return $image;
	}
}
