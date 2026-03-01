<?php



/**
 * This class defines the structure of the 'cc_playout_history_template' table.
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
class CcPlayoutHistoryTemplateTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcPlayoutHistoryTemplateTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('cc_playout_history_template');
        $this->setPhpName('CcPlayoutHistoryTemplate');
        $this->setClassname('CcPlayoutHistoryTemplate');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_playout_history_template_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'DbName', 'VARCHAR', true, 128, null);
        $this->addColumn('type', 'DbType', 'VARCHAR', true, 35, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcPlayoutHistoryTemplateField', 'CcPlayoutHistoryTemplateField', RelationMap::ONE_TO_MANY, array('id' => 'template_id', ), 'CASCADE', null, 'CcPlayoutHistoryTemplateFields');
    } // buildRelations()

} // CcPlayoutHistoryTemplateTableMap
