<?php

namespace Airtime\MediaItem\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'media_content' table.
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
class MediaContentsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.MediaContentsTableMap';

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
        $this->setName('media_content');
        $this->setPhpName('MediaContents');
        $this->setClassname('Airtime\\MediaItem\\MediaContents');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('media_content_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addForeignKey('media_id', 'MediaId', 'INTEGER', 'media_item', 'id', false, null, null);
        $this->addColumn('position', 'Position', 'INTEGER', false, null, null);
        $this->addColumn('trackoffset', 'TrackOffset', 'REAL', true, null, 0);
        $this->addColumn('cliplength', 'Cliplength', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('cuein', 'Cuein', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('cueout', 'Cueout', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('fadein', 'Fadein', 'TIME', false, null, '00:00:00');
        $this->addColumn('fadeout', 'Fadeout', 'TIME', false, null, '00:00:00');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('MediaItem', 'Airtime\\MediaItem', RelationMap::MANY_TO_ONE, array('media_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // MediaContentsTableMap
