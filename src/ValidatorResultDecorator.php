<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

trait ValidatorResultDecorator
{
    /** @var Result */
    private $result;

    /**
     * Proxies to decorated Result instance.
     */
    public function isValid() : bool
    {
        return $this->result->isValid();
    }

    /**
     * Proxies to decorated Result instance.
     */
    public function getMessageTemplates() : array
    {
        return $this->result->getMessageTemplates();
    }

    /**
     * Proxies to decorated Result instance.
     */
    public function getMessageVariables() : array
    {
        return $this->result->getMessageVariables();
    }

    /**
     * Proxies to decorated Result instance.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->result->getValue();
    }
}
