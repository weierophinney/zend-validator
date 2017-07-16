<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

trait ValidatorResultMessageInterpolator
{
    private function interpolateMessageVariables(string $message, ValidatorResult $result) : string
    {
        $messageVariables = array_merge($result->getMessageVariables(), ['value' => $result->getValue()]);
        foreach ($messageVariables as $variable => $substitution) {
            $message = $this->interpolateMessageVariable($message, $variable, $substitution);
        }
        return $message;
    }

    /**
     * @param mixed $substitution
     */
    private function interpolateMessageVariable(string $message, string $variable, $substitution) : string
    {
        return str_replace("%$variable%", $this->castValueToString($substitution), $message);
    }

    /**
     * @param mixed $value
     */
    private function castValueToString($value) : string
    {
        if (is_object($value)) {
            $value = method_exists($value, '__toString')
                ? (string) $value
                : sprintf('%s object', get_class($value));
        }

        $value = is_array($value)
            ? sprintf('[%s]', implode(', ', $value))
            : $value;

        return (string) $value;
    }
}
