<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

use Larium\Validations\ValidatableInterface;
use Larium\Validations\Validate;

class Author implements ValidatableInterface
{
    use Validate;

    public $firstname;

    public $lastname;

    protected function validations()
    {
        $this->validates(
            array('firstname', 'lastname'),
            array(
                'Numericality' => array(
                    'equal_to' => 5,
                    'allow_null' => true
                )
            )
        );

        $this->validates(
            'firstname',
            array(
                'Numericality' => array(
                    'equal_to' => 5,
                    'allow_null' => true,
                    'if' => function($object) {
                        return !empty($object->lastname);
                    }
                )
            )
        );
    }
}
