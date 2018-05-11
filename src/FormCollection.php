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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * @internal
 */
class FormCollection
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param object $entity
     * @param array  $data
     *
     * @return array
     */
    public function getEntityCollections($entity, array &$data = []): array
    {
        if (!is_object($entity)) {
            return $data;
        }

        $ref = new \ReflectionObject($entity);

        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic() || !$method->hasReturnType() || 0 < $method->getNumberOfParameters()) {
                continue;
            }

            if (Collection::class !== (string) $method->getReturnType() || !($collection = $method->invoke($entity)) instanceof Collection) {
                continue;
            }

            if (0 === count($collection)) {
                continue;
            }

            $data[] = [
                'class'      => $ref->getName(),
                'short_name' => $ref->getShortName(),
                'id'         => $this->getEntityIdentifierValue($entity),
                'method'     => $method->getShortName(),
                'collection' => $collection->toArray(),
            ];

            foreach ($collection as $item) {
                $this->getEntityCollections($item, $data);
            }
        }

        return $data;
    }

    public function getEntityIdentifier($entity): ?string
    {
        return $this->getEntityMetadata($entity)->getIdentifier()[0] ?? null;
    }

    public function getEntityIdentifierValue($entity)
    {
        $identifierValue = $this->getEntityMetadata($entity)->getIdentifierValues($entity);

        if (empty($identifierValue)) {
            return;
        }

        return $identifierValue[$this->getEntityIdentifier($entity)] ?? null;
    }

    private function getEntityMetadata($entity): ClassMetadata
    {
        if (!is_object($entity)) {
            throw new \Exception(sprintf('%s expects the first parameter to be an instance of an object, %s given', __METHOD__, gettype($entity)));
        }

        $class = get_class($entity);

        $objectManager = $this->registry->getManagerForClass($class);

        if (null === $objectManager) {
            throw new \Exception(sprintf('%s is not a managed Doctrine entity', $class));
        }

        return $objectManager->getClassMetadata($class);
    }
}
