<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Throwable;

class Callback extends AbstractValidator
{
    /**
     * Invalid callback
     */
    const INVALID_CALLBACK = 'callbackInvalid';

    /**
     * Invalid value
     */
    const INVALID_VALUE = 'callbackValue';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_VALUE    => "The input is not valid",
        self::INVALID_CALLBACK => "An exception has been raised within the callback",
    ];

    /**
     * Default options to set for the validator
     *
     * @var mixed
     */
    protected $options = [
        'callback'         => null,     // Callback in a call_user_func format, string || array
        'callbackOptions'  => [],  // Options for the callback
    ];

    /**
     * Constructor
     *
     * @param array|callable $options
     */
    public function __construct($options = null)
    {
        if (is_callable($options)) {
            $options = ['callback' => $options];
        }

        parent::__construct($options);
    }

    /**
     * Returns the set callback
     *
     * @return mixed
     */
    public function getCallback()
    {
        return $this->options['callback'];
    }

    /**
     * Sets the callback
     *
     * @param  string|array|callable $callback
     * @return Callback Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setCallback($callback)
    {
        if (! is_callable($callback)) {
            throw new Exception\InvalidArgumentException('Invalid callback given');
        }

        $this->options['callback'] = $callback;
        return $this;
    }

    /**
     * Returns the set options for the callback
     *
     * @return mixed
     */
    public function getCallbackOptions()
    {
        return $this->options['callbackOptions'];
    }

    /**
     * Sets options for the callback
     *
     * @param  mixed $options
     * @return Callback Provides a fluent interface
     */
    public function setCallbackOptions($options)
    {
        $this->options['callbackOptions'] = (array) $options;
        return $this;
    }

    /**
     * Returns true if and only if the set callback returns
     * for the provided $value
     *
     * @throws Exception\InvalidArgumentException if no callback present
     * @throws Exception\InvalidArgumentException if callback is not callable
     */
    public function isValid($value, $context = null) : Result
    {
        $options  = $this->getCallbackOptions();
        $callback = $this->getCallback();
        if (empty($callback)) {
            throw new Exception\InvalidArgumentException('No callback given');
        }
        if (! is_callable($callback)) {
            throw new Exception\InvalidArgumentException('Invalid callback given; not callable');
        }

        $args = [$value];
        if (empty($options) && ! empty($context)) {
            $args[] = $context;
        }
        if (! empty($options) && empty($context)) {
            $args = array_merge($args, $options);
        }
        if (! empty($options) && ! empty($context)) {
            $args[] = $context;
            $args   = array_merge($args, $options);
        }

        try {
            return (bool) $callback(...$args)
                ? ValidatorResult::createValidResult($value)
                : $this->createInvalidResult($value, [self::INVALID_VALUE]);
        } catch (Throwable $e) {
            return $this->createInvalidResult($value, [self::INVALID_CALLBACK]);
        }
    }
}
