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

interface FormHandlerResponseInterface
{
    /**
     * Return the default response for a form (E.G rendering a template).
     *
     * @param FormRequest $formRequest
     *
     * @return mixed
     */
    public function getResponse(FormRequest $formRequest);
}
