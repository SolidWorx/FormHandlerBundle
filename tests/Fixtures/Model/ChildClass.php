<?php

declare(strict_types=1);

/*
 * This file is part of the FormHandler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\FormHandler\Tests\Fixtures\Model;

class ChildClass
{
    public $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getId()
    {
        return $this->value;
    }
}
