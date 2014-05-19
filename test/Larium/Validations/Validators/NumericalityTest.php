<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Validations\Validators;

use Larium\Validations\ValidatableInterface;
use Larium\Validations\Validate;
use Topic;

class NumberValidation implements ValidatableInterface
{
    use Validate;

    public $max_length = 4;

    public $min_length = 50;

    public $odd_or_null;

    public $odd;

    public $even;

    public function validations()
    {
        $this->validates(
            'max_length',
            array(
                'Numericality' => array(
                    'less_than_or_equal_to' => 5
                )
            )
        );

        $this->validates(
            'min_length',
            array(
                'Numericality' => array(
                    'greater_than_or_equal_to' => 40,
                )
            )
        );
    }
}

class NumericalityTest extends \PHPUnit_Framework_TestCase
{
    private static $NIL = [null];
    private static $BLANK = ["", " ", " \t \r \n"];
    private static $BIGDECIMAL_STRINGS = ['12345678901234567890.1234567890']; # 30 significant digits
    private static $FLOAT_STRINGS = ['0.0', '+0.0', '-0.0', '10.0', '10.5', '-10.5', '-0.0001', '-090.1', '90.1e1', '-90.1e5', '-90.1e-5', '90e-5'];
    private static $INTEGER_STRINGS = ['0', '+0', '-0', '10', '+10', '-10', '0090', '-090'];
    private static $FLOATS = [0.0, 10.0, 10.5, -10.5, -0.0001];
    private static $INTEGERS = [0, 10, -10];
    private static $JUNK = ["not a number", "42 not a number", "0xinvalidhex", "00-1", "--3", "+-3", "+3-1", "-+019.0", "12.12.13.12", "123\nnot a number"];

    public function setUp()
    {
        Topic::clearValidators();
    }

    public function testDefaultNumericality()
    {
        Topic::validatesNumericalityOf('approved');

        $this->invalid(array_merge(self::$NIL, self::$BLANK,self::$JUNK));
        $this->valid(array_merge(self::$FLOATS, self::$INTEGERS, self::$BIGDECIMAL_STRINGS));
    }

    public function testNumericalityWithNullAllowed()
    {
        Topic::validatesNumericalityOf('approved', ['allow_null' => true]);

        $this->invalid(array_merge(self::$BLANK, self::$JUNK));
        $this->valid(array_merge(self::$NIL, self::$FLOATS, self::$INTEGERS, self::$BIGDECIMAL_STRINGS));
    }

    public function testNumericalityWithIntegerOnly()
    {
        Topic::validatesNumericalityOf('approved', ['only_integer' => true]);

        $this->invalid(array_merge(self::$NIL, self::$BLANK, self::$JUNK, self::$FLOATS, self::$BIGDECIMAL_STRINGS));
        $this->valid(array_merge(self::$INTEGERS));
    }

    public function testNumericalityWithIntegerOnlyAndNullAllowed()
    {
        Topic::validatesNumericalityOf('approved', ['allow_null' => true, 'only_integer' => true]);

        $this->invalid(array_merge(self::$BLANK, self::$JUNK, self::$FLOATS, self::$BIGDECIMAL_STRINGS));
        $this->valid(array_merge(self::$NIL, self::$INTEGERS));
    }

    public function testNumericalityWithGreaterThan()
    {
        Topic::validatesNumericalityOf('approved', ['greater_than' => 10]);

        $this->invalid([-10, 10], 'greater_than');
        $this->valid([11]);
    }

    public function testNumericalityWithGreaterThanOrEqual()
    {
        Topic::validatesNumericalityOf('approved', ['greater_than_or_equal_to' => 10]);

        $this->invalid([-9, 9], 'greater_than_or_equal_to');
        $this->valid([10]);
    }

    public function testNumericalityWithEqualTo()
    {
        Topic::validatesNumericalityOf('approved', ['equal_to' => 10]);

        $this->invalid([-10, 11], 'equal_to');
        $this->valid([10]);
    }




    private function valid(array $values)
    {
        foreach ($this->with_each_topic_approved_value($values) as $topic => $value) {
            $this->assertTrue($topic->isValid(), var_export($value, true).' not accepted as a number');
        }
    }

    private function invalid(array $values, $error = null)
    {
        foreach ($this->with_each_topic_approved_value($values) as $topic => $value) {
            $this->assertTrue($topic->isInvalid(), var_export($value, true).' not rejected as a number');
            $this->assertNotEmpty($topic->getErrors()['approved'], 'FAILED for '.var_export($value, true));
            if ($error) {
                $this->assertEquals($error, reset($topic->getErrors()['approved']));
            }
        }
    }

    private function with_each_topic_approved_value(array $values)
    {
        $topic = new Topic;
        $topic->title = 'Numeric test';
        $topic->content = 'whatever';

        foreach ($values as $value) {
            $topic->approved = $value;
            yield $topic => $value;
        }
    }

}
