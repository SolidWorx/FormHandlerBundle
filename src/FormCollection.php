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

class FormCollection
{
    /**
     * @param object $entity
     * @param array  $data
     *
     * @return array
     */
    public static function getEntityCollections($entity, array &$data = []): array
    {
        if (!is_object($entity)) {
            return $data;
        }

        $ref = new \ReflectionObject($entity);

        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic() || !$method->hasReturnType() || 0 < $method->getNumberOfParameters() || Collection::class !== (string) $method->getReturnType()) {
                continue;
            }

            $collection = $method->invoke($entity)->toArray();

            if (0 === count($collection)) {
                continue;
            }

            $data[] = [
                'class' => $ref->getName(),
                'short_name' => $ref->getShortName(),
                'id' => method_exists($entity, 'getId') ? $entity->getId() : null, // @TODO: We should get the default identifier method instead of hard-coding getId
                'method' => $method->getShortName(),
                'collection' => $collection,
            ];

            foreach ($collection as $item) {
                self::getEntityCollections($item, $data);
            }
        }

        return $data;
    }
}
