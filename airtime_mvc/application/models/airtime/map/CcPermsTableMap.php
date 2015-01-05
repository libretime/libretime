<?php



/**
 * This class defines the structure of the 'cc_perms' table.
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
class CcPermsTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcPermsTableMap';

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
		$this->setName('cc_perms');
		$this->setPhpName('CcPerms');
		$this->setClassname('CcPerms');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('PERMID', 'Permid', 'INTEGER', true, null, null);
		$this->addForeignKey('SUBJ', 'Subj', 'INTEGER', 'cc_subjs', 'ID', false, null, null);
		$this->addColumn('ACTION', 'Action', 'VARCHAR', false, 20, null);
		$this->addColumn('OBJ', 'Obj', 'INTEGER', false, null, null);
		$this->addColumn('TYPE', 'Type', 'CHAR', false, 1, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcSubjs', 'CcSubjs', RelationMap::MANY_TO_ONE, array('subj' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcPermsTableMap
