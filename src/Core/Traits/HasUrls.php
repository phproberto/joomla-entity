<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

/**
 * Trait for entities with urls column.
 *
 * @since   1.0.0
 */
trait HasUrls
{
	/**
	 * URLs
	 *
	 * @var  array
	 */
	protected $urls;

	/**
	 * Get the name of the column that stores urls.
	 *
	 * @return  string
	 */
	protected function getColumnUrls()
	{
		return 'urls';
	}

	/**
	 * Get this article URLs.
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  array
	 */
	public function getUrls($reload = false)
	{
		if ($reload || null === $this->urls)
		{
			$this->urls = $this->loadUrls();
		}

		return $this->urls;
	}

	/**
	 * Get the content of a column with data stored in JSON.
	 *
	 * @param   string  $property  Name of the column storing data
	 *
	 * @return  array
	 */
	abstract public function json($property);

	/**
	 * Load urls from database.
	 *
	 * @return  array
	 */
	protected function loadUrls()
	{
		$urls = array();
		$data = $this->json($this->getColumnUrls());

		if (empty($data))
		{
			return $urls;
		}

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
	 * Parse URL.
	 *
	 * @param   string  $position  URL position
	 * @param   array   $data      URLs data source from db
	 *
	 * @return  array
	 */
	private function parseUrl($position, array $data)
	{
		$url = array();

		if (empty($data['url' . $position]))
		{
			return $url;
		}

		$properties = array(
			'url'    => 'url' . $position,
			'text'   => 'url' . $position . 'text',
			'target' => 'target' . $position
		);

		foreach ($properties as $key => $property)
		{
			if (isset($data[$property]) && $data[$property] != '')
			{
				$url[$key] = $data[$property];
			}
		}

		return $url;
	}
}
