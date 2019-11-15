<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Searcher;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Searcher\DatabaseSearcher;
use Phproberto\Joomla\Entity\Tests\Searcher\Stubs\SampleDatabaseSearcher;

/**
 * Database searcher tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class DatabaseSearcherTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsDbFromOptions()
	{
		$db = $this->getMockBuilder(\JDatabaseDriver::class)
			->disableOriginalConstructor()
			->getMock();

		$searcher = new SampleDatabaseSearcher(['db' => $db]);

		$reflection = new \ReflectionClass($searcher);
		$dbProperty = $reflection->getProperty('db');
		$dbProperty->setAccessible(true);

		$this->assertSame($db, $dbProperty->getValue($searcher));

		// Ensure DB does not reach options
		$this->assertFalse($searcher->options()->exists('db'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function defaultOptionsContainsStartAndLimit()
	{
		$searcher = new SampleDatabaseSearcher;

		$defaultOptions = $searcher->defaultOptions();

		$this->assertTrue(array_key_exists('list.start', $defaultOptions));
		$this->assertTrue(array_key_exists('list.limit', $defaultOptions));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchReturnsExpectedOutput()
	{
		$options = [
			'list.start' => 5,
			'list.limit' => 10
		];

		$query = Factory::getDbo()->getQuery(true)
			->select('*')
			->from('sample_table')
			->where('id = 1');

		$db = $this->getMockBuilder(\JDatabaseDriver::class)
			->disableOriginalConstructor()
			->setMethods(['setQuery', 'loadAssocList'])
			->getMockForAbstractClass();

		$db->expects($this->once())
			->method('setQuery')
			->with($query, $options['list.start'], $options['list.limit']);

		$items = [
			['id' => 1, 'name' => 'first item'],
			['id' => 2, 'name' => 'second item']
		];

		$db->expects($this->once())
			->method('loadAssocList')
			->willReturn($items);

		$options['db'] = $db;

		$searcher = new SampleDatabaseSearcher($options);
		$searcher->searchQuery = $query;

		$this->assertSame($items, $searcher->search());

	}
}
