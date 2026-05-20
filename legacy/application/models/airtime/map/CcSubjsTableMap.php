<?php



/**
 * This class defines the structure of the 'cc_subjs' table.
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
class CcSubjsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcSubjsTableMap';

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
        $this->setName('cc_subjs');
        $this->setPhpName('CcSubjs');
        $this->setClassname('CcSubjs');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_subjs_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('login', 'DbLogin', 'VARCHAR', true, 255, '');
        $this->addColumn('pass', 'DbPass', 'VARCHAR', true, 255, '');
        $this->addColumn('type', 'DbType', 'CHAR', true, 1, 'U');
        $this->addColumn('is_active', 'DbIsActive', 'BOOLEAN', true, null, false);
        $this->addColumn('first_name', 'DbFirstName', 'VARCHAR', true, 255, '');
        $this->addColumn('last_name', 'DbLastName', 'VARCHAR', true, 255, '');
        $this->addColumn('lastlogin', 'DbLastlogin', 'TIMESTAMP', false, null, null);
        $this->addColumn('lastfail', 'DbLastfail', 'TIMESTAMP', false, null, null);
        $this->addColumn('skype_contact', 'DbSkypeContact', 'VARCHAR', false, null, null);
        $this->addColumn('jabber_contact', 'DbJabberContact', 'VARCHAR', false, null, null);
        $this->addColumn('email', 'DbEmail', 'VARCHAR', false, null, null);
        $this->addColumn('cell_phone', 'DbCellPhone', 'VARCHAR', false, null, null);
        $this->addColumn('login_attempts', 'DbLoginAttempts', 'INTEGER', false, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcFilesRelatedByDbOwnerId', 'CcFiles', RelationMap::ONE_TO_MANY, array('id' => 'owner_id', ), null, null, 'CcFilessRelatedByDbOwnerId');
        $this->addRelation('CcFilesRelatedByDbEditedby', 'CcFiles', RelationMap::ONE_TO_MANY, array('id' => 'editedby', ), null, null, 'CcFilessRelatedByDbEditedby');
        $this->addRelation('CcShowHosts', 'CcShowHosts', RelationMap::ONE_TO_MANY, array('id' => 'subjs_id', ), 'CASCADE', null, 'CcShowHostss');
        $this->addRelation('CcPlaylist', 'CcPlaylist', RelationMap::ONE_TO_MANY, array('id' => 'creator_id', ), 'CASCADE', null, 'CcPlaylists');
        $this->addRelation('CcBlock', 'CcBlock', RelationMap::ONE_TO_MANY, array('id' => 'creator_id', ), 'CASCADE', null, 'CcBlocks');
        $this->addRelation('CcPref', 'CcPref', RelationMap::ONE_TO_MANY, array('id' => 'subjid', ), 'CASCADE', null, 'CcPrefs');
        $this->addRelation('CcSubjsToken', 'CcSubjsToken', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'CcSubjsTokens');
        $this->addRelation('Podcast', 'Podcast', RelationMap::ONE_TO_MANY, array('id' => 'owner', ), 'CASCADE', null, 'Podcasts');
    } // buildRelations()

} // CcSubjsTableMap
