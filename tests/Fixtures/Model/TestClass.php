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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class TestClass
{
    public $child;

    public function getId()
    {
        return 1;
    }

    public function getChild(): Collection
    {
        return is_array($this->child) ? new ArrayCollection($this->child) : $this->child;
    }

    public function __construct($child = null)
    {
        $this->child = $child;
    }
}