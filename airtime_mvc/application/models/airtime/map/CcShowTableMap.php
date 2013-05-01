<?php



/**
 * This class defines the structure of the 'cc_show' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.airtime.map
 */
class CcShowTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcShowTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('cc_show');
		$this->setPhpName('CcShow');
		$this->setClassname('CcShow');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_show_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'DbName', 'VARCHAR', true, 255, '');
		$this->addColumn('URL', 'DbUrl', 'VARCHAR', false, 255, '');
		$this->addColumn('GENRE', 'DbGenre', 'VARCHAR', false, 255, '');
		$this->addColumn('DESCRIPTION', 'DbDescription', 'VARCHAR', false, 512, null);
		$this->addColumn('COLOR', 'DbColor', 'VARCHAR', false, 6, null);
		$this->addColumn('BACKGROUND_COLOR', 'DbBackgroundColor', 'VARCHAR', false, 6, null);
		$this->addColumn('LIVE_STREAM_USING_AIRTIME_AUTH', 'DbLiveStreamUsingAirtimeAuth', 'BOOLEAN', false, null, false);
		$this->addColumn('LIVE_STREAM_USING_CUSTOM_AUTH', 'DbLiveStreamUsingCustomAuth', 'BOOLEAN', false, null, false);
		$this->addColumn('LIVE_STREAM_USER', 'DbLiveStreamUser', 'VARCHAR', false, 255, null);
		$this->addColumn('LIVE_STREAM_PASS', 'DbLiveStreamPass', 'VARCHAR', false, 255, null);
		$this->addColumn('LINKED', 'DbLinked', 'BOOLEAN', true, null, false);
		$this->addColumn('IS_LINKABLE', 'DbIsLinkable', 'BOOLEAN', true, null, true);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::ONE_TO_MANY, array('id' => 'show_id', ), 'CASCADE', null);
    $this->addRelation('CcShowDays', 'CcShowDays', RelationMap::ONE_TO_MANY, array('id' => 'show_id', ), 'CASCADE', null);
    $this->addRelation('CcShowRebroadcast', 'CcShowRebroadcast', RelationMap::ONE_TO_MANY, array('id' => 'show_id', ), 'CASCADE', null);
    $this->addRelation('CcShowHosts', 'CcShowHosts', RelationMap::ONE_TO_MANY, array('id' => 'show_id', ), 'CASCADE', null);
	} // buildRelations()

} // CcShowTableMap
