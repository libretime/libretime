<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../../generator/lib/util/PropelSQLParser.php';

/**
 *
 * @package    generator.util
 */
class PropelSQLParserTest extends PHPUnit_Framework_TestCase
{
    public function stripSqlCommentsDataProvider()
    {
        return array(
            array('', ''),
            array('foo with no comments', 'foo with no comments'),
            array('foo with // inline comments', 'foo with // inline comments'),
            array("foo with\n// comments", "foo with\n"),
            array(" // comments preceded by blank\nfoo", "foo"),
            array("// slash-style comments\nfoo", "foo"),
            array("-- dash-style comments\nfoo", "foo"),
            array("# hash-style comments\nfoo", "foo"),
            array("/* c-style comments*/\nfoo", "\nfoo"),
            array("foo with\n// comments\nwith foo", "foo with\nwith foo"),
            array("// comments with\nfoo with\n// comments\nwith foo", "foo with\nwith foo"),
        );
    }

    /**
     * @dataProvider stripSqlCommentsDataProvider
     */
    public function testStripSQLComments($input, $output)
    {
        $parser = new PropelSQLParser();
        $parser->setSQL($input);
        $parser->stripSQLCommentLines();
        $this->assertEquals($output, $parser->getSQL());
    }

    public function convertLineFeedsToUnixStyleDataProvider()
    {
        return array(
            array('', ''),
            array("foo bar", "foo bar"),
            array("foo\nbar", "foo\nbar"),
            array("foo\rbar", "foo\nbar"),
            array("foo\r\nbar", "foo\nbar"),
            array("foo\r\nbar\rbaz\nbiz\r\n", "foo\nbar\nbaz\nbiz\n"),
        );
    }

    /**
     * @dataProvider convertLineFeedsToUnixStyleDataProvider
     */
    public function testConvertLineFeedsToUnixStyle($input, $output)
    {
        $parser = new PropelSQLParser();
        $parser->setSQL($input);
        $parser->convertLineFeedsToUnixStyle();
        $this->assertEquals($output, $parser->getSQL());
    }

    public function explodeIntoStatementsDataProvider()
    {
        return array(
            array('', array()),
            array('foo', array('foo')),
            array('foo;', array('foo')),
            array('foo; ', array('foo')),
            array('foo;bar', array('foo', 'bar')),
            array('foo;bar;', array('foo', 'bar')),
            array("f\no\no;\nb\nar\n;", array("f\no\no", "b\nar")),
            array('foo";"bar;baz', array('foo";"bar', 'baz')),
            array('foo\';\'bar;baz', array('foo\';\'bar', 'baz')),
            array('foo"\";"bar;', array('foo"\";"bar')),
        );
    }
    /**
     * @dataProvider explodeIntoStatementsDataProvider
     */
    public function testExplodeIntoStatements($input, $output)
    {
        $parser = new PropelSQLParser();
        $parser->setSQL($input);
        $this->assertEquals($output, $parser->explodeIntoStatements());
    }

    public function testDelimiterOneCharacter()
    {
        $parser = new PropelSQLParser();
        $parser->setSQL('DELIMITER |');
        $this->assertEquals(array(), $parser->explodeIntoStatements());
    }

    public function testDelimiterMultipleCharacters()
    {
        $parser = new PropelSQLParser();
        $parser->setSQL('DELIMITER ||');
        $this->assertEquals(array(), $parser->explodeIntoStatements());

        $parser = new PropelSQLParser();
        $parser->setSQL('DELIMITER |||');
        $this->assertEquals(array(), $parser->explodeIntoStatements());

        $parser = new PropelSQLParser();
        $parser->setSQL('DELIMITER ////');
        $this->assertEquals(array(), $parser->explodeIntoStatements());
    }

    public function singleDelimiterExplodeIntoStatementsDataProvider()
    {
        return array(
            array("delimiter |", array()),
            array("DELIMITER |", array()),
            array("foo;\nDELIMITER |", array('foo')),
            array("foo;\nDELIMITER |\nbar", array('foo', 'bar')),
            array("foo;\nDELIMITER |\nbar;", array('foo', 'bar;')),
            array("foo;\nDELIMITER |\nbar;\nbaz;", array('foo', "bar;\nbaz;")),
            array("foo;\nDELIMITER |\nbar;\nbaz;\nDELIMITER ;", array('foo', "bar;\nbaz;")),
            array("foo;\nDELIMITER |\nbar;\nbaz;\nDELIMITER ;\nqux", array('foo', "bar;\nbaz;", 'qux')),
            array("foo;\nDELIMITER |\nbar;\nbaz;\nDELIMITER ;\nqux;", array('foo', "bar;\nbaz;", 'qux')),
            array("DELIMITER |\n".'foo"|"bar;'."\nDELIMITER ;\nbaz", array('foo"|"bar;', 'baz')),
            array("DELIMITER |\n".'foo\'|\'bar;'."\nDELIMITER ;\nbaz", array('foo\'|\'bar;', 'baz')),
            array("DELIMITER |\n".'foo"\"|"bar;'."\nDELIMITER ;\nbaz", array('foo"\"|"bar;', 'baz')),
        );
    }

    /**
     * @dataProvider singleDelimiterExplodeIntoStatementsDataProvider
     */
    public function testSingleDelimiterExplodeIntoStatements($input, $output)
    {
        $parser = new PropelSQLParser();
        $parser->setSQL($input);
        $this->assertEquals($output, $parser->explodeIntoStatements());
    }

    public function twoCharDelimiterExplodeIntoStatementsDataProvider()
    {
        return array(
            array("delimiter ||", array()),
            array("DELIMITER ||", array()),
            array("foo;\nDELIMITER ||", array('foo')),
            array("foo;\nDELIMITER ||\nbar", array('foo', 'bar')),
            array("foo;\nDELIMITER ||\nbar;", array('foo', 'bar;')),
            array("foo;\nDELIMITER ||\nbar;\nbaz;", array('foo', "bar;\nbaz;")),
            array("foo;\nDELIMITER ||\nbar;\nbaz;\nDELIMITER ;", array('foo', "bar;\nbaz;")),
            array("foo;\nDELIMITER ||\nbar;\nbaz;\nDELIMITER ;\nqux", array('foo', "bar;\nbaz;", 'qux')),
            array("foo;\nDELIMITER ||\nbar;\nbaz;\nDELIMITER ;\nqux;", array('foo', "bar;\nbaz;", 'qux')),
            array("DELIMITER ||\n".'foo"||"bar;'."\nDELIMITER ;\nbaz", array('foo"||"bar;', 'baz')),
            array("DELIMITER ||\n".'foo\'||\'bar;'."\nDELIMITER ;\nbaz", array('foo\'||\'bar;', 'baz')),
            array("DELIMITER ||\n".'foo"\"||"bar;'."\nDELIMITER ;\nbaz", array('foo"\"||"bar;', 'baz')),
        );
    }

    /**
     * @dataProvider twoCharDelimiterExplodeIntoStatementsDataProvider
     */
    public function testTwoCharDelimiterExplodeIntoStatements($input, $output)
    {
        $parser = new PropelSQLParser();
        $parser->setSQL($input);
        $this->assertEquals($output, $parser->explodeIntoStatements());
    }

    public function threeCharDelimiterExplodeIntoStatementsDataProvider()
    {
        return array(
            array("delimiter |||", array()),
            array("DELIMITER |||", array()),
            array("foo;\nDELIMITER |||", array('foo')),
            array("foo;\nDELIMITER |||\nbar", array('foo', 'bar')),
            array("foo;\nDELIMITER |||\nbar;", array('foo', 'bar;')),
            array("foo;\nDELIMITER |||\nbar;\nbaz;", array('foo', "bar;\nbaz;")),
            array("foo;\nDELIMITER |||\nbar;\nbaz;\nDELIMITER ;", array('foo', "bar;\nbaz;")),
            array("foo;\nDELIMITER |||\nbar;\nbaz;\nDELIMITER ;\nqux", array('foo', "bar;\nbaz;", 'qux')),
            array("foo;\nDELIMITER |||\nbar;\nbaz;\nDELIMITER ;\nqux;", array('foo', "bar;\nbaz;", 'qux')),
            array("DELIMITER |||\n".'foo"|||"bar;'."\nDELIMITER ;\nbaz", array('foo"|||"bar;', 'baz')),
            array("DELIMITER |||\n".'foo\'|||\'bar;'."\nDELIMITER ;\nbaz", array('foo\'|||\'bar;', 'baz')),
            array("DELIMITER |||\n".'foo"\"|||"bar;'."\nDELIMITER ;\nbaz", array('foo"\"|||"bar;', 'baz')),
        );
    }

    /**
     * @dataProvider threeCharDelimiterExplodeIntoStatementsDataProvider
     */
    public function testThreeCharDelimiterExplodeIntoStatements($input, $output)
    {
        $parser = new PropelSQLParser();
        $parser->setSQL($input);
        $this->assertEquals($output, $parser->explodeIntoStatements());
    }

    public function fourCharDelimiterExplodeIntoStatementsDataProvider()
    {
        return array(
            array("delimiter ////", array()),
            array("DELIMITER ////", array()),
            array("foo;\nDELIMITER ////", array('foo')),
            array("foo;\nDELIMITER ////\nbar", array('foo', 'bar')),
            array("foo;\nDELIMITER ////\nbar;", array('foo', 'bar;')),
            array("foo;\nDELIMITER ////\nbar;\nbaz;", array('foo', "bar;\nbaz;")),
            array("foo;\nDELIMITER ////\nbar;\nbaz;\nDELIMITER ;", array('foo', "bar;\nbaz;")),
            array("foo;\nDELIMITER ////\nbar;\nbaz;\nDELIMITER ;\nqux", array('foo', "bar;\nbaz;", 'qux')),
            array("foo;\nDELIMITER ////\nbar;\nbaz;\nDELIMITER ;\nqux;", array('foo', "bar;\nbaz;", 'qux')),
            array("DELIMITER ////\n".'foo"////"bar;'."\nDELIMITER ;\nbaz", array('foo"////"bar;', 'baz')),
            array("DELIMITER ////\n".'foo\'////\'bar;'."\nDELIMITER ;\nbaz", array('foo\'////\'bar;', 'baz')),
            array("DELIMITER ////\n".'foo"\"////"bar;'."\nDELIMITER ;\nbaz", array('foo"\"////"bar;', 'baz')),
        );
    }

    /**
     * @dataProvider fourCharDelimiterExplodeIntoStatementsDataProvider
     */
    public function testFourCharDelimiterExplodeIntoStatements($input, $output)
    {
        $parser = new PropelSQLParser();
        $parser->setSQL($input);
        $this->assertEquals($output, $parser->explodeIntoStatements());
    }
}
