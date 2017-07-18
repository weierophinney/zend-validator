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
use Zend\Stdlib\ArrayUtils;

class Between extends AbstractValidator
{
    const NOT_BETWEEN        = 'notBetween';
    const NOT_BETWEEN_STRICT = 'notBetweenStrict';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_BETWEEN        => "The input is not between '%min%' and '%max%', inclusively",
        self::NOT_BETWEEN_STRICT => "The input is not strictly between '%min%' and '%max%'"
    ];

    /**
     * @var bool
     */
    private $inclusive;

    /**
     * @var int|float
     */
    private $max;

    /**
     * @var int|float
     */
    private $min;

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'min' => scalar, minimum border
     *   'max' => scalar, maximum border
     *   'inclusive' => boolean, inclusive border values
     *
     * @param int|float $min
     * @param int|float $max
     * @throws Exception\InvalidArgumentException if $min is not numeric
     * @throws Exception\InvalidArgumentException if $max is not numeric
     */
    public function __construct($min = 0, $max = PHP_INT_MAX, bool $inclusive = true)
    {
        if (! is_numeric($min)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid value for "min"; must be numeric, received %s',
                is_object($min) ? get_class($min) : gettype($min)
            ));
        }
        if (! is_numeric($max)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid value for "max"; must be numeric, received %s',
                is_object($max) ? get_class($max) : gettype($max)
            ));
        }

        $this->min = $min;
        $this->max = $max;
        $this->inclusive = $inclusive;

        $this->messageVariables = [
            'min' => $min,
            'max' => $max,
        ];
    }

    /**
     * Returns the min option
     *
     * @return int|float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Returns the max option
     *
     * @return int|float
     */
    public function getMax()
    {
        return $this->max;
    }

    public function isInclusive() : bool
    {
        return $this->inclusive;
    }

    /**
     * Returns true if and only if $value is between min and max options, inclusively
     * if inclusive option is true.
     */
    public function validate($value, array $context = []) : Result
    {
        return $this->isInclusive()
            ? $this->validateInclusive($value, $context)
            : $this->validateExclusive($value, $context);
    }

    private function validateInclusive($value, array $context) : Result
    {
        if ($value < $this->getMin() || $value > $this->getMax()) {
            return $this->createInvalidResult($value, [self::NOT_BETWEEN]);
        }
        return ValidatorResult::createValidResult($value);
    }

    private function validateExclusive($value, array $context) : Result
    {
        if ($value <= $this->getMin() || $value >= $this->getMax()) {
            return $this->createInvalidResult($value, [self::NOT_BETWEEN_STRICT]);
        }
        return ValidatorResult::createValidResult($value);
    }
}
