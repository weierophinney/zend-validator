<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

class ValidatorResultTranslator
{
    use ValidatorResultMessageInterpolator;

    /** @var string Translation text domain */
    private $textDomain;

    /** @var Translator\TranslatorInterface */
    private $translator;

    public function __construct(Translator\TranslatorInterface $translator, string $textDomain = null)
    {
        $this->translator = $translator;
        $this->textDomain = $textDomain;
    }

    /**
     * Create translated messages from a ValidatorResult
     *
     * Loops through each message template from the ValidatorResult and returns
     * translated messages. Each message will have interpolated the composed
     * message variables from the result.
     *
     * Additionally, if a `%value%` placeholder is found, the ValidatorResult
     * value will be interpolated.
     */
    public function translateMessages(ValidatorResult $result) : array
    {
        $value    = $result->getValue();
        $messages = [];

        foreach ($result->getMessageTemplates() as $template) {
            $messages[] = $this->interpolateMessageVariables(
                $this->translator->translate($template, $this->textDomain),
                $result
            );
        }

        return $messages;
    }
}
