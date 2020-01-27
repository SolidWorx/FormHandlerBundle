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

namespace SolidWorx\FormHandler\Tests\Fixtures;

use SolidWorx\FormHandler\FormHandlerFailInterface;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormRequest;
use SolidWorx\FormHandler\Options;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;

class TestFormHandlerWithFailHandlerInterface implements FormHandlerInterface, FormHandlerFailInterface
{
    public function getForm(FormFactoryInterface $factory, Options $options)
    {
        /* noop */
    }

    public function onFail(FormRequest $formRequest, FormErrorIterator $errors, $data = null): ?Response
    {
        return new Response();
    }
}
