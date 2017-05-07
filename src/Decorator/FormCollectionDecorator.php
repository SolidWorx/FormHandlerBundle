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

namespace SolidWorx\FormHandler\Decorator;

use Doctrine\Common\Persistence\ManagerRegistry;
use ProxyManager\Proxy\ProxyInterface;
use SolidWorx\FormHandler\FormCollection;
use SolidWorx\FormHandler\FormCollectionHandlerInterface;
use SolidWorx\FormHandler\FormHandlerFailInterface;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormHandlerResponseInterface;
use SolidWorx\FormHandler\FormHandlerSuccessInterface;
use SolidWorx\FormHandler\FormRequest;
use SolidWorx\Util\ArrayUtil;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class FormCollectionDecorator implements FormHandlerInterface, FormCollectionHandlerInterface, FormHandlerFailInterface, FormHandlerResponseInterface, FormHandlerSuccessInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $handler;

    /**
     * @var array
     */
    private $formData;

    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(FormHandlerInterface $handler, ManagerRegistry $registry)
    {
        $this->handler = $handler;
        $this->registry = $registry;
    }

    /**
     * @return FormHandlerInterface
     */
    public function getInnerHandler(): FormHandlerInterface
    {
        return $this->handler;
    }

    /**
     * @return string
     */
    public function getInnerHandlerClass(): string
    {
        if ($this->handler instanceof ProxyInterface) {
            return get_parent_class($this->handler);
        }

        return get_class($this->handler);
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(FormFactoryInterface $factory = null, ...$options)
    {
        $form = $this->handler->getForm($factory, ...$options);

        if ($form instanceof FormInterface) {
            $this->formData = FormCollection::getEntityCollections($form->getData());
        } else {
            $entity = array_reduce($options, function ($carry, $item) {
                if (is_object($carry) && method_exists($carry, 'getId')) {
                    return $carry;
                }

                if (is_object($item) && method_exists($item, 'getId')) {
                    return $item;
                }

                return null;
            });

            $this->formData = FormCollection::getEntityCollections($entity);
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function onFail(FormRequest $formRequest, FormErrorIterator $errors, $data = null): ?Response
    {
        if ($this->handler instanceof FormHandlerFailInterface) {
            return $this->handler->onFail($formRequest, $errors, $data);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(FormRequest $formRequest)
    {
        if ($this->handler instanceof FormHandlerResponseInterface) {
            return $this->handler->getResponse($formRequest);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onSuccess($data, FormRequest $form): ?Response
    {
        $this->resolveCollections($data);

        if ($this->handler instanceof FormHandlerSuccessInterface) {
            return $this->handler->onSuccess($data, $form);
        }

        return null;
    }

    private function resolveCollections($entity): void
    {
        if (!is_object($entity)) {
            return;
        }

        $em = $this->registry->getManager();

        foreach ($this->formData as $data) {
            if ($data['class'] !== get_class($entity) || $data['id'] !== $entity->getId()) {
                continue;
            }

            $values = $entity->{$data['method']}()->toArray();

            if (0 === count($values) && 0 !== count($data['collection'])) {
                $toDel = $data['collection'];
            } else {
                $toDel = array_filter($data['collection'], function ($object) use ($values) {
                    try {
                        return !in_array($object->getId(), ArrayUtil::column($values, 'id'));
                    } catch (\ErrorException $e) {
                        return false;
                    }
                });
            }

            array_walk($values, __METHOD__);

            array_walk($toDel, function ($e) use ($entity, $data, $em) {
                $removeMethod = 'remove'.$data['short_name'];

                if (method_exists($entity, $removeMethod)) {
                    $entity->$removeMethod($e);
                }

                $em->remove($e);
            });
        }
    }
}
