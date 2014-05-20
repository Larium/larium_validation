<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations\Validators;

abstract class AbstractValidator
{
    /**
     * @var array $attributes Class attributes to validates.
     * @access protected
     */
    protected $attributes = array();

    /**
     * @var array
     * @access protected
     */
    protected $options = array();

    /**
     * @var string Name of validator class.
     * @access protected
     */
    protected $kind;

    public function __construct($options)
    {
        $this->attributes = isset($options['attributes'])
            ? (
                !is_array($options['attributes'])
                ? array($options['attributes'])
                : $options['attributes']
            )
            : array();

        if (empty($this->attributes)) {

            throw new \Exception('attributes cannot be empty');
        }

        if (isset($options['attributes'])) {
            unset($options['attributes']);
        }

        $this->options = $options;

        $this->check_validity();
    }

    public function validate($record)
    {
        $if = $this->validates_if($record);
        if (false == $if) {

            return true;
        }

        foreach ($this->attributes as $attribute) {

            $value = $record->readAttributeForValidation($attribute);

            if (   (null === $value && isset($this->options['allow_null'])
                && true == $this->options['allow_null'])
                || (empty($value) && isset($this->options['allow_empty'])
                && true == $this->options['allow_empty'])
            ) {
                continue;
            }

            $this->validateEach($record, $attribute, $value);
        }
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function kind()
    {
        if (null == $this->kind) {
            $name = explode('\\',get_class($this));
            $this->kind = strtolower(array_pop($name));
        }

        return $this->kind;
    }

    protected function validates_if($record)
    {
        if (isset($this->options['if'])) {
            $if = $this->options['if'];
            if (is_callable($if)) {
                return $if($record);
            } else if (method_exists($record, $if)) {
                return $record->$if();
            }
        }

        return true;
    }

    /**
     * Validates each attribute of record class.
     *
     * @param mixed  $record    The class instance to be validated
     * @param string $attribute The property of class to validate.
     * @param mixed  $value     The value to validate against attribute.
     * @access public
     * @return void
     */
    abstract public function validateEach($record, $attribute, $value);

    protected function check_validity()
    {

    }
}
