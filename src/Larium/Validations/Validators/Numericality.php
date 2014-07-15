<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations\Validators;

class Numericality extends AbstractValidator
{
    protected $checks = array(
        'greater_than_or_equal_to' => '>=',
        'less_than_or_equal_to'    => '<=',
        'greater_than' => '>',
        'equal_to'     => '==',
        'less_than'    => '<',
        'odd'          => 'odd',
        'even'         => 'even',
        'other_than'   => '!='
    );

    private $_reserved_options = array(
        'greater_than_or_equal_to' => '>=',
        'less_than_or_equal_to'    => '<=',
        'greater_than' => '>',
        'equal_to'     => '==',
        'less_than'    => '<',
        'odd'          => 'odd',
        'even'         => 'even',
        'other_than'   => '!=',
        'only_integer' => null
    );

    /**
     * {@inheritdoc}
     */
    public function validateEach($record, $attribute, $value)
    {
        if (false === is_numeric($value)) {

            $record->errors()->add(
                $attribute,
                ":not_a_number",
                $this->filtered_options($value)
            );

            return;
        }

        if (array_key_exists('only_integer', $this->options)) {
            if ($this->options['only_integer']) {
                if (is_null($value = $this->parse_value_as_integer($value))) {
                    $record->errors()->add(
                        $attribute,
                        ':not_an_integer',
                        $this->filtered_options($value)
                    );

                    return;
                }
            }
        }

        $options = array_intersect_key($this->options, $this->checks);
        foreach ($options as $option => $option_value) {
            switch ($option) {
                case 'odd':
                    if ( 1 !== (1 & $value))
                        $record->errors()->add(
                            $attribute,
                            ":$option",
                            $this->filtered_options($value)
                        );
                    break;
                case 'even':
                    if ( 0 !== (1 & $value))
                        $record->errors()->add(
                            $attribute,
                            ":$option",
                            $this->filtered_options($value)
                        );
                    break;
                default:

                    if ( is_callable($option_value)) {
                        $option_value = $option_value($record);
                    } else if (method_exists($record, $option_value)) {
                        $option_value = $record->$option_value();
                    }

                    if (false === $this->check_value($value, $option_value, $this->checks[$option])) {
                        $o = $this->filtered_options($value);
                        $o['count'] = $option_value;
                        $record->errors()->add($attribute, ":$option", $o);
                    }
                    break;
            }
        }
    }

    protected function check_validity()
    {
        $options = array_intersect_key($this->options, $this->checks);
        foreach ($options as $option => $value) {

            $odd_or_even = ($option == 'odd') || ($option == 'even');
            $numeric_or_callble = is_numeric($value) || is_callable($value);
            if ($odd_or_even || $numeric_or_callble) {
                continue;
            }

            throw new \InvalidArgumentException(sprintf('%s must be a number or a function', $option));
        }
    }

    protected function parse_value_as_number($value)
    {
        if (is_numeric($value)) {
            if (is_float($value)) {
                return (float) $value;
            }

            if (is_int($value)) {
                return (int) $value;
            }
        }
    }

    protected function parse_value_as_integer($value)
    {
        if (is_numeric($value) && is_int($value)) {

            return (int) $value;
        } else {

            return null;
        }
    }

    protected function filtered_options($value)
    {
        $options = array_diff_key($this->options, $this->_reserved_options);
        $options['value'] = $value;

        return $options;
    }

    private function check_value($record_value, $check_value, $operator)
    {
        switch ($operator) {
            case '>':
                return $record_value > $check_value;
                break;
            case '<':
                return $record_value < $check_value;
                break;
            case '==':
                return $record_value == $check_value;
                break;
            case '>=':
                return $record_value >= $check_value;
                break;
            case '<=':
                return $record_value <= $check_value;
                break;
        }
    }
}
