<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations\Validators;

/**
 * Email validator
 *
 * Will validate the value of a property to be equal with the accept option
 * value. By default accept value is '1' so can easy compared with the value of
 * an HTML checkbox.
 *
 * Options:
 *   + message    - The error message to display. Default 'is not a valid email'.
 *
 * @vendor  Larium
 * @package Validations
 * @author  Andreas Kollaros <php@andreaskollaros.com>
 * @license MIT {@link http://opensource.org/licenses/mit-license.php}
 */
class Email extends AbstractValidator
{
    protected $message = 'is not a valid email';

    public function validateEach($record, $attribute, $value)
    {
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $record->errors()->add($attribute, ':invalid', $this->options);
        }
    }
}
