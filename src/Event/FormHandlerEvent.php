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

namespace SolidWorx\FormHandler\Event;

use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormRequest;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

class FormHandlerEvent extends Event
{
    /**
     * @var FormInterface|null
     */
    private $form;

    /**
     * @var FormHandlerInterface|null
     */
    private $handler;

    /**
     * @var mixed
     */
    private $response;

    /**
     * @var FormRequest
     */
    private $formRequest;

    public function __construct(?FormHandlerInterface $handler = null, ?FormInterface $form = null, ?FormRequest $formRequest = null)
    {
        $this->handler = $handler;
        $this->form = $form;
        $this->formRequest = $formRequest ?: new FormRequest();
    }

    /**
     * @return FormInterface
     */
    public function getForm(): ?FormInterface
    {
        return $this->form;
    }

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    public function getHandler(): ?FormHandlerInterface
    {
        return $this->handler;
    }

    public function setHandler(FormHandlerInterface $handler): void
    {
        $this->handler = $handler;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getFormRequest(): FormRequest
    {
        return $this->formRequest;
    }

    public function setFormRequest(FormRequest $formRequest): void
    {
        $this->formRequest = $formRequest;
    }
}
