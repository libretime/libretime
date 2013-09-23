<?php



/**
 * This class defines the structure of the 'cc_tag' table.
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
class CcTagTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcTagTableMap';

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
		$this->setName('cc_tag');
		$this->setPhpName('CcTag');
		$this->setClassname('CcTag');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_tag_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('TAG_NAME', 'DbTagName', 'VARCHAR', true, 128, null);
		$this->addColumn('TAG_TYPE', 'DbTagType', 'VARCHAR', true, 128, 'boolean');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcFileTag', 'CcFileTag', RelationMap::ONE_TO_MANY, array('id' => 'tag_id', ), 'CASCADE', null);
    $this->addRelation('CcPlayoutHistoryMetaData', 'CcPlayoutHistoryMetaData', RelationMap::ONE_TO_MANY, array('id' => 'tag_id', ), 'CASCADE', null);
    $this->addRelation('CcPlayoutHistoryTemplateTag', 'CcPlayoutHistoryTemplateTag', RelationMap::ONE_TO_MANY, array('id' => 'tag_id', ), 'CASCADE', null);
	} // buildRelations()

} // CcTagTableMap
