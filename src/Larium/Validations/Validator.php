<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations;

class Validator
{
    /**
     *  The hash of object that Validator is validating.
     *
     * @var string
     * @access protected
     */
    protected $object_hash;

    /**
     * List of validators per instance.
     *
     * @var array
     * @access protected
     */
    protected $validators = array();

    public function addConstraint($attrs, array $validations)
    {
        foreach ($validations as $validatorClass => $options) {

            $this->validators[$this->object_hash][] = $this->validatesWith($validatorClass, $attrs, $options);
        }
    }

    public function validate($object)
    {
        $class = get_class($object);
        $this->object_hash = spl_object_hash($object);
        $this->validators[$this->object_hash] = array(); // Reset validators
        $class::loadValidations($this);

        $error = new Errors();

        foreach($this->validators[$this->object_hash] as $validator) {
            $validator->validate($object, $error);
        }

        return $error->getArrayCopy();
    }

    public function validatesWith($class, $attrs, $options)
    {
        $defaults = $this->parse_validates_options($options);

        $defaults['attributes'] = $attrs;

        $klass = "\\Larium\\Validations\\Validators\\" . $class;

        $validator_class = class_exists($klass) ? $klass : $class;

        if (!class_exists($validator_class)){
            throw new \Exception(sprintf("Unknown validator: %s", $validator_class));
        }

        return new $validator_class($defaults);
    }

    private function parse_validates_options(array $options)
    {
        if (is_array($options)) {

            return $options;
        } elseif(is_bool($options)) {

            return array();
        } else {

            return array('with' => $options);
        }
    }
}
