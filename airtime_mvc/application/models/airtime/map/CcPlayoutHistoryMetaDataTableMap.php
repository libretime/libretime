<?php



/**
 * This class defines the structure of the 'cc_playout_history_metadata' table.
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
class CcPlayoutHistoryMetaDataTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcPlayoutHistoryMetaDataTableMap';

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
		$this->setName('cc_playout_history_metadata');
		$this->setPhpName('CcPlayoutHistoryMetaData');
		$this->setClassname('CcPlayoutHistoryMetaData');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_playout_history_metadata_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addForeignKey('HISTORY_ID', 'DbHistoryId', 'INTEGER', 'cc_playout_history', 'ID', true, null, null);
		$this->addColumn('KEY', 'DbKey', 'VARCHAR', true, 128, null);
		$this->addColumn('VALUE', 'DbValue', 'VARCHAR', true, 128, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcPlayoutHistory', 'CcPlayoutHistory', RelationMap::MANY_TO_ONE, array('history_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcPlayoutHistoryMetaDataTableMap
