<?php



/**
 * This class defines the structure of the 'cc_show_rebroadcast' table.
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
class CcShowRebroadcastTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcShowRebroadcastTableMap';

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
        $this->setName('cc_show_rebroadcast');
        $this->setPhpName('CcShowRebroadcast');
        $this->setClassname('CcShowRebroadcast');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_show_rebroadcast_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('day_offset', 'DbDayOffset', 'VARCHAR', true, null, null);
        $this->addColumn('start_time', 'DbStartTime', 'TIME', true, null, null);
        $this->addForeignKey('show_id', 'DbShowId', 'INTEGER', 'cc_show', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcShow', 'CcShow', RelationMap::MANY_TO_ONE, array('show_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // CcShowRebroadcastTableMap
