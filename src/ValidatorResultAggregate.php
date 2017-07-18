<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

class ValidatorResultAggregate implements ResultAggregate
{
    /** @var Result[] */
    private $results = [];

    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function push(Result $result) : void
    {
        $this->results[] = $result;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->results);
    }

    /**
     * @return iterable
     */
    public function getIterator()
    {
        foreach ($this->results as $result) {
            yield $result;
        }
    }

    public function isValid() : bool
    {
        return array_reduce($this->results, function (bool $isValid, Result $result) {
            return $isValid && $result->isValid();
        }, true);
    }

    /**
     * Returns a shallow list of all messages, with variables interpolated.
     */
    public function getMessages() : array
    {
        return array_reduce($this->results, function (array $messages, Result $result) {
            return array_merge($messages, $result->getMessages());
        }, []);
    }

    /**
     * Returns a list with message templates from each validator.
     *
     * Instead of a shallow list, this contains an array of arrays, with the
     * second level being the full list of templates for a single validator.
     */
    public function getMessageTemplates() : array
    {
        return array_reduce($this->results, function (array $templates, Result $result) {
            $templates[] = $result->getMessageTemplates();
            return $templates;
        }, []);
    }

    /**
     * Returns a list with message variables from each validator.
     *
     * Instead of a shallow list, this contains an array of arrays, with the
     * second level being the full map of variables for a single validator.
     */
    public function getMessageVariables() : array
    {
        return array_reduce($this->results, function (array $variables, Result $result) {
            $variables[] = $result->getMessageVariables();
            return $variables;
        }, []);
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}
