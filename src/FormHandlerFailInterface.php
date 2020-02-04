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

use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Response;

interface FormHandlerFailInterface
{
    /**
     * @param FormRequest       $formRequest
     * @param FormErrorIterator $errors
     * @param null|mixed        $data
     *
     * @return Response|null
     */
    public function onFail(FormRequest $formRequest, FormErrorIterator $errors, $data = null): ?Response;
}
