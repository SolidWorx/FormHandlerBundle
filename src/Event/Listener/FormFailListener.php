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

namespace SolidWorx\FormHandler\Event\Listener;

use SolidWorx\FormHandler\Event\FormHandlerEvent;
use SolidWorx\FormHandler\Event\FormHandlerEvents;
use SolidWorx\FormHandler\FormHandlerFailInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class FormFailListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormHandlerEvents::EVENT_FORM_FAIL => ['onFormFail', 128],
        ];
    }

    public function onFormFail(FormHandlerEvent $event): void
    {
        $handler = $event->getHandler();

        if (!$handler instanceof FormHandlerFailInterface) {
            return;
        }

        $form = $event->getForm();

        if (!$form instanceof FormInterface) {
            return;
        }

        $response = $handler->onFail($event->getFormRequest(), $form->getErrors(true, false), $form->getData());

        if ($response instanceof Response) {
            $event->setResponse($response);
        }
    }
}
