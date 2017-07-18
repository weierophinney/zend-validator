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
        return $this->result instanceof ResultAggregate
            ? $this->getMessagesForResultAggregate($this->result, $this->getValue())
            : $this->getMessagesForResult($this->result, $this->getValue());
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

    private function getMessagesForResult(Result $result, string $value) : array
    {
        return array_reduce(
            $result->getMessageTemplates(),
            function (array $messages, string $template) use ($result, $value) {
                array_push(
                    $messages,
                    $this->interpolateMessageVariablesWithValue($template, $result, $value)
                );
                return $messages;
            },
            []
        );
    }

    private function getMessagesForResultAggregate(ResultAggregate $aggregate, string $value) : array
    {
        $messages = [];
        foreach ($aggregate as $result) {
            array_merge($messages, $this->getMessagesForResult($result, $value));
        }
        return $messages;
    }

    /**
     * Ensure that the value is obscured when interpolating messages for an aggregate.
     */
    private function interpolateMessageVariablesWithValue(string $message, Result $result, string $value) : string
    {
        $messageVariables = array_merge($result->getMessageVariables(), ['value' => $value]);
        foreach ($messageVariables as $variable => $substitution) {
            $message = $this->interpolateMessageVariable($message, $variable, $substitution);
        }
        return $message;
    }
}
