<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations\Validators;

/**
 * Exclusion Validator.
 *
 * Validates that the value of the specified attribute is not in a particular
 * enumerable object.
 *
 * Options:
 *   + in      - An enumerable object of items that the value shouldn't be part of.
 *               This can be supplied as a proc, lambda or symbol which returns an
 *               enumerable. If the enumerable is a range the test is performed with.
 *   + message - The error message to display. Default 'is reserved'.
 *
 * <code>
 *      # Example 1
 *
 *      $options = array(
 *          'Exclusion' => array(
 *              'in' => array('admin', 'superuser'),
 *              'message' => 'You don't belong here'
 *          )
 *      );
 *      $this->validates('username', $options);
 *
 *      # Example 2
 *
 *      $options = array(
 *          'Exclusion' => array(
 *              'in' => function($class){
 *                  return array($class->getFirstname(), $class->getUsername());
 *              },
 *              'message' => 'Password should not be the same as your username or first name'
 *          )
 *      );
 *      $this->validates('password', $options);
 *
 *
 * </code>
 *
 * @vendor  Larium
 * @package Validations
 * @uses    Clusivity
 * @author  Andreas Kollaros <andreaskollaros@ymail.com>
 * @license MIT {@link http://opensource.org/licenses/mit-license.php}
 */
class Exclusion extends Clusivity
{

    /**
     * {@inheritdoc}
     */
    public function validateEach($record, $attribute, $value)
    {
        if ($this->is_include($record, $value)) {
            $record->errors()->add(
                $attribute,
                ':exclusion',
                $this->filtered_options($value)
            );
        }
    }
}
