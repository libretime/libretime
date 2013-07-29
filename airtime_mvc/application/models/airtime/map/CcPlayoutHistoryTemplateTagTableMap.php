<?php



/**
 * This class defines the structure of the 'cc_playout_history_template_field' table.
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
class CcPlayoutHistoryTemplateTagTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcPlayoutHistoryTemplateTagTableMap';

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
		$this->setName('cc_playout_history_template_field');
		$this->setPhpName('CcPlayoutHistoryTemplateTag');
		$this->setClassname('CcPlayoutHistoryTemplateTag');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_playout_history_template_field_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addForeignKey('TEMPLATE_ID', 'DbTemplateId', 'INTEGER', 'cc_playout_history_template', 'ID', true, null, null);
		$this->addColumn('NAME', 'DbName', 'VARCHAR', true, 128, null);
		$this->addColumn('TYPE', 'DbType', 'VARCHAR', true, 128, null);
		$this->addColumn('IS_FILE_MD', 'DbIsFileMD', 'BOOLEAN', true, null, false);
		$this->addColumn('POSITION', 'DbTagPosition', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcPlayoutHistoryTemplate', 'CcPlayoutHistoryTemplate', RelationMap::MANY_TO_ONE, array('template_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcPlayoutHistoryTemplateTagTableMap
