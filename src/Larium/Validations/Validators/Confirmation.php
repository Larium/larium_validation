<?php 

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations\Validators;

/**
 * Confirmation validator 
 *
 * Options:
 *   + message    - The error message to display. 
 *                  Default 'does not match confirmation'.
 *
 * @vendor  Larium
 * @package Validations
 * @author  Andreas Kollaros <php@andreaskollaros.com> 
 * @license MIT {@link http://opensource.org/licenses/mit-license.php}
 */
class Confirmation extends \Larium\Validations\Validators\Each
{
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
