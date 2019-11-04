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
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Content\Entity\Article;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\ControllerWithEntityCRUD;

defined('JPATH_COMPONENT') || define('JPATH_COMPONENT', JPATH_BASE . '/components/com_content');

/**
 * HasAjaxDelete trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasEntityCreateTest extends \TestCaseDatabase
{
	private $userStateContext = 'com_phproberto.entityCreate.my-context.data';

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxEntityCreateReturnsErrorForMissingAjaxHeader()
	{
		$this->setupRequestWithToken();

		$controller = new ControllerWithEntityCRUD;

		ob_start();

		$controller->ajaxEntityCreate();

		$output = ob_get_clean();

		$this->assertSame('Invalid AJAX request', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxEntityCreateReturnsErrorForMissingToken()
	{
		$this->setupRequestWithAjaxHeader();

		$controller = new ControllerWithEntityCRUD;

		ob_start();

		$controller->ajaxEntityCreate();

		$output = ob_get_clean();

		$this->assertSame('None or invalid token received in method `post`', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxEntityCreateAssignsOwnerForOwnerableEntities()
	{
		$this->setupRequestWithTokenAndAjaxHeader();

		$requestData = [
			'name' => 'Test entity'
		];

		$postSaveData = array_merge(
			['id' => 23],
			$requestData
		);

		$activeUser = new User(53);
		$reflection = new \ReflectionClass(User::class);
		$activeProperty = $reflection->getProperty('active');
		$activeProperty->setAccessible(true);
		$activeProperty->setValue(User::class, $activeUser);

		$entity = $this->getMockBuilder(Ownerable::class)
			->setMethods(
				[
					'all', 'assign', 'bind', 'columnAlias', 'hasOwner', 'isOwner',
					'owner', 'primaryKey', 'save'
				]
			)
			->getMock();

		$entity->expects($this->once())
			->method('all')
			->willReturn($postSaveData);

		$entity->expects($this->once())
			->method('primaryKey')
			->willReturn('id');

		$entity->expects($this->once())
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->once())
			->method('columnAlias')
			->with($this->equalTo(UsersColumn::OWNER))
			->willReturn(UsersColumn::OWNER);

		$entity->expects($this->once())
			->method('assign')
			->with($this->equalTo(UsersColumn::OWNER), $this->equalTo($activeUser->id()));

		$entity->expects($this->once())
			->method('save');

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['entityInstance', 'entityCreateDataFromRequest'])
			->getMock();

		$controller->expects($this->once())
			->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->once())
			->method('entityCreateDataFromRequest')
			->willReturn($requestData);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		ob_start();

		$controller->ajaxEntityCreate();

		$output = ob_get_clean();

		$this->assertSame(json_encode($postSaveData), $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityCreateDataFromRequestReturnsModelFormControl()
	{
		$requestData = [
			'id'   => 12,
			'name' => 'Test entity'
		];

		Factory::getApplication()->input->post->set('entity', $requestData);

		$controller = new ControllerWithEntityCRUD;

		$this->assertSame($requestData, $controller->entityCreateDataFromRequest());
	}

	/**
	 * Setup AJAX header on the active request.
	 *
	 * @return  void
	 */
	private function setupRequestWithAjaxHeader()
	{
		Factory::getApplication()->input->server->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');
	}

	/**
	 * Setups current request to have a valid token.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 */
	private function setupRequestWithToken(string $method = 'post')
	{
		Factory::getApplication()->input->{$method}->set(Session::getFormToken(), 1);
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
		$this->setupRequestWithToken($method);
		$this->setupRequestWithAjaxHeader();
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
		User::clearActive();

		parent::tearDown();
	}
}
