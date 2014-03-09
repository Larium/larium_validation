<?php 

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations\Validators;

class Exclusion extends \Larium\Validations\Validators\Clusivity
{
    public function validateEach($record, $attribute, $value)
    {
        if ($this->is_include($record,$value)) {
            $record->errors()->add($attribute, ':exclusion',$this->filtered_options($value));
        }
    }
}
