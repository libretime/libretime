<?php



/**
 * This class defines the structure of the 'cc_show_days' table.
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
class CcShowDaysTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcShowDaysTableMap';

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
        $this->setName('cc_show_days');
        $this->setPhpName('CcShowDays');
        $this->setClassname('CcShowDays');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_show_days_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('first_show', 'DbFirstShow', 'DATE', true, null, null);
        $this->addColumn('last_show', 'DbLastShow', 'DATE', false, null, null);
        $this->addColumn('start_time', 'DbStartTime', 'TIME', true, null, null);
        $this->addColumn('timezone', 'DbTimezone', 'VARCHAR', true, null, null);
        $this->addColumn('duration', 'DbDuration', 'VARCHAR', true, null, null);
        $this->addColumn('day', 'DbDay', 'TINYINT', false, null, null);
        $this->addColumn('repeat_type', 'DbRepeatType', 'TINYINT', true, null, null);
        $this->addColumn('next_pop_date', 'DbNextPopDate', 'DATE', false, null, null);
        $this->addForeignKey('show_id', 'DbShowId', 'INTEGER', 'cc_show', 'id', true, null, null);
        $this->addColumn('record', 'DbRecord', 'TINYINT', false, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcShow', 'CcShow', RelationMap::MANY_TO_ONE, array('show_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // CcShowDaysTableMap
