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

namespace SolidWorx\FormHandler\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FormHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('solidworx.form_handler')) {
            return;
        }

        $formHandlerDefinition = $container->getDefinition('solidworx.form_handler');
        $serviceIds = $container->findTaggedServiceIds('form.handler');

        foreach (array_keys($serviceIds) as $serviceId) {
            $formHandlerDefinition->addMethodCall('registerHandler', [new Reference($serviceId)]);
        }
    }
}
