<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations\Validators;

/**
 * Confirmation Validator.
 *
 * Encapsulates the pattern of wanting to validate a password or email
 * address field with a confirmation.
 *
 * Options:
 *   + message    - The error message to display.
 *                  Default 'does not match confirmation'.
 *
 * @vendor  Larium
 * @package Validations
 * @uses    AbstractValidator
 * @author  Andreas Kollaros <andreaskollaros@ymail.com>
 * @license MIT {@link http://opensource.org/licenses/mit-license.php}
 */
class Confirmation extends AbstractValidator
{

    /**
     * {@inheritdoc}
     */
    public function validateEach($record, $attribute, $value)
    {
        $confirmed = $record->readAttributeForValidation(
            "{$attribute}_confirmation"
        );

        if ($value != $confirmed) {
            $record->errors()->add($attribute, ':confirmation', $this->options);
        }
    }
}
