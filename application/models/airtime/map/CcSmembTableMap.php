<?php



/**
 * This class defines the structure of the 'cc_smemb' table.
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
class CcSmembTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcSmembTableMap';

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
		$this->setName('cc_smemb');
		$this->setPhpName('CcSmemb');
		$this->setClassname('CcSmemb');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('UID', 'Uid', 'INTEGER', true, null, 0);
		$this->addColumn('GID', 'Gid', 'INTEGER', true, null, 0);
		$this->addColumn('LEVEL', 'Level', 'INTEGER', true, null, 0);
		$this->addColumn('MID', 'Mid', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // CcSmembTableMap
