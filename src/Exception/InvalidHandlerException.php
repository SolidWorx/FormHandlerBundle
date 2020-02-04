<?php

declare(strict_types=1);

/**
 * This file is part of the FormHandler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\FormHandler\Exception;

class InvalidHandlerException extends \LogicException
{
    /**
     * @param mixed $actual
     */
    public function __construct(string $expected, $actual)
    {
        parent::__construct(sprintf('Handler is expected to be of type "%s", "%s" given', $expected, is_object($actual) ? get_class($actual) : gettype($actual)));
    }
}
