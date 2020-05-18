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

namespace SolidWorx\FormHandler\Tests;

use PHPUnit\Framework\TestCase;
use SolidWorx\FormHandler\Event\FormHandlerEvents;
use SolidWorx\FormHandler\FormHandler;
use SolidWorx\FormHandler\FormRequest;
use SolidWorx\FormHandler\Tests\Fixtures\TestFormHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FormHandlerTest extends TestCase
{
    public function testRegisterFormHandler()
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $formHandler = new FormHandler($requestStack, $this->createMock(EventDispatcherInterface::class), $this->createMock(FormFactoryInterface::class));
        $testHandler = new TestFormHandler();
        $formHandler->registerHandler($testHandler);

        $this->assertSame([TestFormHandler::class => $testHandler], $formHandler->getHandlers());
    }

    public function testHandlerFormNotSubmitted()
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $factory = $this->createMock(FormFactoryInterface::class);
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false);

        $factory->expects($this->once())
            ->method('create')
            ->with(TextType::class)
            ->will($this->returnValue($form));

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->never())
            ->method('dispatch');

        $formHandler = new FormHandler($requestStack, $dispatcher, $factory);
        $formHandler->registerHandler(new TestFormHandler());

        $formRequest = $formHandler->handle(TestFormHandler::class);

        $this->assertInstanceOf(FormRequest::class, $formRequest);
        $this->assertSame($form, $formRequest->getForm());
    }

    public function testHandlerFormSuccess()
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $factory = $this->createMock(FormFactoryInterface::class);
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $factory->expects($this->once())
            ->method('create')
            ->with(TextType::class)
            ->will($this->returnValue($form));

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->anything(), FormHandlerEvents::EVENT_FORM_SUCCESS);

        $formHandler = new FormHandler($requestStack, $dispatcher, $factory);
        $formHandler->registerHandler(new TestFormHandler());

        $formRequest = $formHandler->handle(TestFormHandler::class);

        $this->assertInstanceOf(FormRequest::class, $formRequest);
        $this->assertSame($form, $formRequest->getForm());
    }

    public function testHandlerFormFail()
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $factory = $this->createMock(FormFactoryInterface::class);
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $factory->expects($this->once())
            ->method('create')
            ->with(TextType::class)
            ->will($this->returnValue($form));

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->anything(), FormHandlerEvents::EVENT_FORM_FAIL);

        $formHandler = new FormHandler($requestStack, $dispatcher, $factory);
        $formHandler->registerHandler(new TestFormHandler());

        $formRequest = $formHandler->handle(TestFormHandler::class);

        $this->assertInstanceOf(FormRequest::class, $formRequest);
        $this->assertSame($form, $formRequest->getForm());
    }
}
