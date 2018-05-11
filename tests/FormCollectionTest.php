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
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use SolidWorx\FormHandler\FormCollection;

class FormCollectionTest extends TestCase
{
    public function testGetEntityCollectionsWithEmptyObject()
    {
        $this->assertSame([], (new FormCollection($this->getMockBuilder(ManagerRegistry::class)->getMock()))->getEntityCollections(null));
    }

    public function testGetEntityCollections()
    {
        $results = [
            [
                'class'      => 'SolidWorx\FormHandler\Tests\Foo',
                'short_name' => 'Foo',
                'id'         => 4,
                'method'     => 'getBars',
                'collection' => [
                    new Bar(12),
                    new Bar(13),
                ],
            ],
            [
                'class'      => 'SolidWorx\FormHandler\Tests\Bar',
                'short_name' => 'Bar',
                'id'         => 12,
                'method'     => 'getBaz',
                'collection' => [
                    new Baz(),
                    new Baz(),
                ],
            ],
            [
                'class'      => 'SolidWorx\FormHandler\Tests\Bar',
                'short_name' => 'Bar',
                'id'         => 13,
                'method'     => 'getBaz',
                'collection' => [
                    new Baz(),
                    new Baz(),
                ],
            ],
        ];

        $foo = new Foo(4);

        $metadata = $this->getMockBuilder(ClassMetadata::class)->getMock();
        $objectManager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $registry = $this->getMockBuilder(ManagerRegistry::class)->getMock();

        $registry->expects($this->any())
            ->method('getManagerForClass')
            //->with(Foo::class)
            ->willReturn($objectManager);

        $objectManager->expects($this->any())
            ->method('getClassMetadata')
            //->with(Foo::class)
            ->willReturn($metadata);

        $metadata->expects($this->any())
            ->method('getIdentifier')
            //->with(Foo::class)
            ->willReturn(['id']);

        $metadata->expects($this->at(0))
            ->method('getIdentifierValues')
            ->with($foo)
            ->willReturn(['id' => 4]);

        $metadata->expects($this->at(2))
            ->method('getIdentifierValues')
            ->willReturn(['id' => 12]);

        $metadata->expects($this->at(4))
            ->method('getIdentifierValues')
            ->willReturn(['id' => 13]);

        $this->assertEquals($results, (new FormCollection($registry))->getEntityCollections($foo));
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
