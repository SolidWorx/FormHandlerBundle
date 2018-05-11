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

use Symfony\Component\HttpFoundation\Response;

interface FormHandlerSuccessInterface
{
    /**
     * Handle successful form submits.
     *
     * @param mixed       $data The data that is returned from the form
     * @param FormRequest $form
     *
     * @return null|Response
     */
    public function onSuccess($data, FormRequest $form): ?Response;
}
