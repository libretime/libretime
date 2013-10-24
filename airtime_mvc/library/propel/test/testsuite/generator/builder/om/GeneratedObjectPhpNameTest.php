<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * Tests for changed php names in relations.
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @package generator.builder.om
 */
class GeneratedObjectPhpNameTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        if (!class_exists('PhpNameTest\Page')) {
            $schema = <<<XML
<database name="php_name_test" namespace="PhpNameTest" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://xsd.propelorm.org/1.6/database.xsd">
    <table name="php_name_test_page" phpName="Page">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
        <column name="title" type="VARCHAR" size="100" primaryString="true" />
    </table>

    <table name="php_name_test_author" phpName="Author">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
        <column name="name" type="VARCHAR" size="100" primaryString="true" />
    </table>

    <table name="php_name_test_author_page" phpName="AuthorPage" isCrossRef="true">
        <column name="author_id" required="true" primaryKey="true" autoIncrement="false" type="INTEGER" />
        <column name="page_id" required="true" primaryKey="true" autoIncrement="false" type="INTEGER" />

        <foreign-key foreignTable="php_name_test_author">
            <reference local="author_id" foreign="id" />
        </foreign-key>
        <foreign-key foreignTable="php_name_test_page" refPhpName="Writer">
            <reference local="page_id" foreign="id" />
        </foreign-key>
    </table>
</database>
XML;

            $builder = new PropelQuickBuilder();
            $builder->setSchema($schema);
            $builder->build();
        }

        // Clear existing data, if any.
        \PhpNameTest\PagePeer::doDeleteAll();
        \PhpNameTest\AuthorPeer::doDeleteAll();
        \PhpNameTest\AuthorPagePeer::doDeleteAll();
    }

    public function testDeleteWorksWithRefPhpName()
    {
        $author = new \PhpNameTest\Author();
        $author->setName('Really Important');
        $author->save();

        $anotherAuthor = new \PhpNameTest\Author();
        $anotherAuthor->setName('Not S. Important');
        $anotherAuthor->save();

        $page = new \PhpNameTest\Page();
        $page->setTitle('Awesomeness at its finest');
        $page->save();

        $page->addAuthor($author);
        $page->addAuthor($anotherAuthor);
        $page->save();

        // Should not result in "Fatal error: Class 'PhpNameTest\om\WriterQuery' not found"
        $page->removeAuthor($author);
        $page->save();
    }
}
