<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Traversable;

class Bitwise extends AbstractValidator
{
    const OP_AND = 'and';
    const OP_XOR = 'xor';

    const NOT_AND        = 'notAnd';
    const NOT_AND_STRICT = 'notAndStrict';
    const NOT_XOR        = 'notXor';

    /**
     * @var integer
     */
    protected $control;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_AND        => "The input has no common bit set with '%control%'",
        self::NOT_AND_STRICT => "The input doesn't have the same bits set as '%control%'",
        self::NOT_XOR        => "The input has common bit set with '%control%'",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'control' => 'control',
    ];

    /**
     * @var integer
     */
    protected $operator;

    /**
     * @var boolean
     */
    protected $strict = false;

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'control'  => integer
     *   'operator' =>
     *   'strict'   => boolean
     *
     * @param array|Traversable $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (! is_array($options)) {
            $options = func_get_args();

            $temp['control'] = array_shift($options);

            if (! empty($options)) {
                $temp['operator'] = array_shift($options);
            }

            if (! empty($options)) {
                $temp['strict'] = array_shift($options);
            }

            $options = $temp;
        }

        parent::__construct($options);
    }

    /**
     * Returns the control parameter.
     *
     * @return integer
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Returns the operator parameter.
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Returns the strict parameter.
     *
     * @return boolean
     */
    public function getStrict()
    {
        return $this->strict;
    }

    /**
     * Validates successfully if and only if $value is between min and max
     * options, inclusively if inclusive option is true.
     *
     * @throws Exception\RuntimeException for unrecognized operators.
     */
    public function validate($value, array $context = []) : Result
    {
        switch ($this->operator) {
            case (self::OP_AND):
                return $this->validateAndOperation($value);
            case (self::OP_OR):
                return $this->validateOrOperation($value);
            default:
                throw Exception\RuntimeException(sprintf(
                    '%s instance has unrecognized operator "%s"; must be one of "%s" or "%s"',
                    get_class($this),
                    var_export($this->operator, true),
                    self::OP_AND,
                    self::OP_OR
                ));
        }
    }

    /**
     * Sets the control parameter.
     *
     * @param  integer $control
     * @return Bitwise
     */
    public function setControl($control)
    {
        $this->control = (int) $control;

        return $this;
    }

    /**
     * Sets the operator parameter.
     *
     * @param  string  $operator
     * @return Bitwise
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Sets the strict parameter.
     *
     * @param  boolean $strict
     * @return Bitwise
     */
    public function setStrict($strict)
    {
        $this->strict = (bool) $strict;

        return $this;
    }

    /**
     * @param mixed $value
     */
    private function validateAndOperation($value) : Result
    {
        if ($this->strict) {
            // All the bits set in value must be set in control
            $this->error(self::NOT_AND_STRICT);

            return ($this->control & $value) == $value
                ? ValidatorResult::createValidResult($value)
                : $this->createInvalidResult($value, [self::NOT_AND_STRICT]);
        }

        // At least one of the bits must be common between value and control
        return (bool) ($this->control & $value)
            ? ValidatorResult::createValidResult($value)
            : $this->createInvalidResult($value, [self::NOT_AND]);
    }

    /**
     * @param mixed $value
     */
    private function validateOrOperation($value) : Result
    {
        return ($this->control ^ $value) === ($this->control | $value)
            ? ValidatorResult::createValidResult($value)
            : $this->createInvalidResult($value, [self::NOT_XOR]);
    }
}
