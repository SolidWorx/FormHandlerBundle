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
use SolidWorx\FormHandler\Event\Listener\FormHandlerResponseListener;
use SolidWorx\FormHandler\FormRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FormHandlerResponseListenerTest extends TestCase
{
    public function testControllerNotReturningFormRequest()
    {
        $event = new ViewEvent($this->createMock(HttpKernelInterface::class), Request::createFromGlobals(), HttpKernelInterface::MASTER_REQUEST, new Response());

        $listener = new FormHandlerResponseListener();

        $listener->onKernelView($event);

        $this->assertNull($event->getResponse());
    }

    public function testControllerReturningFormRequest()
    {
        $event = new ViewEvent($this->createMock(HttpKernelInterface::class), Request::createFromGlobals(), HttpKernelInterface::MASTER_REQUEST, new FormRequest());

        $listener = new FormHandlerResponseListener();

        $listener->onKernelView($event);

        $this->assertInstanceOf(Response::class, $event->getResponse());
    }
}
