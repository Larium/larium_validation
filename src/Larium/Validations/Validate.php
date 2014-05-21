<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations;
/**
 * Abstract class Validate.
 *
 * To allow validation to a class extend the class with this Validate class.
 * Implement the abstract method {@link validations()} by adding the validations
 * you want to pass the class.
 *
 * <code>
 * class MyClass
 * {
 *      use Larium\Validations\Validate;
 *
 *      protected function validations()
 *      {
 *          $this->validates('property', array('Validator'=>$options);
 *      }
 *
 * }
 * </code>
 */
trait Validate
{
    private $_validated = false;

    protected static $validators = array();

    protected $default_keys = array('if', 'on', 'allow_empty', 'allow_null');

    private $_errors;

    /**
     * Returns errors that occured after the validation of the class.
     *
     * @see \Larium"validations\Errors
     *
     * @return \ArrayIterator
     */
    public function errors()
    {
        $this->_errors = $this->_errors ?: new Errors();
        return $this->_errors;
    }

    public function getErrors()
    {
        return $this->errors();
    }

    /**
     * Checks if a class is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $this->errors()->clear();
        $this->run_validations();

        return $this->errors()->isEmpty();
    }

    /**
     * Checks if a class is invalid
     *
     * @return boolean
     */
    public function isInvalid()
    {
        return !$this->isValid();
    }

    /**
     * Validates properties of a class against validators.
     *
     * validations array must contain the name of Validator class without the
     * namespace and the options for this validator.
     *
     * <code>
     *  $validations = array('Validator' => array('message'=>'my error message'));
     *
     *  $validations = array('Validator' => true); // you must pass true if
     *                                                options are not exist.
     * </code>
     *
     * On examples above the Larium\Validations\Validators\Validator will be
     * called.
     *
     * @param string|array $attrs       Properties of class to validate.
     * @param array        $validations An array of Validator name class and
     *                                  its options.
     *
     * @throws \Exception if a Validator class does not exist.
     *
     * @return void
     */
    final public function validates($attrs, array $validations)
    {
        foreach ($validations as $validatorClass => $options) {

            self::validatesWith($validatorClass, $attrs, $options);
        }
    }

    public static function __callStatic($name, $arguments)
    {
        if (preg_match("/validates(\w+)Of/", $name, $match)) {

            $class = $match[1];

            $attrs = array_shift($arguments);

            $attrs = is_array($attrs) ? $attrs : array($attrs);

            $options = array();
            if (!empty($arguments)) {
                $options = array_shift($arguments);
            }

            self::validatesWith($class, $attrs, $options);
        } else {
            throw new \InvalidArgumentException(sprintf("%s::%s method does not exist!", __CLASS__, $name));
        }
    }

    public static function clearValidators()
    {
        self::$validators = array();
    }

    public static function validatesWith($class, $attrs, $options)
    {
        $defaults = self::parse_validates_options($options);

        $defaults['attributes'] = $attrs;

        $klass = "\\Larium\\Validations\\Validators\\" . $class;

        $validator_class = class_exists($klass) ? $klass : $class;

        if (!class_exists($validator_class)){
            throw new \Exception(sprintf("Unknown validator: %s", $validator_class));
        }

        //$validator = new $validator_class($defaults);

        $key = md5(serialize(array($validator_class => $defaults)));

        self::$validators[$key] = array($validator_class => $defaults);
    }

    public function readAttributeForValidation($attribute)
    {
        return isset($this->$attribute) ? $this->$attribute : null;
    }

    public function getValidators()
    {
        return self::$validators;
    }

    protected function validations()
    {
        return true;
    }

    protected function getValidations()
    {
        return isset(self::$validate) ? self::$validate : array();
    }

    protected function run_validations()
    {
        $this->validations();

        foreach (self::$validators as $meta) {
            $validator_class = key($meta);
            $options = $meta[$validator_class];
            $validator = new $validator_class($options);
            $validator->validate($this);
        }

        return $this->errors()->count() == 0;
    }

    private static function parse_validates_options($options)
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
