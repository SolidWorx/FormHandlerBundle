<?php
/**
 * This file is part of the FormHandlerBundle project.
 *
 * @author     pierre
 * @copyright  Copyright (c) 2017
 */

namespace SolidWorx\FormHandler;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface FormHandlerOptionsResolver
{
    /**
     * Configure defined, required and default options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void;
}
