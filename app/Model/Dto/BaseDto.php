<?php

namespace App\Model\Dto;

use ReflectionClass;
use ReflectionProperty;
use ReflectionType;
use InvalidArgumentException;

abstract class BaseDto
{
    public function fill(array $attrs): void
    {
        $reflectionClass = new ReflectionClass($this);

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();

            // 检查属性数组中是否存在当前属性
            if (array_key_exists($propertyName, $attrs)) {
                $this->setPropertyValue($property, $attrs[$propertyName]);
            }
        }
    }

    private function setPropertyValue(ReflectionProperty $property, mixed $value): void
    {
        $propertyType = $property->getType();

        if ($propertyType?->isBuiltin() ?? false) {
            $this->setBasicTypeValue($property, $value, $propertyType);
        } else {
            $this->setComplexTypeValue($property, $value, $propertyType);
        }
    }

    private function setComplexTypeValue(ReflectionProperty $property, mixed $value, ReflectionType $propertyType): void
    {
        $typeName = $propertyType->getName();

        if ($value === null && $propertyType->allowsNull()) {
            $property->setValue($this, null);
            return;
        }

        // 使用 is_a 函数检查对象类型
        if (is_a($value, $typeName, true)) {
            $property->setValue($this, $value);
            return;
        }

        throw new InvalidArgumentException("Invalid type for property {$property->getName()}.");
    }

    private function setBasicTypeValue(ReflectionProperty $property, mixed $value, ReflectionType $propertyType): void
    {
        if ($propertyType->allowsNull() && $value === null) {
            $property->setValue($this, null);
            return;
        }

        // 根据类型检查值
        $typeName = $propertyType->getName();
        switch ($typeName) {
            case 'int':
                if (!is_int($value)) {
                    throw new InvalidArgumentException("Invalid type for property {$property->getName()}.");
                }
                break;

            case 'float':
                if (!is_float($value) && !is_int($value)) {
                    throw new InvalidArgumentException("Invalid type for property {$property->getName()}.");
                }
                break;

            case 'string':
                if (!is_string($value)) {
                    throw new InvalidArgumentException("Invalid type for property {$property->getName()}.");
                }
                break;

            case 'bool':
                if (!is_bool($value)) {
                    throw new InvalidArgumentException("Invalid type for property {$property->getName()}.");
                }
                break;

            case 'array':
                if (!is_array($value)) {
                    throw new InvalidArgumentException("Invalid type for property {$property->getName()}.");
                }
                break;

            default:
                throw new InvalidArgumentException("Type {$typeName} is not supported for property {$property->getName()}.");
        }

        $property->setValue($this, $value);
    }
}
