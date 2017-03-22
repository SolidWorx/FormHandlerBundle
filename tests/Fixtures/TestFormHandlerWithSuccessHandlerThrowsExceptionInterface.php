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

namespace SolidWorx\FormHandler\Tests\Fixtures;

use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormHandlerSuccessInterface;
use SolidWorx\FormHandler\FormRequest;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;

class TestFormHandlerWithSuccessHandlerThrowsExceptionInterface implements FormHandlerInterface, FormHandlerSuccessInterface
{
    public function getForm(FormFactoryInterface $factory = null, ...$options)
    {
        /* noop */
    }

    public function onSuccess($data, FormRequest $form): ?Response
    {
        throw new \Exception('Invalid call');
    }
}
