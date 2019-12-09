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
class HasEntityUpdateTest extends \TestCaseDatabase
{
	private $userStateContext = 'com_phproberto.entityUpdate.my-context.data';

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxEntityUpdateReturnsCorrectValueBasedOnCanEdit()
	{
		$this->setupRequestWithTokenAndAjaxHeader();

		$requestData = [
			'id'   => 12,
			'name' => 'Test entity'
		];

		$acl = $this->getMockBuilder(Acl::class)
			->setMethods(['canEdit'])
			->getMock();

		$acl->expects($this->exactly(2))
			->method('canEdit')
			->will($this->onConsecutiveCalls(true, false));

		$entity = $this->getMockBuilder(Aclable::class)
			->setMethods(['acl', 'aclPrefix', 'aclAssetName', 'all', 'bind', 'primaryKey', 'save'])
			->getMock();

		$entity->expects($this->exactly(2))
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->once())
			->method('all')
			->willReturn($requestData);

		$entity->expects($this->exactly(2))
			->method('acl')
			->willReturn($acl);

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['entityInstance', 'entityUpdateDataFromRequest'])
			->getMock();

		$controller->expects($this->exactly(2))
			->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->exactly(2))
			->method('entityUpdateDataFromRequest')
			->willReturn($requestData);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		ob_start();

		$controller->ajaxEntityUpdate();

		$output = ob_get_clean();

		$this->assertSame(json_encode($requestData), $output);

		ob_start();

		$controller->ajaxEntityUpdate();

		$response = json_decode(ob_get_clean());

		$this->assertSame(403, $response->error->code);
		$this->assertSame('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN', $response->error->message);
	}


	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxEntityUpdateReturnsErrorForMissingAjaxHeader()
	{
		$this->setupRequestWithToken();

		$controller = new ControllerWithEntityCRUD;

		ob_start();

		$controller->ajaxentityUpdate();

		$output = ob_get_clean();

		$this->assertSame('Invalid AJAX request', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxentityUpdateReturnsErrorForMissingToken()
	{
		$this->setupRequestWithAjaxHeader();

		$controller = new ControllerWithEntityCRUD;

		ob_start();

		$controller->ajaxentityUpdate();

		$output = ob_get_clean();

		$this->assertSame('None or invalid token received in method `post`', $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxentityUpdateAssignsOwnerForOwnerableEntities()
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
					'all', 'bind', 'hasOwner', 'isOwner',
					'owner', 'save'
				]
			)
			->getMock();

		$entity->expects($this->once())
			->method('all')
			->willReturn($postSaveData);

		$entity->expects($this->once())
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->once())
			->method('save');

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['entityInstance', 'entityUpdateDataFromRequest'])
			->getMock();

		$controller->expects($this->once())
			->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->once())
			->method('entityUpdateDataFromRequest')
			->willReturn($requestData);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		ob_start();

		$controller->ajaxentityUpdate();

		$output = ob_get_clean();

		$this->assertSame(json_encode($postSaveData), $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ajaxentityUpdateTrhowsExceptionForMissingToken()
	{
		$this->setupRequestWithAjaxHeader();

		$controller = new ControllerWithEntityCRUD;

		ob_start();

		$controller->ajaxentityUpdate();

		$output = ob_get_clean();

		$this->assertTrue(substr_count($output, 'None or invalid token received') > 0);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityUpdateAssignsOwnerForOwnerableEntities()
	{
		$this->setupRequestWithTokenAndAjaxHeader();

		$requestData = [
			'name' => 'Test entity'
		];

		$postSaveData = array_merge(
			['id' => 23],
			$requestData
		);

		$returnError = 'index.php?returnError=error';
		$returnOk = 'index.php?return=ok';

		$activeUser = new User(53);
		$reflection = new \ReflectionClass(User::class);
		$activeProperty = $reflection->getProperty('active');
		$activeProperty->setAccessible(true);
		$activeProperty->setValue(User::class, $activeUser);

		$entity = $this->getMockBuilder(Ownerable::class)
			->setMethods(['all', 'bind', 'owner', 'isOwner', 'hasOwner', 'save'])
			->getMock();

		$entity->expects($this->once())
			->method('all')
			->willReturn($postSaveData);

		$entity->expects($this->once())
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->once())
			->method('save');

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['entityInstance', 'entityUpdateDataFromRequest'])
			->getMock();

		$controller->expects($this->once())
			->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->once())
			->method('entityUpdateDataFromRequest')
			->willReturn($requestData);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		ob_start();

		$controller->ajaxentityUpdate();

		$output = ob_get_clean();

		$this->assertSame(json_encode($postSaveData), $output);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityUpdateDataFromRequestReturnsModelFormControl()
	{
		$requestData = [
			'id'   => 12,
			'name' => 'Test entity'
		];

		Factory::getApplication()->input->post->set('entity', $requestData);

		$controller = new ControllerWithEntityCRUD;

		$this->assertSame($requestData, $controller->entityUpdateDataFromRequest());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityUpdateReturnsFalseOnSaveException()
	{
		$this->setupRequestWithTokenAndAjaxHeader();

		$requestData = [
			'name' => 'Test entity'
		];

		$saveError = [
			'error' => [
				'code' => 500,
				'message' => 'My error message'
			]
		];

		$entity = $this->getMockBuilder(EntityInterface::class)
			->setMethods(['bind', 'save'])
			->getMock();

		$entity->expects($this->once())
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->once())
			->method('save')
			->willThrowException(new \Exception($saveError['error']['message']));

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['entityInstance', 'entityUpdateDataFromRequest'])
			->getMock();

		$controller->expects($this->once())
			->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->once())
			->method('entityUpdateDataFromRequest')
			->willReturn($requestData);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		ob_start();

		$controller->ajaxentityUpdate();

		$output = ob_get_clean();

		$this->assertSame(json_encode($saveError), $output);
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
