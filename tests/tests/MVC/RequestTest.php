<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Input\Input;
use Joomla\CMS\Session\Session;
use Phproberto\Joomla\Entity\MVC\Request;
use Joomla\CMS\Application\ApplicationHelper;

/**
 * Request tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class RequestTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function activeReturnsActiveInstance()
	{
		Factory::getApplication()->input->set('my-var', 'my-value');

		$this->assertSame('my-value', Request::active()->input()->get('my-var'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function activeReturnsCachedInstance()
	{
		$activeRequest = new Request(new Input(['my' => 'value']));

		$reflection = new \ReflectionClass(Request::class);
		$activeProperty = $reflection->getProperty('active');
		$activeProperty->setAccessible(true);

		$activeProperty->setValue(Request::class, $activeRequest);

		$this->assertSame($activeRequest, Request::active());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function clearActiveClearsCachedInstance()
	{
		$activeRequest = new Request(new Input(['my' => 'value']));

		$reflection = new \ReflectionClass(Request::class);
		$activeProperty = $reflection->getProperty('active');
		$activeProperty->setAccessible(true);

		$activeProperty->setValue(Request::class, $activeRequest);

		$this->assertSame($activeRequest, Request::active());

		Request::clearActive();

		$this->assertNotSame($activeRequest, Request::active());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsInput()
	{
		$input = new Input(['test' => 'me']);
		$request = new Request($input);

		$reflection = new \ReflectionClass($request);
		$inputProperty = $reflection->getProperty('input');
		$inputProperty->setAccessible(true);

		$this->assertSame($input, $inputProperty->getValue($request));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasTokenReturnsFalseWhenPostTokenNotPresent()
	{
		$this->assertFalse((new Request)->hasToken());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasTokenReturnsTrueWhenPostTokenPresent()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->post->set($requestToken, 1);

		$this->assertTrue((new Request)->hasToken());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasTokenReturnsTrueWhenGetTokenPresent()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->get->set($requestToken, 1);

		$this->assertTrue((new Request)->hasToken('get'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function inputReturnsInput()
	{
		$input = new Input(['test' => 'me']);
		$request = new Request($input);

		$this->assertSame($input, $request->input());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isAjaxReturnsFalseForNonAjaxRequests()
	{
		$this->assertFalse((new Request)->isAjax());
	}


	/**
	 * @test
	 *
	 * @return void
	 */
	public function isAjaxReturnsTrueForAjaxRequests()
	{
		$input = new Input;
		$input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');

		$this->assertTrue((new Request($input))->isAjax());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isAjaxWithTokenReturnsFalseForMissingToken()
	{
		Factory::getApplication()->input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');

		$this->assertFalse((new Request)->isAjaxWithToken());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isAjaxWithTokenReturnsFalseForMissingAjaxHeader()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->post->set($requestToken, 1);

		$this->assertFalse((new Request)->isAjaxWithToken());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isAjaxWithTokenReturnsTrueWhenExpected()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->post->set($requestToken, 1);
		Factory::getApplication()->input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');

		$this->assertTrue((new Request)->isAjaxWithToken());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateAjaxWithTokenOrCloseAppDoesNothingIfOk()
	{
		ob_start();

		(new Request)->validateAjaxWithTokenOrCloseApp();

		$output = ob_get_clean();

		$this->assertContains('Invalid AJAX request', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateAjaxWithTokenOrCloseAppClosesApp()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->post->set($requestToken, 1);
		Factory::getApplication()->input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');

		ob_start();

		(new Request)->validateAjaxWithTokenOrCloseApp();

		$output = ob_get_clean();

		$this->assertEmpty('', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateIsAjaxReturnsTrueForValidAjaxRequest()
	{
		$input = new Input;
		$input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');

		$this->assertTrue((new Request($input))->validateIsAjax());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateIsAjaxThrowsExceptionForMissingHeader()
	{
		$error = '';

		try
		{
			(new Request)->validateIsAjax();
		}
		catch (\RuntimeException $e)
		{
			$error = $e->getMessage();
		}

		$this->assertContains('Invalid AJAX request', $error);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateIsAjaxWithTokenReturnsTrueWhenExpected()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->post->set($requestToken, 1);
		Factory::getApplication()->input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');

		$this->assertTrue((new Request)->validateIsAjaxWithToken());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateIsAjaxWithTokenThrowsExceptionForMissingToken()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');

		$error = '';

		try
		{
			(new Request)->validateIsAjaxWithToken();
		}
		catch (\RuntimeException $e)
		{
			$error = $e->getMessage();
		}

		$this->assertContains('invalid token', $error);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateIsAjaxWithTokenThrowsExceptionIfNotAjax()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->post->set($requestToken, 1);

		$error = '';

		try
		{
			(new Request)->validateIsAjaxWithToken();
		}
		catch (\RuntimeException $e)
		{
			$error = $e->getMessage();
		}

		$this->assertContains('Invalid AJAX request', $error);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateTokenThrowsExceptionForMissingToken()
	{
		$error = '';

		try
		{
			(new Request)->validateHasToken();
		}
		catch (\RuntimeException $e)
		{
			$error = $e->getMessage();
		}

		$this->assertContains('invalid token', $error);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateTokenReturnsExpectedValueForPostToken()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->post->set($requestToken, 1);

		$this->assertTrue((new Request)->validateHasToken());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function validateTokenReturnsExpectedValueForGetToken()
	{
		$requestToken = Session::getFormToken();

		Factory::getApplication()->input->get->set($requestToken, 1);

		$this->assertTrue((new Request)->validateHasToken('get'));
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

		Factory::$config      = $this->getMockConfig();
		Factory::$session     = $this->getMockSession(['session.token' => 'my-token']);
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

		parent::tearDown();
	}
}
