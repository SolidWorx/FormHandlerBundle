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

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

interface FormHandlerInterface
{
    /**
     * @param FormFactoryInterface $factory The factory can be used to instantiate a form
     * @param Options              $options Any custom data to be used in creating the form (This can be form options, api values etc)
     *
     * @return string|FormInterface<FormInterface>|mixed The form to use for this handler
     */
    public function getForm(FormFactoryInterface $factory, Options $options);
}
