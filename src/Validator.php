<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

interface Validator
{
    /**
     * Validate a value.
     *
     * Returns a Result, containing the results of validation.
     *
     * @param  mixed $value
     * @param  array $context Optional; additional context for validation, such
     *     as other form values.
     * @return Result
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function validate($value, array $context = []) : Result;
}
