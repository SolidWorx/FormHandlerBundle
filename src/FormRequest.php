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

namespace SolidWorx\FormHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormRequest
{
    /**
     * @var null|Request
     */
    private $request;

    /**
     * @var null|mixed
     */
    private $response;

    /**
     * @var null|FormInterface<FormInterface>
     */
    private $form;

    /**
     * @var Options
     */
    private $options;

    /**
     * @param null|FormInterface<FormInterface> $form
     * @param null|Request                      $request
     * @param null|Response                     $response
     * @param null|Options                      $options
     */
    public function __construct(FormInterface $form = null, Request $request = null, Response $response = null, Options $options = null)
    {
        $this->form = $form;
        $this->request = $request;
        $this->response = $response ?: new Response();
        $this->options = $options ?: Options::fromArray([]);
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return mixed|Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed|Response|null $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @return FormInterface<FormInterface>|null
     */
    public function getForm(): ?FormInterface
    {
        return $this->form;
    }

    /**
     * @param FormInterface<FormInterface> $form
     */
    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function addOptions(array $options): void
    {
        $this->options = Options::fromArray($options)->merge($this->options->toArray());
    }
}
