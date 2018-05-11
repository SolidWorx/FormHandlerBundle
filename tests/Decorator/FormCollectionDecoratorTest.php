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

namespace SolidWorx\FormHandler\Tests\Decorator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use SolidWorx\FormHandler\Decorator\FormCollectionDecorator;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormHandlerOptionsResolver;
use SolidWorx\FormHandler\FormRequest;
use SolidWorx\FormHandler\Options;
use SolidWorx\FormHandler\Test\FormHandlerTestCase;
use SolidWorx\FormHandler\Tests\Fixtures\Form\TestForm;
use SolidWorx\FormHandler\Tests\Fixtures\Model\ChildClass;
use SolidWorx\FormHandler\Tests\Fixtures\Model\TestClass;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormCollectionDecoratorTest extends FormHandlerTestCase
{
    protected function getHandlerOptions(): array
    {
        return [
            'entity' => new TestClass(),
        ];
    }

    public function getHandler()
    {
        $handlerMock = new class() implements FormHandlerInterface, FormHandlerOptionsResolver {
            public function getForm(FormFactoryInterface $factory, Options $options)
            {
                $class = new TestClass();
                $class->child = [new ChildClass('value3')];

                return $factory->create(TestForm::class, $class);
            }

            public function configureOptions(OptionsResolver $resolver): void
            {
                $resolver->setDefined('entity');
            }
        };

        $metadata = $this->getMockBuilder(ClassMetadata::class)->getMock();
        $objectManager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $registry = $this->getMockBuilder(ManagerRegistry::class)->getMock();

        $registry->expects($this->atLeastOnce())
            ->method('getManagerForClass')
            ->with(TestClass::class)
            ->willReturn($objectManager);

        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->with(TestClass::class)
            ->willReturn($metadata);

        return new FormCollectionDecorator(
            $handlerMock,
            $registry
        );
    }

    protected function assertOnSuccess(?Response $response, $data, FormRequest $form)
    {
        $result = new TestClass(
            new ArrayCollection([
                new ChildClass('value1'),
                new ChildClass('value2'),
            ])
        );

        $this->assertNull($response);
        $this->assertEquals($result, $data);
    }

    public function getFormData(): array
    {
        return [
            'test_form' => [
                'child' => [
                    [
                        'value' => 'value1',
                    ],
                    [
                        'value' => 'value2',
                    ],
                ],
            ],
        ];
    }
}
