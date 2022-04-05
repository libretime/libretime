<?php



/**
 * This class defines the structure of the 'cc_file_tag' table.
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
class CcFileTagTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcFileTagTableMap';

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
		$this->setName('cc_file_tag');
		$this->setPhpName('CcFileTag');
		$this->setClassname('CcFileTag');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_file_tag_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addForeignKey('FILE_ID', 'DbFileId', 'INTEGER', 'cc_files', 'ID', true, null, null);
		$this->addForeignKey('TAG_ID', 'DbTagId', 'INTEGER', 'cc_tag', 'ID', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcFiles', 'CcFiles', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcTag', 'CcTag', RelationMap::MANY_TO_ONE, array('tag_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcFileTagTableMap
