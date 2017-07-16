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
class ValidatorResult implements Result
{
    use ValidatorResultMessageInterpolator;

    /**
     * @var bool
     */
    private $isValid;

    /**
     * Message templates.
     *
     * Each may be a self-contained message, or contain placeholders of the form
     * "%name%" for variables to interpolate into the string.
     *
     * @var string[]
     */
    private $messageTemplates = [];

    /**
     * Map of message variable names to the values to interpolate.
     * @var string[]
     */
    private $messageVariables = [];

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     * @param bool $isValid
     * @param string[] $messageTemplates
     * @param string[] $messageVariables
     */
    public function __construct(
        $value,
        bool $isValid,
        array $messageTemplates = [],
        array $messageVariables = []
    ) {
        $this->value = $value;
        $this->isValid = $isValid;
        $this->messageTemplates = $messageTemplates;
        $this->messageVariables = $messageVariables;
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
     * @param string[] $messageTemplates
     * @param string[] $messageVariables
     */
    public static function createInvalidResult(
        $value,
        array $messageTemplates,
        array $messageVariables = []
    ) : self {
        return new self($value, false, $messageTemplates, $messageVariables);
    }

    public function isValid() : bool
    {
        return $this->isValid;
    }

    /**
     * Retrieve validation error messages.
     *
     * This method loops through each message template and interpolates any
     * message variables discovered in the string.
     *
     * If you are using i18n features, decorate this instance with a
     * TranslatableValidatorResult.
     *
     * If you wish to osbcure the value, decorate this instance with an
     * ObscuredValueValidatorResult.
     */
    public function getMessages() : array
    {
        $messages = [];
        foreach ($this->getMessageTemplates() as $template) {
            $messages[] = $this->interpolateMessageVariables($template, $this);
        }
        return $messages;
    }

    public function getMessageTemplates() : array
    {
        return $this->messages;
    }

    public function getMessageVariables() : array
    {
        return $this->messageVariables;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
