<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Router;
use Joomla\Registry\Registry;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Application\SiteApplication;
use Phproberto\Joomla\Entity\Routing\RouteGenerator;

/**
 * Route generator tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class RouteGeneratorTest extends \TestCase
{
	/**
	 * Route generator instance for fast tests.
	 *
	 * @var  RouteGenerator
	 */
	private $generator;

	/**
	 * $_SERVER['HTTP_HOST']
	 *
	 * @var  string
	 */
	private $host = 'phproberto.test.com';

	/**
	 * $_SERVER['SCRIPT_NAME']
	 *
	 * @var  string
	 */
	private $scriptName = '/index.php';

	/**
	 * Sample component option to use in tests.
	 *
	 * @var  string
	 */
	private $option = 'com_content';

	/**
	 * Backup of the $_SERVER variable
	 *
	 * @var    array
	 */
	private $server;

	/**
	 * Token that is added to URLs.
	 *
	 * @var  string
	 */
	private $token = 'cfcd208495d565ef66e7dff9f98764da';

	/**
	 * [generateUrlsDataProvider description]
	 *
	 * @return  array
	 */
	public function generateUrlsDataProvider()
	{
		$currentUrl = 'http://' . $this->host . $this->scriptName;

		return [
			// Itemid has higher priority than itemId
			[
				'expected' => 'index.php?option=com_content&Itemid=23&my-var=12',
				'vars'     => ['my-var' => 12, 'Itemid' => 23, 'itemId' => 22],
				'options'  => ['routed' => false]
			],
			[
				'expected' => 'index.php?option=com_content&Itemid=22&my-var=212',
				'vars'     => ['my-var' => 212, 'itemId' => 22],
				'options'  => ['routed' => false]
			],
			// Options itemId overrides vars Itemid & itemId
			[
				'expected' => 'index.php?option=com_content&Itemid=44',
				'vars'     => ['Itemid' => 23, 'itemId' => 22],
				'options'  => ['routed' => false, 'itemId' => 44]
			],
			// URL with token added
			[
				'expected' => 'index.php?option=com_content&' . $this->token . '=1',
				'vars'     => [],
				'options'  => ['routed' => false, 'addToken' => true]
			],
			// Return to custom url
			[
				'expected' => 'index.php?option=com_content&return=' . base64_encode('index.php?hello=world'),
				'vars'     => [],
				'options'  => ['routed' => false, 'return' => 'index.php?hello=world']
			],
			// Return to current url
			[
				'expected' => 'index.php?option=com_content&return=' . base64_encode($currentUrl),
				'vars'     => [],
				'options'  => ['routed' => false, 'return' => 'current']
			],
			// Routed URL
			[
				'expected' => 'blog/article/1-test',
				'vars'     => ['view' => 'article', 'id' => '1-test'],
				'options'  => ['routed' => true]
			],
			[
				'expected' => 'category/2',
				'vars'     => ['view' => 'category', 'id' => '2'],
				'options'  => ['routed' => true]
			],
		];
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsOption()
	{
		$reflection = new \ReflectionClass($this->generator);
		$optionProperty = $reflection->getProperty('option');
		$optionProperty->setAccessible(true);

		$this->assertSame($this->option, $optionProperty->getValue($this->generator));
	}

	/**
	 * @test
	 *
	 * @param   string  $expected  Expected URL
	 * @param   array   $vars      Vars to add to the URL
	 * @param   array   $options   Options to generate the URL
	 *
	 * @dataProvider  generateUrlsDataProvider
	 *
	 * @return void
	 */
	public function generateUrlReturnsExpectedUrl($expected, $vars, $options)
	{
		$rawUrl = $this->generator->generateUrl($vars, array_merge($options, ['routed' => false]));
		$routed = isset($options['routed']) ? $options['routed'] : true;

		if ($routed)
		{
			$uri = new Uri;
			$uri->setPath($expected);

			$router = $this->getMockBuilder(Router::class)
				->setMethods(array('build'))
				->getMock();

			$router->expects($this->once())
				->method('build')
				->with($rawUrl)
				->willReturn($uri);

			\TestReflection::setValue('JRouter', 'instances', ['site' => $router]);
		}

		$this->assertSame($expected, $this->generator->generateUrl($vars, $options));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function generateUrlProcessesItemId()
	{
		$this->assertSame(
			'index.php?option=com_content&Itemid=23&my-var=12',
			$this->generator->generateUrl(['my-var' => 12, 'Itemid' => 23, 'itemId' => 22], ['routed' => false])
		);
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

		$this->server = $_SERVER;

		$_SERVER['HTTP_HOST'] = $this->host;
		$_SERVER['SCRIPT_NAME'] = $this->scriptName;

		$config = new Registry;
		$config->set('session', false);

		$session = $this->getMockSession();

		$app = new SiteApplication($this->getMockInput(), $config);
		$app->loadSession($session);

		Factory::$application = $app;
		Factory::$session = $session;

		$this->generator = new RouteGenerator($this->option);
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

		$_SERVER = $this->server;
		unset($this->server);

		Uri::reset();

		\TestReflection::setValue('JRouter', 'instances', []);
		\TestReflection::setValue('JRoute', '_router', array());

		parent::tearDown();
	}
}
