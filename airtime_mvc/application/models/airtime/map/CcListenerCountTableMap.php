<?php



/**
 * This class defines the structure of the 'cc_listener_count' table.
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
class CcListenerCountTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcListenerCountTableMap';

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
		$this->setName('cc_listener_count');
		$this->setPhpName('CcListenerCount');
		$this->setClassname('CcListenerCount');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_listener_count_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addForeignKey('TIMESTAMP_ID', 'DbTimestampId', 'INTEGER', 'cc_timestamp', 'ID', true, null, null);
		$this->addForeignKey('MOUNT_NAME_ID', 'DbMountNameId', 'INTEGER', 'cc_mount_name', 'ID', true, null, null);
		$this->addColumn('LISTENER_COUNT', 'DbListenerCount', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcTimestamp', 'CcTimestamp', RelationMap::MANY_TO_ONE, array('timestamp_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcMountName', 'CcMountName', RelationMap::MANY_TO_ONE, array('mount_name_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcListenerCountTableMap
