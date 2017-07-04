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
 * Article entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Article extends Entity
{
	use CategoriesTraits\HasCategory, CoreTraits\HasAsset, EntityTraits\HasImages, EntityTraits\HasMetadata, EntityTraits\HasParams, EntityTraits\HasState;

	/**
	 * URLs
	 *
	 * @var  array
	 */
	protected $urls;

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
	 * Get this article URLs.
	 *
	 * @return  array
	 */
	public function getUrls()
	{
		if (null === $this->urls)
		{
			$this->urls = $this->loadUrls();
		}

		return $this->urls;
	}

	/**
	 * Has this article an full text image?
	 *
	 * @return  boolean
	 */
	public function hasFullTextImage()
	{
		return array_key_exists('full', $this->getImages());
	}

	/**
	 * Has this article an intro image?
	 *
	 * @return  boolean
	 */
	public function hasIntroImage()
	{
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
	 * Load urls from database.
	 *
	 * @return  array
	 */
	protected function loadUrls()
	{
		$row = $this->getRow();

		if (empty($row['urls']))
		{
			return [];
		}

		$data = (array) json_decode($row['urls']);

		$urls = [];

		for ($i = 'a'; $i < 'd'; $i++)
		{
			if ($url = $this->parseUrl($i, $data))
			{
				$urls[$i] = $url;
			}
		}

		return $urls;
	}

	/**
	 * Parse data
	 *
	 * @param   array   $properties  Properties to process
	 * @param   array   $data        Array containing the JSON decoded data
	 *
	 * @return  array
	 */
	private function parseJsonDecodedProperties($properties, array $data)
	{
		$output = [];

		foreach ($properties as $key => $property)
		{
			if (isset($data[$property]) && $data[$property] != '')
			{
				$output[$key] = $data[$property];
			}
		}

		return $output;
	}

	/**
	 * Parse URL.
	 *
	 * @param   string  $position  URL position
	 * @param   array   $data      URLs data source from db
	 *
	 * @return  array
	 */
	private function parseUrl($position, array $data)
	{
		if (empty($data['url' . $position]))
		{
			return [];
		}

		return $this->parseJsonDecodedProperties(
			[
				'url'    => 'url' . $position,
				'text'   => 'url' . $position . 'text',
				'target' => 'target' . $position
			],
			$data
		);
	}
}
