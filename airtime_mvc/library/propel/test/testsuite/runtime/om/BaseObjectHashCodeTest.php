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
 * Test class for BaseObject serialization.
 *
 * @author     Francois Zaninotto
 * @version    $Id: PropelCollectionTest.php 1348 2009-12-03 21:49:00Z francois $
 * @package    runtime.om
 * @group hashcode
 */
class BaseObjectHashCodeTest extends BookstoreTestBase
{
    public function testUnsavedObjectCallingHashCodeIsNotChangingObject()
    {
        $book1 = new Book();
        $book1->setTitle('Foo5');
        $book1->setISBN('1234');

        $author = new Author();
        $author->setFirstName('JAne');
        $author->setLastName('JAne');
        $author->addBook($book1);

        $a = clone $author;
        $a->hashCode();

        $this->assertEquals($author, $a);
    }

    public function testSavedObjectCallingHashCodeIsNotChangingObject()
    {
        $book1 = new Book();
        $book1->setTitle('Foo5');
        $book1->setISBN('1234');

        $author = new Author();
        $author->setFirstName('JAne');
        $author->setLastName('JAne');
        $author->addBook($book1);
        $author->save();

        $a = clone $author;
        $a->hashCode();

        $this->assertEquals($author, $a);
    }

    public function testUnsavedObjectCreatesSameHashForIdenticalObjects()
    {
        $book1 = new Book();
        $book1->setTitle('Foo5');
        $book1->setISBN('1234');

        $author1 = new Author();
        $author1->setFirstName('JAne');
        $author1->setLastName('JAne');
        $author1->addBook($book1);

        $author2 = new Author();
        $author2->setFirstName('JAne');
        $author2->setLastName('JAne');
        $author2->addBook($book1);

        $this->assertEquals($author1->hashCode(), $author2->hashCode());
    }

    /**
     * Primary key should differ
     */
    public function testSavedObjectCreatesDifferentHashForIdenticalObjects()
    {
        $book1 = new Book();
        $book1->setTitle('Foo5');
        $book1->setISBN('1234');

        $author1 = new Author();
        $author1->setFirstName('JAne');
        $author1->setLastName('JAne');
        $author1->addBook($book1);
        $author1->save();

        $author2 = new Author();
        $author2->setFirstName('JAne');
        $author2->setLastName('JAne');
        $author2->addBook($book1);
        $author2->save();

        $this->assertNotEquals($author1->hashCode(), $author2->hashCode());
    }
}
