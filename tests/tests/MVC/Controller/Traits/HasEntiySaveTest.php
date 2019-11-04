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
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;
use Phproberto\Joomla\Entity\Tests\Acl\Stubs\EntityWithAcl;
use Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs\EntityWithOwner;
use Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs\ControllerWithEntityCRUD;

defined('JPATH_COMPONENT') || define('JPATH_COMPONENT', JPATH_BASE . '/components/com_content');

/**
 * HasAjaxDelete trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasEntitySaveTest extends \TestCaseDatabase
{
	private $userStateContext = 'com_phproberto.edit.my-context.data';

	/**
	 * @test
	 *
	 * @return void
	 */
	public function saveTrhowsExceptionForMissingToken()
	{
		$controller = new ControllerWithEntityCRUD;
		$error = '';

		try
		{
			$controller->save();
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
	public function saveAssignsOwnerForOwnerableEntities()
	{
		$this->setupRequestWithToken();

		$requestData = [
			'name' => 'Test entity'
		];

		$returnError = 'index.php?returnError=error';
		$returnOk = 'index.php?return=ok';

		$activeUser = new User(53);
		$reflection = new \ReflectionClass(User::class);
		$activeProperty = $reflection->getProperty('active');
		$activeProperty->setAccessible(true);
		$activeProperty->setValue(User::class, $activeUser);

		$entity = $this->getMockBuilder(EntityWithOwner::class)
			->setMethods(['bind', 'columnAlias', 'assign', 'owner', 'isOwner', 'hasOwner', 'primaryKey', 'save'])
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
			->setMethods(['allowAdd', 'entityInstance', 'saveDataFromRequest', 'saveReturnError', 'saveReturnOk'])
			->getMock();

		$controller->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->once())
			->method('allowAdd')
			->willReturn(true);

		$controller->expects($this->once())
			->method('saveDataFromRequest')
			->willReturn($requestData);

		$controller->expects($this->once())
			->method('saveReturnError')
			->willReturn($returnError);

		$controller->expects($this->once())
			->method('saveReturnOk')
			->willReturn($returnOk);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		$app = Factory::getApplication();
		$app->expects($this->once())
			->method('setUserState')
			->with($this->equalTo($this->userStateContext), $this->equalTo(null));

		$this->assertTrue($controller->save());

		$reflection = new \ReflectionClass($controller);
		$messageProperty = $reflection->getProperty('message');
		$messageProperty->setAccessible(true);
		$redirectProperty = $reflection->getProperty('redirect');
		$redirectProperty->setAccessible(true);

		$this->assertSame('JLIB_APPLICATION_SAVE_SUCCESS', $messageProperty->getValue($controller));
		$this->assertSame($returnOk, $redirectProperty->getValue($controller));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function saveDataFromRequestReturnsModelFormControl()
	{
		$formControl = 'jform';
		$requestData = [
			'id'   => 12,
			'name' => 'Test entity'
		];

		Factory::getApplication()->input->post->set($formControl, $requestData);

		$controller = new ControllerWithEntityCRUD;

		$this->assertSame($requestData, $controller->saveDataFromRequest());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function saveReturnsCorrectValueBasedOnCanCreate()
	{
		$this->setupRequestWithToken();

		$requestData = [
			'name' => 'Test entity'
		];

		$returnError = 'index.php?returnError=error';
		$returnOk = 'index.php?return=ok';

		$acl = $this->getMockBuilder(Acl::class)
			->setMethods(['canCreate'])
			->getMock();

		$acl->expects($this->exactly(2))
			->method('canCreate')
			->will($this->onConsecutiveCalls(true, false));

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(['bind', 'acl', 'aclPrefix', 'aclAssetName', 'primaryKey', 'save'])
			->getMock();

		$entity->method('primaryKey')
			->willReturn('id');

		$entity->expects($this->exactly(4))
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->exactly(2))
			->method('acl')
			->willReturn($acl);

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['entityInstance', 'saveDataFromRequest', 'saveReturnError', 'saveReturnOk'])
			->getMock();

		$controller->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->exactly(2))
			->method('saveDataFromRequest')
			->willReturn($requestData);

		$controller->expects($this->exactly(2))
			->method('saveReturnError')
			->willReturn($returnError);

		$controller->expects($this->once())
			->method('saveReturnOk')
			->willReturn($returnOk);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		$app = Factory::getApplication();
		$app->expects($this->at(0))
			->method('setUserState')
			->with($this->equalTo($this->userStateContext), $this->equalTo(null));

		$app->expects($this->at(3))
			->method('setUserState')
			->with($this->equalTo($this->userStateContext), $this->equalTo($requestData));

		$reflection = new \ReflectionClass($controller);
		$messageProperty = $reflection->getProperty('message');
		$messageProperty->setAccessible(true);
		$redirectProperty = $reflection->getProperty('redirect');
		$redirectProperty->setAccessible(true);

		// $acl->canEdit() returns true
		$this->assertTrue($controller->save());

		$this->assertSame('JLIB_APPLICATION_SAVE_SUCCESS', $messageProperty->getValue($controller));
		$this->assertSame($returnOk, $redirectProperty->getValue($controller));

		// $acl->canEdit() returns false
		$this->assertFalse($controller->save());

		$this->assertSame('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED', $messageProperty->getValue($controller));
		$this->assertSame($returnError, $redirectProperty->getValue($controller));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function saveReturnsCorrectValueBasedOnCanEdit()
	{
		$this->setupRequestWithToken();

		$requestData = [
			'id' => 12,
			'name' => 'Test entity'
		];

		$returnError = 'index.php?returnError=error';
		$returnOk = 'index.php?return=ok';

		$acl = $this->getMockBuilder(Acl::class)
			->setMethods(['canEdit'])
			->getMock();

		$acl->expects($this->exactly(2))
			->method('canEdit')
			->will($this->onConsecutiveCalls(true, false));

		$entity = $this->getMockBuilder(EntityWithAcl::class)
			->setMethods(['bind', 'acl', 'aclPrefix', 'aclAssetName', 'primaryKey', 'save'])
			->getMock();

		$entity->method('primaryKey')
			->willReturn('id');

		$entity->expects($this->exactly(4))
			->method('bind')
			->with($this->equalTo($requestData));

		$entity->expects($this->exactly(2))
			->method('acl')
			->willReturn($acl);

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['entityInstance', 'saveDataFromRequest', 'saveReturnError', 'saveReturnOk'])
			->getMock();

		$controller->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->exactly(2))
			->method('saveDataFromRequest')
			->willReturn($requestData);

		$controller->expects($this->exactly(2))
			->method('saveReturnError')
			->willReturn($returnError);

		$controller->expects($this->once())
			->method('saveReturnOk')
			->willReturn($returnOk);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		$app = Factory::getApplication();
		$app->expects($this->at(0))
			->method('setUserState')
			->with($this->equalTo($this->userStateContext), $this->equalTo(null));
		$app->expects($this->at(1))
			->method('setUserState')
			->with($this->equalTo($this->userStateContext), $this->equalTo($requestData));

		$reflection = new \ReflectionClass($controller);
		$messageProperty = $reflection->getProperty('message');
		$messageProperty->setAccessible(true);
		$redirectProperty = $reflection->getProperty('redirect');
		$redirectProperty->setAccessible(true);

		// $acl->canEdit() returns true
		$this->assertTrue($controller->save());

		$this->assertSame('JLIB_APPLICATION_SAVE_SUCCESS', $messageProperty->getValue($controller));
		$this->assertSame($returnOk, $redirectProperty->getValue($controller));

		// $acl->canEdit() returns false
		$this->assertFalse($controller->save());

		$this->assertSame('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED', $messageProperty->getValue($controller));
		$this->assertSame($returnError, $redirectProperty->getValue($controller));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entitySaveReturnsFalseOnSaveException()
	{
		$this->setupRequestWithToken();

		$requestData = [
			'name' => 'Test entity'
		];

		$saveError = 'My error message';

		$entity = $this->getMockBuilder(EntityInterface::class)
			->setMethods(
				[
					'all', 'assign', 'columnAlias', 'bind', 'get',
					'hasId', 'id', 'name', 'primaryKey', 'save'
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
			->method('save')
			->willThrowException(new \Exception($saveError));

		$controller = $this->getMockBuilder(ControllerWithEntityCRUD::class)
			->setMethods(['allowAdd', 'entityInstance', 'saveDataFromRequest'])
			->getMock();

		$controller->method('entityInstance')
			->willReturn($entity);

		$controller->expects($this->once())
			->method('allowAdd')
			->willReturn(true);

		$controller->expects($this->once())
			->method('saveDataFromRequest')
			->willReturn($requestData);

		$controller->option = 'com_phproberto';
		$controller->context = 'my-context';
		$controller->{'text_prefix'} = 'LIB_JOOMLA_ENTITY_';

		$app = Factory::getApplication();
		$app->expects($this->once())
			->method('setUserState')
			->with($this->equalTo($this->userStateContext), $this->equalTo($requestData));

		$this->assertFalse($controller->save());

		$reflection = new \ReflectionClass($controller);
		$messageProperty = $reflection->getProperty('message');
		$messageProperty->setAccessible(true);

		$this->assertSame($saveError, $messageProperty->getValue($controller));
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
