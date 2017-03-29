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

namespace SolidWorx\FormHandler\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SolidWorx\FormHandler\FormCollection;
use PHPUnit\Framework\TestCase;

class FormCollectionTest extends TestCase
{
    public function testGetEntityCollectionsWithEmptyObject()
    {
        $this->assertSame([], FormCollection::getEntityCollections(null));
    }

    public function testGetEntityCollections()
    {
        $results = [
            [
                'class' => 'SolidWorx\FormHandler\Tests\Foo',
                'short_name' => 'Foo',
                'id' => 4,
                'method' => 'getBars',
                'collection' => [
                    new Bar(12),
                    new Bar(13),
                ]
            ],
            [
                'class' => 'SolidWorx\FormHandler\Tests\Bar',
                'short_name' => 'Bar',
                'id' => 12,
                'method' => 'getBaz',
                'collection' => [
                    new Baz(),
                    new Baz(),
                ]
            ],
            [
                'class' => 'SolidWorx\FormHandler\Tests\Bar',
                'short_name' => 'Bar',
                'id' => 13,
                'method' => 'getBaz',
                'collection' => [
                    new Baz(),
                    new Baz(),
                ]
            ]
        ];

        $this->assertEquals($results, FormCollection::getEntityCollections(new Foo(4)));
    }
}

class Foo
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getBars(): Collection
    {
        return new ArrayCollection([new Bar(12), new Bar(13)]);
    }

    public function getId(): int
    {
        return $this->id;
    }
}

class Bar
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getBaz(): Collection
    {
        return new ArrayCollection([new Baz(), new Baz()]);
    }

    public function getId(): int
    {
        return $this->id;
    }
}

class Baz
{

}
