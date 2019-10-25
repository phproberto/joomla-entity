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
use Phproberto\Joomla\Entity\Extensions\Entity\Component;
use Phproberto\Joomla\Entity\Tests\Mock\ComponentMock;

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
	 * Data provider to test generateUrls() method.
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
				'expected' => 'index.php?option=com_content&' . DEFAULT_TOKEN_FOR_URLS . '=1',
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
	 * @test
	 *
	 * @return void
	 */
	public function getInstanceUsesActiveOption()
	{
		Factory::getApplication()->input->set('option', 'com_phproberto');

		$generator = RouteGenerator::getInstance();

		$reflection = new \ReflectionClass($generator);
		$optionProperty = $reflection->getProperty('option');
		$optionProperty->setAccessible(true);

		$this->assertSame('com_phproberto', $optionProperty->getValue($generator));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function getInstanceReturnsCachedInstance()
	{
		$option = 'com_cached_instances';
		$generator = new RouteGenerator($option);

		$reflection = new \ReflectionClass(RouteGenerator::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instancesProperty->setValue(
			RouteGenerator::class,
			[
				RouteGenerator::class => [
					$option => $generator
				]
			]
		);

		$this->assertSame($generator, RouteGenerator::getInstance($option));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function getInstanceCachesInstance()
	{
		$reflection = new \ReflectionClass(RouteGenerator::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$this->assertEquals([], $instancesProperty->getValue(RouteGenerator::class));

		$generator = RouteGenerator::getInstance('com_phproberto');

		$expected = [
			RouteGenerator::class => ['com_phproberto' => $generator]
		];

		$this->assertEquals($expected, $instancesProperty->getValue(RouteGenerator::class));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function getMenuItemsReturnsCachedItems()
	{
		$items = ['test' => 23];

		$generator = $this->getMockBuilder(RouteGenerator::class)
			->disableOriginalConstructor()
			->setMethods(['loadMenuItems'])
			->getMock();

		$generator->expects($this->never())
			->method('loadMenuItems');

		$reflection = new \ReflectionClass($generator);
		$menuItemsProperty = $reflection->getProperty('menuItems');
		$menuItemsProperty->setAccessible(true);
		$menuItemsProperty->setValue($generator, $items);

		$this->assertSame($items, $generator->getMenuItems());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function getMenuItemsCallsLoadMenuItems()
	{
		$items = ['test' => 23];

		$generator = $this->getMockBuilder(RouteGenerator::class)
			->disableOriginalConstructor()
			->setMethods(['loadMenuItems'])
			->getMock();

		$generator->expects($this->once())
			->method('loadMenuItems')
			->willReturn($items);

		$this->assertSame($items, $generator->getMenuItems());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function LoadMenuItemsReturnsExpectedItems()
	{
		$option = 'com_route_generator';
		$items = [
			23 => (object) [
				'id' => 23,
				'component_id' => 69
			],
			63 => (object) [
				'id' => 63,
				'component_id' => 69
			],
		];

		$component = ComponentMock::instance($this, ['id', 'option']);
		$component->method('id')
			->willReturn(69);

		$component->method('option')
			->willReturn($option);

		ComponentMock::saveToCache($component);
		ComponentMock::saveOptionIdXref($component);

		$menu = $this->getMockBuilder('JMenu')
					->setMethods(['getItems'])
					->setConstructorArgs(array())
					->disableOriginalConstructor()
					->getMock();
		$menu->expects($this->any())
				->method('getItems')
				->with($this->equalTo('component_id'), $this->equalTo(69))
				->willReturn($items);

		$app = $this->getMockBuilder('JApplicationCms')
				->setMethods(\TestMockApplicationCms::getMethods())
				->getMock();

		$app->method('getMenu')
			->willReturn($menu);

		Factory::$application = $app;

		$generator = new RouteGenerator($option);

		$reflection = new \ReflectionClass($generator);
		$method = $reflection->getMethod('loadMenuItems');
		$method->setAccessible(true);

		$this->assertEquals($items, $method->invoke($generator));
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
		RouteGenerator::clearInstances();

		\TestReflection::setValue('JRouter', 'instances', []);
		\TestReflection::setValue('JRoute', '_router', array());

		parent::tearDown();
	}
}
