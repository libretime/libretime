<?php

/*
 *  $Id: ConcreteInheritanceBehaviorTest.php 1458 2010-01-13 16:09:51Z francois $
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../../tools/helpers/bookstore/BookstoreTestBase.php';

/**
 * Tests for ConcreteInheritanceParentBehavior class
 *
 * @author    François Zaniontto
 * @version   $Revision$
 * @package   generator.behavior.concrete_inheritance
 */
class ConcreteInheritanceExcludedParentBehaviorTest extends BookstoreTestBase
{
    public function testHasChildObjectAddChildMethod()
    {

        $article = new ConcreteTag(); // to autoload the BaseConcreteArticle class
        $r = new ReflectionClass('BaseConcreteTag');
        $p =$r->getMethod('addChild')->getParameters();
        $this->assertEquals('ConcreteCategory', $p[0]->getClass()->getName(), 'concrete_inheritance does not generate addChild method child object class');
    }

    public function testHasChildPeerIsValidMethod()
    {
        $r = new ReflectionClass('BaseConcreteTagPeer');
        $p =$r->getMethod('isValid')->getParameters();
        $this->assertEquals('ConcreteCategory', $p[0]->getClass()->getName(), 'concrete_inheritance does not generate isValid method child peer class');
    }

}
