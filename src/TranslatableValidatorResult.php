<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

class TranslatableValidatorResult implements Result
{
    use ValidatorResultDecorator;
    use ValidatorResultMessageInterpolator;

    /** @var Result */
    private $result;

    /** @var string Translation text domain */
    private $textDomain;

    /** @var Translator\TranslatorInterface */
    private $translator;

    public function __construct(
        Result $result,
        Translator\TranslatorInterface $translator,
        string $textDomain = null
    ) {
        $this->result     = $result;
        $this->translator = $translator;
        $this->textDomain = $textDomain;
    }

    /**
     * Returns translated error message strings from the decorated result instance.
     *
     * Loops through each message template from the composed Result and returns
     * translated messages. Each message will have interpolated the composed
     * message variables from the result.
     *
     * Additionally, if a `%value%` placeholder is found, the Result value will
     * be interpolated.
     */
    public function getMessages() : array
    {
        return $this->result instanceof ResultAggregate
            ? $this->getMessagesForResultAggregate($this->result)
            : $this->getMessagesForResult($this->result);
    }

    private function getMessagesForResult(Result $result) : array
    {
        return array_reduce(
            $result->getMessageTemplates(),
            function (array $messages, string $template) use ($result) {
                array_push($messages, $this->interpolateMessageVariables(
                    $this->translator->translate($template, $this->textDomain),
                    $result
                ));
            },
            []
        );
    }

    private function getMessagesForResultAggregate(ResultAggregate $aggregate) : array
    {
        $messages = [];
        foreach ($aggregate as $result) {
            array_merge($messages, $this->getMessagesForResult($result));
        }
        return $messages;
    }
}
