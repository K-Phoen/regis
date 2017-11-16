<?php

declare(strict_types=1);

namespace Tests\Regis\Helper;

trait ObjectManipulationHelper
{
    private function setPrivateValue($object, string $property, $value)
    {
        $reflectionClass = new \ReflectionClass($object);
        $property = $reflectionClass->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
