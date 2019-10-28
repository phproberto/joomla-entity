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
			->setMethods(['entityInstance', 'entitySaveDataFromRequest'])
			->getMock();

		$controller->expects($this->once())
			->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->once())
			->method('entitySaveDataFromRequest')
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
	public function entityCreateTrhowsExceptionForMissingToken()
	{
		$controller = new ControllerWithEntityCRUD;
		$error = '';

		try
		{
			$controller->entityCreate();
		}
		catch (\Exception $e)
		{
			$error = $e->getMessage();
		}

		$this->assertTrue(substr_count($error, 'None or invalid token received') > 0);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityCreateAssignsOwnerForOwnerableEntities()
	{
		$this->setupRequestWithToken();

		$requestData = [
			'name' => 'Test entity'
		];

		$activeUser = new User(53);
		$reflection = new \ReflectionClass(User::class);
		$activeProperty = $reflection->getProperty('active');
		$activeProperty->setAccessible(true);
		$activeProperty->setValue(User::class, $activeUser);

		$entity = $this->getMockBuilder(Ownerable::class)
			->setMethods(
				[
					'assign', 'bind', 'columnAlias', 'hasOwner', 'isOwner', 'owner', 'primaryKey', 'save'
				]
			)
			->getMock();

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

		$app = Factory::getApplication();
		$app->expects($this->once())
			->method('setUserState')
			->with($this->equalTo($this->userStateContext), $this->equalTo(null));

		$this->assertTrue($controller->entityCreate());

		$reflection = new \ReflectionClass($controller);
		$messageProperty = $reflection->getProperty('message');
		$messageProperty->setAccessible(true);

		$this->assertSame('JLIB_APPLICATION_SAVE_SUCCESS', $messageProperty->getValue($controller));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityCreateDataFromRequestReturnsModelFormControl()
	{
		$formControl = 'jform';
		$requestData = [
			'id'   => 12,
			'name' => 'Test entity'
		];

		Factory::getApplication()->input->post->set($formControl, $requestData);

		$model = $this->getMockBuilder('Model')
			->setMethods(['formControl'])
			->getMock();

		$model->expects($this->once())
			->method('formControl')
			->willReturn($formControl);

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['getModel'])
			->getMock();

		$controller->expects($this->once())
			->method('getModel')
			->willReturn($model);

		$this->assertSame($requestData, $controller->entityCreateDataFromRequest());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityCreateReturnsFalseOnSaveException()
	{
		$this->setupRequestWithToken();

		$requestData = [
			'name' => 'Test entity'
		];

		$saveError = 'My error message';

		$entity = $this->getMockBuilder(EntityInterface::class)
			->setMethods(['bind', 'primaryKey', 'save'])
			->getMock();

		$entity->expects($this->once())
			->method('primaryKey')
			->willReturn('id');

		$entity->expects($this->once())
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->once())
			->method('save')
			->willThrowException(new \Exception($saveError));

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

		$app = Factory::getApplication();
		$app->expects($this->once())
			->method('setUserState')
			->with($this->equalTo($this->userStateContext), $this->equalTo($requestData));

		$this->assertFalse($controller->entityCreate());

		$reflection = new \ReflectionClass($controller);
		$messageProperty = $reflection->getProperty('message');
		$messageProperty->setAccessible(true);

		$this->assertSame($saveError, $messageProperty->getValue($controller));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityCreateReturnsCorrectValueBasedOnCanCreate()
	{
		$this->setupRequestWithToken();

		$requestData = [
			'name' => 'Test entity'
		];

		$acl = $this->getMockBuilder('Acl')
			->setMethods(['canCreate'])
			->getMock();

		$acl->expects($this->exactly(2))
			->method('canCreate')
			->will($this->onConsecutiveCalls(true, false));

		$entity = $this->getMockBuilder(Aclable::class)
			->setMethods(['bind', 'acl', 'aclPrefix', 'aclAssetName', 'primaryKey', 'save'])
			->getMock();

		$entity->expects($this->exactly(2))
			->method('primaryKey')
			->willReturn('id');

		$entity->expects($this->exactly(2))
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->exactly(2))
			->method('acl')
			->willReturn($acl);

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['entityInstance', 'entityCreateDataFromRequest'])
			->getMock();

		$controller->expects($this->exactly(2))
			->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->exactly(2))
			->method('entityCreateDataFromRequest')
			->willReturn($requestData);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		$app = Factory::getApplication();
		$app->expects($this->at(0))
			->method('setUserState')
			->withConsecutive(
				[$this->equalTo($this->userStateContext), $this->equalTo(null)],
				[$this->equalTo($this->userStateContext), $this->equalTo($requestData)]
			);

		// $acl->canCreate() returns true
		$this->assertTrue($controller->entityCreate());

		// $acl->canCreate() returns false
		$this->assertFalse($controller->entityCreate());

		$reflection = new \ReflectionClass($controller);
		$messageProperty = $reflection->getProperty('message');
		$messageProperty->setAccessible(true);

		$this->assertSame('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED', $messageProperty->getValue($controller));
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
