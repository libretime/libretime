<?php



/**
 * This class defines the structure of the 'cc_access' table.
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
class CcAccessTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcAccessTableMap';

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
		$this->setName('cc_access');
		$this->setPhpName('CcAccess');
		$this->setClassname('CcAccess');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_access_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('GUNID', 'Gunid', 'CHAR', false, 32, null);
		$this->addColumn('TOKEN', 'Token', 'BIGINT', false, null, null);
		$this->addColumn('CHSUM', 'Chsum', 'CHAR', true, 32, '');
		$this->addColumn('EXT', 'Ext', 'VARCHAR', true, 128, '');
		$this->addColumn('TYPE', 'Type', 'VARCHAR', true, 20, '');
		$this->addColumn('PARENT', 'Parent', 'BIGINT', false, null, null);
		$this->addForeignKey('OWNER', 'Owner', 'INTEGER', 'cc_subjs', 'ID', false, null, null);
		$this->addColumn('TS', 'Ts', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcSubjs', 'CcSubjs', RelationMap::MANY_TO_ONE, array('owner' => 'id', ), null, null);
	} // buildRelations()

} // CcAccessTableMap
