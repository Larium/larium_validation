<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations;

use Author;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
        $author = new Author();

        $validator = new Validator();
        $errors = $validator->validate($author);

        $this->assertEmpty($errors);

        $author->firstname = 'John';
        $author->lastname = 'Doe';
        $errors = $validator->validate($author);

        print_r($errors);
    }
}
