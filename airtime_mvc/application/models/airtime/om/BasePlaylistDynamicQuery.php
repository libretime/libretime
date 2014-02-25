<?php

namespace Airtime\MediaItem\om;

use \BasePeer;
use \Criteria;
use \PropelException;
use \PropelPDO;
use Airtime\MediaItem\PlaylistDynamicQuery;
use Airtime\MediaItem\PlaylistPeer;
use Airtime\MediaItem\PlaylistQuery;

/**
 * Skeleton subclass for representing a query for one of the subclasses of the 'media_playlist' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime.om
 */
class BasePlaylistDynamicQuery extends PlaylistQuery {

    /**
     * Returns a new PlaylistDynamicQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return PlaylistDynamicQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PlaylistDynamicQuery) {
            return $criteria;
        }
        $query = new PlaylistDynamicQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Filters the query to target only PlaylistDynamic objects.
     */
    public function preSelect(PropelPDO $con)
    {
        $this->addUsingAlias(PlaylistPeer::CLASS_KEY, PlaylistPeer::CLASSKEY_1);
    }

    /**
     * Filters the query to target only PlaylistDynamic objects.
     */
    public function preUpdate(&$values, PropelPDO $con, $forceIndividualSaves = false)
    {
        $this->addUsingAlias(PlaylistPeer::CLASS_KEY, PlaylistPeer::CLASSKEY_1);
    }

    /**
     * Filters the query to target only PlaylistDynamic objects.
     */
    public function preDelete(PropelPDO $con)
    {
        $this->addUsingAlias(PlaylistPeer::CLASS_KEY, PlaylistPeer::CLASSKEY_1);
    }

    /**
     * Issue a DELETE query based on the current ModelCriteria deleting all rows in the table
     * Having the PlaylistDynamic class.
     * This method is called by ModelCriteria::deleteAll() inside a transaction
     *
     * @param PropelPDO $con a connection object
     *
     * @return integer the number of deleted rows
     */
    public function doDeleteAll($con)
    {
        // condition on class key is already added in preDelete()
        return parent::doDelete($con);
    }


    /**
     * Issue a SELECT ... LIMIT 1 query based on the current ModelCriteria
     * and format the result with the current formatter
     * By default, returns a model object
     *
     * @param PropelPDO $con an optional connection object
     *
     * @return mixed the result, formatted by the current formatter
     *
     * @throws PropelException
     */
    public function findOneOrCreate($con = null)
    {
        if ($this->joins) {
            throw new PropelException('findOneOrCreate() cannot be used on a query with a join, because Propel cannot transform a SQL JOIN into a subquery. You should split the query in two queries to avoid joins.');
        }
        if (!$ret = $this->findOne($con)) {
            $class = PlaylistPeer::CLASSNAME_1;
            $obj = new $class;
            foreach ($this->keys() as $key) {
                $obj->setByName($key, $this->getValue($key), BasePeer::TYPE_COLNAME);
            }
            $ret = $this->getFormatter()->formatRecord($obj);
        }

        return $ret;
    }

} // BasePlaylistDynamicQuery
