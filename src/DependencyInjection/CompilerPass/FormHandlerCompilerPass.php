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

use SolidWorx\FormHandler\Decorator\FormCollectionDecorator;
use SolidWorx\FormHandler\FormCollectionHandlerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
            $this->decorate($container, $serviceId);
            $formHandlerDefinition->addMethodCall('registerHandler', [new Reference($serviceId)]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $serviceId
     *
     * @return string
     *
     * @throws \Exception
     */
    private function decorate(ContainerBuilder $container, string $serviceId)
    {
        $handler = $container->getDefinition($serviceId);

        if (is_a($handler->getClass(), FormCollectionHandlerInterface::class, true)) {
            if (!$container->hasDefinition('doctrine')) {
                $message = sprintf('You must install the doctrine/doctrine-bundle package in order to use the "FormCollectionHandlerInterface" interface on class "%s"', $handler->getClass());

                if ($serviceId !== $handler->getClass()) {
                    $message .= sprintf(' for service "%s"', $serviceId);
                }

                throw new \Exception($message);
            }

            $decoratorId = $serviceId.'.decorator.'.bin2hex(random_bytes(4));
            $decorator = new Definition(FormCollectionDecorator::class, [new Reference("$decoratorId.inner"), new Reference('doctrine')]);
            $decorator->setDecoratedService($serviceId);
            $container->setDefinition($decoratorId, $decorator);
        }
    }
}

