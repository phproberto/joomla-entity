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
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\MVC\JSONResponse;

/**
 * For controllers with entity search method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEntitySearch
{
	/**
	 * Get an item from its ID.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 */
	public function ajaxSearch(string $method = 'post')
	{
		if (!Request::active()->validateAjaxWithTokenOrCloseApp($method))
		{
			return;
		}

		return $this->jsonSearch();
	}

	/**
	 * JSON search.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 */
	public function jsonSearch(string $method = 'post')
	{
		if (!Request::active()->validateHasToken($method))
		{
			return;
		}

		$response = new JSONResponse;

		try
		{
			$data = $this->search($this->searchOptions());
		}
		catch (\Exception $e)
		{
			return $response->setStatusCode(500)
				->setErrorMessage($e->getMessage())
				->send();
		}

		return $response->setData($data)->send();
	}

	/**
	 * Perform a search.
	 *
	 * @param   array  $options  Search options
	 *
	 * @return  array
	 */
	protected function search(array $options = [])
	{
		$searchClass = $this->searchClass();
		$searcher = new $searchClass($options);

		return [
			'entities'   => $searcher->search(),
			'pagination' => $searcher->pagination()
		];
	}

	/**
	 * Get the associated search class.
	 *
	 * @return  string
	 */
	abstract protected function searchClass();

	/**
	 * Options that will be passed to the searcher.
	 *
	 * @return  array
	 */
	protected function searchOptions()
	{
		$app = Factory::getApplication();

		$options = [
			'list.limit' => $this->input->getInt('limit', $app->getCfg('list_limit', 20)),
			'list.start' => $this->input->getInt('start', 0)
		];

		$lang = $this->input->getString('lang');

		if ($lang && $lang !== '*')
		{
			$language = Language::getInstance($lang);
			$options['filter.language'] = ['*', $language->getTag()];
		}

		return $options;
	}
}
