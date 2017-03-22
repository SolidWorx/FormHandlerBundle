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

namespace SolidWorx\FormHandler\Event\Listener;

use SolidWorx\FormHandler\FormRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FormHandlerResponseListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => 'onKernelView',
        ];
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     *
     * @throws \Exception
     */
    public function onKernelView(GetResponseForControllerResultEvent $event): void
    {
        $result = $event->getControllerResult();

        if (!$result instanceof FormRequest) {
            return;
        }

        $event->setResponse($result->getResponse());
    }
}
