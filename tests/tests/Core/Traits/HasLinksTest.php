<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Router;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\SiteApplication;
use Phproberto\Joomla\Entity\Tests\Mock\ComponentMock;
use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithLinks;

/**
 * HasLinks trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasLinksTest extends \TestCase
{
	/**
	 * Entity instance for tests.
	 *
	 * @var  EntityWithLinks
	 */
	private $entity;

	/**
	 * Backup of the $_SERVER variable
	 *
	 * @var    array
	 */
	private $server;

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function slugReturnsCorrectValue()
	{
		$entity = new EntityWithLinks;

		$this->assertTrue(null === $entity->slug());

		$entity->bind(
			[
				'id' => 999,
				'name' => 'test'
			]
		);

		$this->assertSame('999', $entity->slug());

		$entity->bind(
			[
				'id' => 999,
				'name' => 'test',
				'alias' => 'my-alias'
			]
		);

		$this->assertSame('999-my-alias', $entity->slug());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkCreateReturnsExpectedUrl()
	{
		$expected = 'index.php?option=com_entity_with_links&var=value'
			. '&task=entitywithlinks.add'
			. '&view=entitywithlinks'
			. '&' . DEFAULT_TOKEN_FOR_URLS . '=1';

		$this->assertSame($expected, $this->entity->linkCreate(['var' => 'value'], ['routed' => false]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkDeleteReturnsNullForUnloadedItems()
	{
		$this->assertSame(null, $this->entity->linkDelete(['var' => 'value'], ['routed' => false]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkDeleteReturnsExpectedUrl()
	{
		$this->entity->bind(
			[
				'id' => 99
			]
		);
		$expected = 'index.php?option=com_entity_with_links&var=value'
			. '&task=entitywithlinks.delete'
			. '&id=99'
			. '&view=entitywithlinks'
			. '&' . DEFAULT_TOKEN_FOR_URLS . '=1';

		$this->assertSame($expected, $this->entity->linkDelete(['var' => 'value'], ['routed' => false]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkEditReturnsNullForUnloadeItems()
	{
		$this->assertSame(null, $this->entity->linkEdit(['var' => 'value'], ['routed' => false]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkEditReturnsExpectedUrl()
	{
		$this->entity->bind(
			[
				'id' => 99
			]
		);
		$expected = 'index.php?option=com_entity_with_links&var=value'
			. '&task=entitywithlinks.edit'
			. '&id=99'
			. '&view=entitywithlinks'
			. '&' . DEFAULT_TOKEN_FOR_URLS . '=1';

		$this->assertSame($expected, $this->entity->linkEdit(['var' => 'value'], ['routed' => false]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkReturnsExpectedValue()
	{
		$entity = new EntityWithLinks;

		$this->assertSame('index.php?option=com_entity_with_links&var=value&view=entitywithlinks', $this->entity->link(['var' => 'value'], ['routed' => false]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkTaskPrefixReturnsExpectedValue()
	{
		$this->assertSame('entitywithlinks', $this->entity->linkTaskPrefix());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkViewNameReturnsExpectedValue()
	{
		$this->assertSame('entitywithlinks', $this->entity->linkViewName());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkViewReturnsNullForUnloadedItems()
	{
		$this->assertSame(null, $this->entity->linkView(['var' => 'value'], ['routed' => false]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function linkViewReturnsExpectedUrl()
	{
		$this->entity->bind(
			[
				'id' => 89
			]
		);
		$expected = 'index.php?option=com_entity_with_links&var=value'
			. '&id=89'
			. '&view=entitywithlinks';

		$this->assertSame($expected, $this->entity->linkView(['var' => 'value'], ['routed' => false]));
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

		$this->server = $_SERVER;

		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$config = new Registry;
		$config->set('session', false);

		$session = $this->getMockSession();

		$option = 'com_entity_with_links';
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

		$app->method('getName')
			->willReturn('site');

		Factory::$application = $app;
		Factory::$session = $session;

		$this->entity = new EntityWithLinks;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		$_SERVER = $this->server;
		unset($this->server);

		Uri::reset();

		parent::tearDown();
	}
}
