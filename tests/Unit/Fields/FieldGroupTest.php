<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Fields;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Fields\FieldGroup;

/**
 * Field group entity tests.
 *
 * @since   1.2.0
 */
class FieldGroupTest extends \TestCaseDatabase
{
	/**
	 * Preloaded field group for tests.
	 *
	 * @var  FieldGroup
	 */
	protected $fieldGroup;

	/**
	 * @test
	 *
	 * @return void
	 */
	public function fieldsReturnsFields()
	{
		$fields = $this->fieldGroup->fields();

		$this->assertInstanceOf(Collection::class, $fields);
		$this->assertNotSame(0, count($fields));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function fieldsReturnsEmptyCollectionForNonLoadedGroup()
	{
		$fieldGroup = new FieldGroup;
		$fields = $fieldGroup->fields();

		$this->assertInstanceOf(Collection::class, $fields);
		$this->assertSame(0, count($fields));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadWorks()
	{
		$data = $this->fieldGroup->all();

		$this->assertTrue(is_array($data));
		$this->assertNotSame(0, count($data));
	}
	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_extensions', JPATH_TESTS_PHPROBERTO . '/db/data/extensions.csv');
		$dataSet->addTable('jos_fields', JPATH_TESTS_PHPROBERTO . '/db/data/fields.csv');
		$dataSet->addTable('jos_fields_groups', JPATH_TESTS_PHPROBERTO . '/db/data/fields_groups.csv');

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

		$this->fieldGroup = FieldGroup::find(1);
	}

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$files = [
			JPATH_TESTS_PHPROBERTO . '/db/schema/fields.sql',
			JPATH_TESTS_PHPROBERTO . '/db/schema/fields_groups.sql'
		];

		foreach ($files as $file)
		{
			static::$driver->getConnection()->exec(file_get_contents($file));
		}

		Factory::$database = static::$driver;
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function tableReturnsSpecificTableInstance()
	{
		$this->assertInstanceOf('JTableUser', $this->fieldGroup->table('User', 'JTable'));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		FieldGroup::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
