<?php



/**
 * This class defines the structure of the 'cc_show_instances' table.
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
class CcShowInstancesTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcShowInstancesTableMap';

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
		$this->setName('cc_show_instances');
		$this->setPhpName('CcShowInstances');
		$this->setClassname('CcShowInstances');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_show_instances_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('STARTS', 'DbStarts', 'TIMESTAMP', true, null, null);
		$this->addColumn('ENDS', 'DbEnds', 'TIMESTAMP', true, null, null);
		$this->addForeignKey('SHOW_ID', 'DbShowId', 'INTEGER', 'cc_show', 'ID', true, null, null);
		$this->addColumn('RECORD', 'DbRecord', 'TINYINT', false, null, 0);
		$this->addColumn('REBROADCAST', 'DbRebroadcast', 'TINYINT', false, null, 0);
		$this->addForeignKey('INSTANCE_ID', 'DbOriginalShow', 'INTEGER', 'cc_show_instances', 'ID', false, null, null);
		$this->addForeignKey('FILE_ID', 'DbRecordedFile', 'INTEGER', 'cc_files', 'ID', false, null, null);
		$this->addColumn('TIME_FILLED', 'DbTimeFilled', 'VARCHAR', false, null, '00:00:00');
		$this->addColumn('CREATED', 'DbCreated', 'TIMESTAMP', true, null, null);
		$this->addColumn('LAST_SCHEDULED', 'DbLastScheduled', 'TIMESTAMP', false, null, null);
		$this->addColumn('MODIFIED_INSTANCE', 'DbModifiedInstance', 'BOOLEAN', true, null, false);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcShow', 'CcShow', RelationMap::MANY_TO_ONE, array('show_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcShowInstancesRelatedByDbOriginalShow', 'CcShowInstances', RelationMap::MANY_TO_ONE, array('instance_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcFiles', 'CcFiles', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcShowInstancesRelatedByDbId', 'CcShowInstances', RelationMap::ONE_TO_MANY, array('id' => 'instance_id', ), 'CASCADE', null);
    $this->addRelation('CcSchedule', 'CcSchedule', RelationMap::ONE_TO_MANY, array('id' => 'instance_id', ), 'CASCADE', null);
    $this->addRelation('CcPlayoutHistory', 'CcPlayoutHistory', RelationMap::ONE_TO_MANY, array('id' => 'instance_id', ), 'SET NULL', null);
	} // buildRelations()

} // CcShowInstancesTableMap
