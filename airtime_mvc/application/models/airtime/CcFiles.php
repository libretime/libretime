<?php

/**
 * Skeleton subclass for representing a row from the 'cc_files' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.campcaster
 */
class CcFiles extends BaseCcFiles {

	public function getDbLength($format = "H:i:s.u")
    {
        return parent::getDbCliplength($format);
    }

    public function setDbLength($v)
    {
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else {

            try {

                $dt = new DateTime($v);

            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        $this->length = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcFilesPeer::LENGTH;

        return $this;
    }


} // CcFiles
