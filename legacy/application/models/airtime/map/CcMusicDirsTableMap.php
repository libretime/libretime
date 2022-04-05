<?php



/**
 * This class defines the structure of the 'cc_music_dirs' table.
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
class CcMusicDirsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcMusicDirsTableMap';

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
        $this->setName('cc_music_dirs');
        $this->setPhpName('CcMusicDirs');
        $this->setClassname('CcMusicDirs');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_music_dirs_id_seq');
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('directory', 'Directory', 'LONGVARCHAR', false, null, null);
        $this->addColumn('type', 'Type', 'VARCHAR', false, 255, null);
        $this->addColumn('exists', 'Exists', 'BOOLEAN', false, null, true);
        $this->addColumn('watched', 'Watched', 'BOOLEAN', false, null, true);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcFiles', 'CcFiles', RelationMap::ONE_TO_MANY, array('id' => 'directory', ), null, null, 'CcFiless');
    } // buildRelations()

} // CcMusicDirsTableMap
