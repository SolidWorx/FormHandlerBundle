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

namespace SolidWorx\FormHandler\Tests\Event\Listener;

use PHPUnit\Framework\TestCase;
use SolidWorx\FormHandler\Event\FormHandlerEvent;
use SolidWorx\FormHandler\Event\Listener\FormFailListener;
use SolidWorx\FormHandler\FormRequest;
use SolidWorx\FormHandler\Tests\Fixtures\TestFormHandlerWithFailHandlerInterface;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class FormFailListenerTest extends TestCase
{
    public function testOnFormFailWithNoFailHandler()
    {
        $event = new FormHandlerEvent();

        $listener = new FormFailListener();

        $listener->onFormFail($event);

        $this->assertNull($event->getResponse());
    }

    public function testOnFormFailWithFailHandler()
    {
        $form = $this->createMock(FormInterface::class);
        $event = new FormHandlerEvent(new TestFormHandlerWithFailHandlerInterface(), $form, new FormRequest());

        $error = $this->createMock(FormErrorIterator::class);

        $form->method('getErrors')
            ->willReturn($error);

        $listener = new FormFailListener($error);

        $listener->onFormFail($event);

        $this->assertInstanceOf(Response::class, $event->getResponse());
    }
}
