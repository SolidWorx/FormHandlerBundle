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

namespace SolidWorx\FormHandler\Test;

use PHPUnit\Framework\TestCase;
use SolidWorx\FormHandler\Event\FormHandlerEvent;
use SolidWorx\FormHandler\Event\FormHandlerEvents;
use SolidWorx\FormHandler\FormHandler;
use SolidWorx\FormHandler\FormHandlerFailInterface;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormHandlerSuccessInterface;
use SolidWorx\FormHandler\FormRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\Extension\HttpFoundation\Type\FormTypeHttpFoundationExtension;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

abstract class FormHandlerTestCase extends TestCase
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    public function testForm(): void
    {
        $requestStack = new RequestStack();
        $request = Request::create('/', 'GET');
        $request->setSession(new Session(new MockArraySessionStorage(), new AttributeBag(), new FlashBag()));
        $requestStack->push($request);
        $dispatcher = new EventDispatcher();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions(
                array_merge(
                    $this->getTypeExtensions(),
                    [
                        $this->getRequestHandlerExtension($requestStack),
                    ]
                )
            )
            ->addTypes($this->getTypes())
            ->addTypeGuessers($this->getTypeGuessers())
            ->getFormFactory();

        $formHandler = new FormHandler($requestStack, $dispatcher, $this->factory);

        $handler = $this->getHandler();

        if (!$handler instanceof FormHandlerInterface) {
            throw new \Exception(get_class($this).'::getHandler() must return a string or instance of '.FormHandlerInterface::class);
        }

        if (is_object($handler)) {
            $formHandler->registerHandler($handler);
        }

        $response = $formHandler->handle($handler, $this->getHandlerOptions());

        $this->assertResponse($response);
    }

    public function testFormSubmit(): void
    {
        $requestStack = new RequestStack();
        $request = Request::create('/', 'POST', $this->getFormData());
        $request->setSession(new Session(new MockArraySessionStorage(), new AttributeBag(), new FlashBag()));
        $requestStack->push($request);
        $dispatcher = new EventDispatcher();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions(
                array_merge(
                    $this->getTypeExtensions(),
                    [
                        $this->getRequestHandlerExtension($requestStack),
                    ]
                )
            )
            ->addTypes($this->getTypes())
            ->addTypeGuessers($this->getTypeGuessers())
            ->getFormFactory();

        $formHandler = new FormHandler($requestStack, $dispatcher, $this->factory);

        $handler = $this->getHandler();

        if (!$handler instanceof FormHandlerInterface) {
            throw new \Exception(get_class($this).'::getHandler() must return a string or instance of '.FormHandlerInterface::class);
        }

        if (is_object($handler)) {
            $formHandler->registerHandler($handler);
        }

        if ($handler instanceof FormHandlerSuccessInterface) {
            $this->registerSuccessHandler($handler, $dispatcher);
        }

        if ($handler instanceof FormHandlerFailInterface) {
            $this->registerFailHandler($handler, $dispatcher);
        }

        $formHandler->handle($handler, $this->getHandlerOptions());
    }

    /**
     * @param FormHandlerSuccessInterface $handler
     *
     * @throws \Exception
     */
    private function registerSuccessHandler(FormHandlerSuccessInterface $handler, EventDispatcherInterface $dispatcher): void
    {
        if (!is_a($handler, FormHandlerSuccessInterface::class, true)) {
            return;
        }

        $dispatcher->addListener(FormHandlerEvents::EVENT_FORM_SUCCESS, function (FormHandlerEvent $event) use ($handler) {
            $form = $event->getForm();

            if (!$form instanceof FormInterface) {
                $this->fail(sprintf('Event %s does not return an instance of %s', FormHandlerEvents::EVENT_FORM_SUCCESS, FormInterface::class));
                return;
            }

            $data = [
                $event->getFormRequest(),
                $form->getData(),
            ];

            $this->beforeSuccess($event->getFormRequest(), $form->getData());
            $this->assertOnSuccess($handler->onSuccess(...$data), ...$data);
        });
    }

    /**
     * @param FormHandlerFailInterface $handler
     *
     * @throws \Exception
     */
    private function registerFailHandler(FormHandlerFailInterface $handler, EventDispatcherInterface $dispatcher): void
    {
        if (!is_a($handler, FormHandlerFailInterface::class, true)) {
            return;
        }

        $dispatcher->addListener(FormHandlerEvents::EVENT_FORM_SUCCESS, function (FormHandlerEvent $event) use ($handler) {
            $form = $event->getForm();

            if (!$form instanceof FormInterface) {
                $this->fail(sprintf('Event %s does not return an instance of %s', FormHandlerEvents::EVENT_FORM_SUCCESS, FormInterface::class));
                return;
            }

            $data = [
                $event->getFormRequest(),
                $form->getErrors(true, false),
                $form->getData(),
            ];

            $this->beforeFail(...$data);
            $this->assertOnFail($handler->onFail(...$data), ...$data);
        });
    }

    private function getRequestHandlerExtension(RequestStack $requestStack): FormTypeHttpFoundationExtension
    {
        return new FormTypeHttpFoundationExtension(new HttpFoundationRequestHandler(new ServerParams($requestStack)));
    }

    /**
     * @return FormHandlerInterface|FormHandlerSuccessInterface|mixed
     */
    abstract public function getHandler();

    /**
     * @return array
     */
    abstract public function getFormData(): array;

    /**
     * Return options that should be passed to the handler.
     */
    protected function getHandlerOptions(): array
    {
        return [];
    }

    /**
     * This method should be used to set up any mocks or objects that will be needed by the success handler.
     *
     * @param FormRequest $form
     * @param mixed       $data
     */
    protected function beforeSuccess(FormRequest $form, $data): void
    {
    }

    /**
     * This method should be used to set up any mocks or objects that will be needed by the fail handler.
     *
     * @param FormRequest       $formRequest
     * @param FormErrorIterator $errors
     * @param mixed             $data
     */
    protected function beforeFail(FormRequest $formRequest, FormErrorIterator $errors, $data): void
    {
    }

    /**
     * Get the response from the success handler and run any assertions needed.
     *
     * @param null|Response $response The result returned from the success handler
     * @param FormRequest   $form
     * @param mixed         $data
     */
    protected function assertOnSuccess(?Response $response, FormRequest $form, $data): void
    {
    }

    /**
     * Get the response from the fail handler and run any assertions needed.
     *
     * @param null|Response     $response The result returned from the fail handler
     * @param FormRequest       $formRequest
     * @param FormErrorIterator $errors
     * @param mixed             $data
     */
    protected function assertOnFail(?Response $response, FormRequest $formRequest, $data, FormErrorIterator $errors): void
    {
    }

    protected function assertResponse(FormRequest $formRequest): void
    {
    }

    /**
     * Register custom form extensions.
     */
    protected function getExtensions(): array
    {
        return [];
    }

    /**
     * Register custom form type extensions.
     */
    protected function getTypeExtensions(): array
    {
        return [];
    }

    /**
     * Register custom form types.
     */
    protected function getTypes(): array
    {
        return [];
    }

    /**
     * Register custom form type guessers.
     */
    protected function getTypeGuessers(): array
    {
        return [];
    }
}
