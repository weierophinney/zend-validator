<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

class ObscuredValueValidatorResult implements Result
{
    use ValidatorResultDecorator;
    use ValidatorResultMessageInterpolator;

    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Override `getMessages()` to ensure value is obscured.
     *
     * Recreates the logic of ValidatorResult::getMessages in order to ensure
     * that the decorator's getValue() is called when substituting the value
     * into message templates.
     */
    public function getMessages() : array
    {
        $messages = [];
        foreach ($this->result->getMessageTemplates() as $template) {
            $messages[] = $this->interpolateMessageVariables($template, $this);
        }
        return $messages;
    }

    /**
     * Returns an obscured version of the value.
     *
     * Casts the value to a string, and then replaces all characters with '*'.
     *
     * @return string
     */
    public function getValue()
    {
        $value = $this->castValueToString($this->result->getValue());
        return str_repeat('*', strlen($value));
    }
}
