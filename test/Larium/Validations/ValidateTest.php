<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations;

use Author;

class ValidateTest extends \PHPUnit_Framework_TestCase
{
    public function testUniqueValidators()
    {

        Author::validatesPresenceOf('lastname');

        $a = new Author();

        $a->isValid();


        $this->assertEquals(3, count($a->getValidators()));

        Author::clearValidators();
        $a->isValid();

        $this->assertEquals(2, count($a->getValidators()));
    }
}
