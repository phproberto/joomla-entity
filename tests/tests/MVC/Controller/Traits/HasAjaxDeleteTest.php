<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\Content\Entity\Article;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\ControllerWithAjaxDelete;

defined('JPATH_COMPONENT') || define('JPATH_COMPONENT', JPATH_BASE . '/components/com_content');

/**
 * HasAjaxDelete trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasAjaxDeleteTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxDeleteReturnsErrorForMissingAjaxHeader()
	{
		$controller = new ControllerWithAjaxDelete;

		ob_start();

		$controller->ajaxDelete();

		$output = ob_get_clean();

		$this->assertSame('Invalid AJAX request', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxDeleteReturnsErrorForMissingCid()
	{
		$this->setupRequestWithTokenAndAjaxHeader();

		$controller = new ControllerWithAjaxDelete;

		ob_start();

		$controller->ajaxDelete();

		$output = ob_get_clean();

		$this->assertSame('JERROR_NO_ITEMS_SELECTED', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxDeleteReturnsErrorIfDeleteIsNotAllowed()
	{
		$this->setupRequestWithTokenAndAjaxHeader();

		$ids = [1, 2, 5];

		Factory::getApplication()->input->set('cid', $ids);

		$controller = $this->getMockBuilder(ControllerWithAjaxDelete::class)
			->setMethods(['allowDelete', 'entityClass'])
			->getMock();

		$controller->method('entityClass')
			->willReturn(Article::class);

		$controller->method('allowDelete')
			->will($this->onConsecutiveCalls(true, true, false));

		ob_start();

		$controller->ajaxDelete();

		$output = ob_get_clean();

		$this->assertSame('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxDeleteReturnsExpectedJson()
	{
		$this->setupRequestWithTokenAndAjaxHeader();

		$article1 = Article::create(
			[
				'title' => 'First article'
			]
		);

		$article2 = Article::create(
			[
				'title' => 'Second article'
			]
		);

		$ids = [$article1->id(), $article2->id()];

		Factory::getApplication()->input->set('cid', $ids);

		$controller = $this->getMockBuilder(ControllerWithAjaxDelete::class)
			->setMethods(['allowDelete', 'entityClass'])
			->getMock();

		$controller->method('entityClass')
			->willReturn(Article::class);

		$controller->method('allowDelete')
			->will($this->onConsecutiveCalls(true, true));

		$reloaded = Article::load($article1->id());
		$this->assertTrue($reloaded->isLoaded());

		ob_start();

		$controller->ajaxDelete();

		$output = ob_get_clean();
		$error = '';

		try
		{
			$reloaded = Article::load($article1->id());
		}
		catch (LoadEntityDataError $e)
		{

			$error = $e->getMessage();
		}

		$this->assertTrue(strlen($error) > 0);
		$this->assertSame(json_encode($ids), $output);
	}

	/**
	 * Setups current request to have a valid token and an AJAX header.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 */
	private function setupRequestWithTokenAndAjaxHeader(string $method = 'post')
	{
		Factory::getApplication()->input->{$method}->set(Session::getFormToken(), 1);
		Factory::getApplication()->input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_assets', JPATH_TEST_DATABASE . '/jos_assets.csv');

		return $dataSet;
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		Factory::$session     = $this->getMockSession();
		Factory::$config      = $this->getMockConfig();
		Factory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		Request::clearActive();

		parent::tearDown();
	}
}
