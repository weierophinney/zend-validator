<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

abstract class AbstractValidator implements Validator
{
    /**
     * Array of validation failure message templates. Should be an array of
     * key value pairs, to allow both lookup of templates by key, as well as
     * overriding the message template string.
     *
     * @var string[]
     */
    protected $messageTemplates = [];

    /**
     * Array of variable subsitutions to make in message templates. Typically,
     * these will be validator constraint values. The message templates will
     * refer to them as `%name%`.
     *
     * @var array
     */
    protected $messageVariables = [];

    /**
     * Create and return a result indicating validation failure.
     *
     * Use this within validators to create the validation result when a failure
     * condition occurs. Pass it the value, and an array of message keys.
     */
    protected function createInvalidResult($value, array $messageKeys) : Result
    {
        $messageTemplates = array_map(function ($key) {
            return $this->getMessageTemplate($key);
        }, $messageKeys);

        return ValidatorResult::createInvalidResult(
            $value,
            $messageTemplates,
            $this->messageVariables
        );
    }

    /**
     * Returns an array of variable names used in constructing validation failure messages.
     *
     * @return string[]
     */
    public function getMessageVariables() : array
    {
        return array_keys($this->messageVariables);
    }

    /**
     * Returns the message templates from the validator
     *
     * @return string[]
     */
    public function getMessageTemplates() : array
    {
        return $this->messageTemplates;
    }

    /**
     * Sets the validation failure message template for a particular key
     */
    public function setMessageTemplate(string $messageKey, string $messageString) : void
    {
        $this->messageTemplates[$messageKey] = $messageString;
    }

    /**
     * Finds and returns the message template associated with the given message key.
     */
    protected function getMessageTemplate(string $messageKey) : string
    {
        return $this->messageTemplates[$messageKey] ?? '';
    }
}
