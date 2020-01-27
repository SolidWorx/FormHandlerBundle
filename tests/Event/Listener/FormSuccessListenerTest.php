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

namespace SolidWorx\FormHandler\Tests\Event\Listener;

use PHPUnit\Framework\TestCase;
use SolidWorx\FormHandler\Event\FormHandlerEvent;
use SolidWorx\FormHandler\Event\Listener\FormSuccessListener;
use SolidWorx\FormHandler\FormRequest;
use SolidWorx\FormHandler\Tests\Fixtures\TestFormHandlerWithSuccessHandlerInterface;
use SolidWorx\FormHandler\Tests\Fixtures\TestFormHandlerWithSuccessHandlerThrowsExceptionInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class FormSuccessListenerTest extends TestCase
{
    public function testOnFormSuccessWithNoSuccessHandler()
    {
        $event = new FormHandlerEvent();

        $listener = new FormSuccessListener($this->createMock(Session::class));

        $listener->onFormSuccess($event);

        $this->assertNull($event->getResponse());
    }

    public function testOnFormSuccessWithSuccessHandlerSucceed()
    {
        $form = $this->createMock(FormInterface::class);

        $event = new FormHandlerEvent(new TestFormHandlerWithSuccessHandlerInterface(), $form, new FormRequest());

        $listener = new FormSuccessListener($this->createMock(Session::class));

        $listener->onFormSuccess($event);

        $this->assertInstanceOf(Response::class, $event->getResponse());
    }

    public function testOnFormSuccessWithSuccessHandlerApiException()
    {
        $form = $this->createMock(FormInterface::class);

        $event = new FormHandlerEvent(new TestFormHandlerWithSuccessHandlerThrowsExceptionInterface(), $form, new FormRequest());

        $flashBag = $this->createMock(FlashBagInterface::class);
        $flashBag->expects($this->once())
            ->method('add')
            ->with('error', 'Invalid call');

        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag);

        $listener = new FormSuccessListener($session);

        $listener->onFormSuccess($event);

        $this->assertNull($event->getResponse());
    }
}
