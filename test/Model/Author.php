<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

use Larium\Validations\Validator;

class Author
{
    public $firstname;

    public $lastname;

    public static function loadValidations(Validator $validator)
    {
        $validator->addConstraint(
            array('firstname', 'lastname'),
            array(
                'Length' => array(
                    'min' => 5,
                    'allow_null' => true
                )
            )
        );

        $validator->addConstraint(
            'firstname',
            array(
                'Length' => array(
                    'min' => 5,
                    'allow_null' => true,
                    'if' => function($object) {
                        return !empty($object->lastname);
                    }
                )
            )
        );
    }
}
