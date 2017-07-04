<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

/**
 * Trait for entities with images.
 *
 * @since   __DEPLOY_VERSION__
 */
trait HasImages
{
	/**
	 * Images.
	 *
	 * @var  array
	 */
	protected $images;

	/**
	 * Get the name of the column that stores images.
	 *
	 * @return  string
	 */
	protected function getColumnImages()
	{
		return 'images';
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
	 * Load images information.
	 *
	 * @return  array
	 */
	protected function loadImages()
	{
		$column = $this->getColumnImages();
		$row    = $this->getRow();

		if (empty($row[$column]))
		{
			return [];
		}

		$data = (array) json_decode($row[$column]);

		$images = [];

		if ($introImage = $this->parseImage('intro', $data))
		{
			$images['intro'] = $introImage;
		}

		if ($fullImage = $this->parseImage('fulltext', $data))
		{
			$images['full'] = $fullImage;
		}

		return $images;
	}

	/**
	 * Parse an image information from db data.
	 *
	 * @param   string  $name  Name of the image: intro | fulltext
	 * @param   array   $data  Data from the database
	 *
	 * @return  array
	 */
	private function parseImage($name, array $data)
	{
		$image = [];

		if (empty($data['image_' . $name]))
		{
			return $image;
		}

		$properties = [
			'url'     => 'image_' . $name,
			'float'   => 'float_' . $name,
			'alt'     => 'image_' . $name . '_alt',
			'caption' => 'image_' . $name . '_caption'
		];

		foreach ($properties as $key => $property)
		{
			if (isset($data[$property]) && $data[$property] != '')
			{
				$image[$key] = $data[$property];
			}
		}

		return $image;
	}
}
