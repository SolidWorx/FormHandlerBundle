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

namespace SolidWorx\FormHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var mixed
     */
    private $response;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var array
     */
    private $options;

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param Response      $response
     * @param Options       $options
     */
    public function __construct(FormInterface $form = null, Request $request = null, Response $response = null, Options $options = null)
    {
        $this->form = $form;
        $this->request = $request;
        $this->response = $response ?: new Response();
        $this->options = $options;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form
     */
    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    /**
     * @return Options
     */
    public function getOptions(): Options
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function addOptions(array $options): void
    {
        $this->options = Options::fromArray($options)->merge($this->options);
    }
}
