<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

use Countable;
use IteratorAggregate;

/**
 * Represent aggregates of several results; e.g., for use with chains.
 */
interface ResultAggregate extends Countable, IteratorAggregate, Result
{
    public function push(Result $result) : void;
}
