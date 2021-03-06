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

use SolidWorx\FormHandler\FormHandlerInterface;

class NonUniqueHandlerException extends \LogicException
{
    public function __construct(FormHandlerInterface $handlerClass)
    {
        parent::__construct(sprintf('Handler class "%s" is already registered', get_class($handlerClass)));
    }
}
