<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

/**
 * Value object representing results of validation.
 */
class ValidatorResult
{
    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var string[]
     */
    private $messages = [];

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     * @param bool $isValid
     * @param string[] $messages
     */
    public function __construct($value, bool $isValid, array $messages = [])
    {
        $this->value = $value;
        $this->isValid = $isValid;
        $this->messages = $messages;
    }

    /**
     * @param mixed $value
     */
    public static function createValidResult($value) : self
    {
        return new self($value, true);
    }

    /**
     * @param mixed $value
     * @param string[] $messages
     */
    public static function createInvalidResult($value, array $messages) : self
    {
        return new self($value, false, $messages);
    }

    public function isValid() : bool
    {
        return $this->isValid;
    }

    public function getMessages() : array
    {
        return $this->messages;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
