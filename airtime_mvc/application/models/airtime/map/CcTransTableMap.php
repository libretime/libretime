<?php



/**
 * This class defines the structure of the 'cc_trans' table.
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
class CcTransTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcTransTableMap';

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
		$this->setName('cc_trans');
		$this->setPhpName('CcTrans');
		$this->setClassname('CcTrans');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_trans_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('TRTOK', 'Trtok', 'CHAR', true, 16, null);
		$this->addColumn('DIRECTION', 'Direction', 'VARCHAR', true, 128, null);
		$this->addColumn('STATE', 'State', 'VARCHAR', true, 128, null);
		$this->addColumn('TRTYPE', 'Trtype', 'VARCHAR', true, 128, null);
		$this->addColumn('LOCK', 'Lock', 'CHAR', true, 1, 'N');
		$this->addColumn('TARGET', 'Target', 'VARCHAR', false, 255, null);
		$this->addColumn('RTRTOK', 'Rtrtok', 'CHAR', false, 16, null);
		$this->addColumn('MDTRTOK', 'Mdtrtok', 'CHAR', false, 16, null);
		$this->addColumn('GUNID', 'Gunid', 'CHAR', false, 32, null);
		$this->addColumn('PDTOKEN', 'Pdtoken', 'BIGINT', false, null, null);
		$this->addColumn('URL', 'Url', 'VARCHAR', false, 255, null);
		$this->addColumn('LOCALFILE', 'Localfile', 'VARCHAR', false, 255, null);
		$this->addColumn('FNAME', 'Fname', 'VARCHAR', false, 255, null);
		$this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
		$this->addColumn('EXPECTEDSUM', 'Expectedsum', 'CHAR', false, 32, null);
		$this->addColumn('REALSUM', 'Realsum', 'CHAR', false, 32, null);
		$this->addColumn('EXPECTEDSIZE', 'Expectedsize', 'INTEGER', false, null, null);
		$this->addColumn('REALSIZE', 'Realsize', 'INTEGER', false, null, null);
		$this->addColumn('UID', 'Uid', 'INTEGER', false, null, null);
		$this->addColumn('ERRMSG', 'Errmsg', 'VARCHAR', false, 255, null);
		$this->addColumn('JOBPID', 'Jobpid', 'INTEGER', false, null, null);
		$this->addColumn('START', 'Start', 'TIMESTAMP', false, null, null);
		$this->addColumn('TS', 'Ts', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // CcTransTableMap
