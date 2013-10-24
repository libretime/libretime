<?php
/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../tools/helpers/bookstore/BookstoreTestBase.php';

/**
 * Abstract for DB adapter tests
 *
 * @author     Kamil Dziedzic <arvenil@klecza.pl>
 * @package    runtime.adapter
 */

abstract class DBAdapterTestAbstract extends BookstoreTestBase
{
    abstract public function testQuotingIdentifiers();

}
