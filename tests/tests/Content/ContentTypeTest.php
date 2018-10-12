<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content;

defined('_JEXEC') || die;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Content\ContentType;

/**
 * ContentType tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ContentTypeTest extends \TestCaseDatabase
{
	/**
	 * Preloaded entity for tests.
	 *
	 * @var  ContentType
	 */
	private $entity;

	/**
	 * @test
	 *
	 * @return void
	 */
	public function aliasReturnsExpectedValue()
	{
		$this->assertSame('com_content.article', $this->entity->alias());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function contentHistoryOptionsReturnsCorrectValue()
	{
		$entity = new ContentType;
		$entity->bind(
			[
				$entity->primaryKey() => '99',
				'content_history_options' => '{"formFile":"administrator\/components\/com_content\/models\/forms\/article.xml", "hideFields":["asset_id","checked_out","checked_out_time","version"],"ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time", "version", "hits"],"convertToInt":["publish_up", "publish_down", "featured", "ordering"],"displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ]}'
			]
		);

		$this->assertSame(
			'administrator/components/com_content/models/forms/article.xml',
			$entity->contentHistoryOptions()->formFile
		);

		$entity->assign('content_history_options', '');

		$this->assertSame(null, $entity->contentHistoryOptions());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function fieldMappingsReturnsCorrectValue()
	{
		$entity = new ContentType;
		$entity->bind(
			[
				$entity->primaryKey() => '99',
				'field_mappings' => '{"common":{"core_content_item_id":"id","core_title":"name","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"description", "core_hits":"null","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"link", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"null", "asset_id":"null"}, "special":{"imptotal":"imptotal", "impmade":"impmade", "clicks":"clicks", "clickurl":"clickurl", "custombannercode":"custombannercode", "cid":"cid", "purchase_type":"purchase_type", "track_impressions":"track_impressions", "track_clicks":"track_clicks"}}'
			]
		);

		$fieldMappings = $entity->fieldMappings();

		$this->assertSame('id', $fieldMappings->common->core_content_item_id);

		$entity->assign('field_mappings', '');

		$this->assertSame(null, $entity->fieldMappings());
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_content_types', JPATH_TEST_DATABASE . '/jos_content_types.csv');

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function loadWorks()
	{
		$this->assertSame('com_content.article', $this->entity->get('type_alias'));
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

		$this->entity = ContentType::find(1);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function tableSettingsReturnsCorrectValue()
	{
		$entity = new ContentType;
		$entity->bind(
			[
				$entity->primaryKey() => '99',
				'table' => '{"special":{"dbtable":"#__banner_clients","key":"id","type":"Client","prefix":"BannersTable"}}'
			]
		);

		$tableSettings = $entity->tableSettings();

		$this->assertSame('#__banner_clients', $tableSettings->special->dbtable);

		$entity->assign('table', '');

		$this->assertSame(null, $entity->tableSettings());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function titleReturnsExpectedTitle()
	{
		$this->assertSame('Article', $this->entity->title());
	}
}
