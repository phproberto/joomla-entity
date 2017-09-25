<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Extension;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Acl\Acl;
use Phproberto\Joomla\Entity\Core\Extension\Component;

/**
 * Component entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ComponentTest extends \TestCaseDatabase
{
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

		\JFactory::$session     = $this->getMockSession();
		\JFactory::$config      = $this->getMockConfig();
		\JFactory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		Component::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');

		return $dataSet;
	}

	/**
	 * aclAssetName returns option.
	 *
	 * @return  void
	 */
	public function testAclAssetNameReturnsOption()
	{
		$component = $this->getMockBuilder(Component::class)
			->setMethods(array('option'))
			->getMock();

		$component->expects($this->once())
			->method('option')
			->willReturn('com_phproberto');

		$this->assertSame('com_phproberto', $component->aclAssetName());
	}

	/**
	 * Acl can be retrieved.
	 *
	 * @return  void
	 */
	public function testAclCanBeRetrieved()
	{
		$entity = new Component(666);
		$user = new User(999);

		$acl = $entity->acl($user);

		$reflection = new \ReflectionClass($acl);
		$entityProperty = $reflection->getProperty('entity');
		$entityProperty->setAccessible(true);
		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);

		$this->assertInstanceOf(Acl::class, $acl);
		$this->assertSame($user, $userProperty->getValue($acl));
		$this->assertSame($entity, $entityProperty->getValue($acl));
	}

	/**
	 * active returns correct instance.
	 *
	 * @return  void
	 */
	public function testActiveReturnsCorrectInstance()
	{
		\JFactory::getApplication()->input->set('option', 'com_content');

		$this->assertInstanceOf(Component::class, Component::active());

		\JFactory::getApplication()->input->set('option', 'com_phproberto');

		$this->assertSame('com_phproberto', Component::active()->option());

		\JFactory::getApplication()->input->set('option', null);
	}

	/**
	 * fromOption loads cached id.
	 *
	 * @return  void
	 */
	public function testFromOptionCachesId()
	{
		$component = new Component;

		$reflection = new \ReflectionClass($component);
		$optionIdXrefProperty = $reflection->getProperty('optionIdXref');
		$optionIdXrefProperty->setAccessible(true);

		$this->assertSame(array(), $optionIdXrefProperty->getValue($component));

		$component = Component::fromOption('com_content');

		$this->assertSame(array('com_content' => 22), $optionIdXrefProperty->getValue($component));
	}

	/**
	 * fromOption throws exception for wrong option.
	 *
	 * @return  void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testFromOptionThrowsExceptionForWrongOption()
	{
		$component = Component::fromOption(' ');
	}

	/**
	 * fromOption throws exception when component not found.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testFromOptionThrowsExceptionWhenComponentNotFound()
	{
		$component = Component::fromOption('com_phproberto');
	}

	/**
	 * model returns admin model.
	 *
	 * @return  void
	 */
	public function testModelReturnsAdministratorModel()
	{
		$component = Component::fromOption('com_admin');

		$this->assertEquals('AdminModelProfile', get_class($component->model('Profile')));
	}

	/**
	 * model returns site model.
	 *
	 * @return  void
	 */
	public function testModelReturnsSiteModel()
	{
		$component = Component::fromOption('com_content')->site();

		$this->assertInstanceOf('ContentModelArticles', $component->model('Articles'));
	}

	/**
	 * Test model returns a backend model when backend app is active.
	 *
	 * @return  void
	 */
	public function testModelReturnsBackendModelWhenBackendAppIsActive()
	{
		$component = Component::fromOption('com_admin');

		$this->assertEquals('AdminModelProfile', get_class($component->model('Profile')));
	}

	/**
	 * model throws exception when model not found.
	 *
	 * @return  void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testModelThrowsExceptionWhenModelNotFound()
	{
		$component = Component::fromOption('com_admin');

		$component->model('UnexistingModel');
	}

	/**
	 * modelsFolder returns correct value.
	 *
	 * @return  void
	 */
	public function testModelsFolderReturnsCorrectValue()
	{
		$component = Component::fromOption('com_admin');

		$this->assertSame(JPATH_SITE . '/administrator/components/com_admin/models', $component->modelsFolder());

		$component = Component::fromOption('com_config')->site();

		$this->assertSame(JPATH_SITE . '/components/com_config/model', $component->modelsFolder());
	}

	/**
	 * modelsFolder throws exception when folder not found.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testModelsFolderThrowsExceptionWhenFolderNotFound()
	{
		$component = Component::fromOption('com_cpanel');

		$component->modelsFolder();
	}

	/**
	 * option returns correct string.
	 *
	 * @return  void
	 */
	public function testOptionReturnsCorrectString()
	{
		$component = new Component(999);

		$reflection = new \ReflectionClass($component);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($component, array('extension_id' => 999, 'element' => 'com_phproberto'));

		$this->assertSame('com_phproberto', $component->option());

		$component = new Component(999);
		$rowProperty->setValue($component, array('extension_id' => 999, 'element' => 'com_contact'));

		$this->assertSame('com_contact', $component->option());
	}

	/**
	 * prefix returns correct value.
	 *
	 * @return  void
	 */
	public function testPrefixReturnsCorrectValue()
	{
		$component = Component::fromOption('com_cpanel');

		$this->assertSame('Cpanel', $component->prefix());

		$component = Component::fromOption('com_categories');

		$this->assertSame('Categories', $component->prefix());
	}

	/**
	 * table returns correct table.
	 *
	 * @return  void
	 */
	public function testTableReturnsCorrectTable()
	{
		$component = Component::fromOption('com_cpanel');

		$this->assertInstanceOf('JTableExtension', $component->table());

		$component = Component::fromOption('com_categories');

		require_once JPATH_ADMINISTRATOR . '/components/com_categories/tables/category.php';

		$this->assertInstanceOf('CategoriesTableCategory', $component->table('Category'));
		$this->assertInstanceOf('JTableContent', $component->table('Content', 'JTable'));
	}

	/**
	 * tablesFolder returns correct value.
	 *
	 * @return  void
	 */
	public function testTablesFolderReturnsCorrectValue()
	{
		$component = Component::fromOption('com_categories');

		$this->assertSame(JPATH_ADMINISTRATOR . '/components/com_categories/tables', $component->tablesFolder());
	}

	/**
	 * tablesFolder throws exception when folder does not exist.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testTablesFolderThrowsExceptionWhenFolderDoesNotExist()
	{
		$component = Component::fromOption('com_cpanel');

		$component->tablesFolder();
	}
}
