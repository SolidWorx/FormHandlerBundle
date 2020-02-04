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
use SolidWorx\FormHandler\FormHandlerResponseInterface;
use SolidWorx\FormHandler\FormHandlerSuccessInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class FormSuccessListener implements EventSubscriberInterface
{
    /**
     * @var Session<string>
     */
    private $session;

    /**
     * @param Session<string> $session
     */
    public function __construct(Session $session) // Don't type-hint against SessionInterface, as the interface doesn't have the getFlashBag method
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, array<mixed>|string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormHandlerEvents::EVENT_FORM_SUCCESS => ['onFormSuccess', 128],
        ];
    }

    public function onFormSuccess(FormHandlerEvent $event): void
    {
        $handler = $event->getHandler();

        if (!$handler instanceof FormHandlerSuccessInterface) {
            return;
        }

        try {
            $form = $event->getForm();
            if (!$form instanceof FormInterface) {
                return;
            }

            $response = $handler->onSuccess($form->getData(), $event->getFormRequest());

            if ($response instanceof Response) {
                $event->setResponse($response);
            }
        } catch (\Exception $e) {
            $this->session->getFlashBag()->add('error', $e->getMessage());

            if ($handler instanceof FormHandlerResponseInterface) {
                $event->setResponse($handler->getResponse($event->getFormRequest()));
            }
        }
    }
}
