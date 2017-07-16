<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

interface Result
{
    public function isValid() : bool;

    public function getMessages() : array;

    public function getMessageTemplates() : array;

    public function getMessageVariables() : array;

    /**
     * @return mixed
     */
    public function getValue();
}
