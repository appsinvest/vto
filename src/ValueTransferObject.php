<?php

/**
 * ValueTransferObject
 * php version 7.4
 *
 * @category VTO
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/vto
 */

declare(strict_types=1);

namespace SoftInvest\VTO;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;


class ValueTransferObject
{
    /**
     * @throws ReflectionException
     */
    public function __construct(array $arr = [])
    {
        if ($arr) {
            $this->hydrate($arr);
        }
    }

    /**
     * @param array<string, mixed> $arr
     *
     * @throws ReflectionException
     */
    public function hydrate(array $arr): void
    {
        $reflect = new ReflectionClass($this);
        foreach ($arr as $field => $value) {
            $prop = $reflect->getProperty($field);

            switch ((string)$prop->getType()) {
                case 'bool':
                case '?bool' :
                    $fieldValue = (bool)$value;
                    break;
                case 'int' :
                case '?int' :
                    $fieldValue = (int)$value;
                    break;
                case 'float' :
                case '?float' :
                    $fieldValue = (float)$value;
                    break;
                case 'string' :
                case '?string' :
                    $fieldValue = (string)$value;
                    break;
                case 'array' :
                case '?array' :
                    $fieldValue = (array)$value;
                    break;
                default:
                    $fieldValue = $value;
            }

            $className = str_replace('?', '', (string)$prop->getType());
            if (class_exists($className)) {
                $this->$field = new $className($value);
            } else {
                $this->$field = $fieldValue;
            }
        }
    }

    /**
     * @param ?array<string> $onlyKeys
     *
     * @return array<string, mixed>
     */
    public function toArray(?array $onlyKeys = null): array
    {
        $array = $this->toArrayInternal();
        if (!$onlyKeys) {
            return $array;
        }

        return array_filter($array, static function ($v) use ($onlyKeys) {
            return in_array($v, $onlyKeys, true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return array<string>
     */
    public function keys(): array
    {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $arr = [];
        foreach ($props as $prop) {
            /**
             * @var ReflectionProperty $prop
             */
            $arr[$prop->getName()] = $prop->getName();
        }

        return array_values($arr);
    }

    /**
     * @return array<string, mixed>
     */
    private function toArrayInternal(): array
    {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $arr = [];
        foreach ($props as $prop) {
            /**
             * @var ReflectionProperty $prop
             */
            $arr[$prop->getName()] = $this->{$prop->getName()};
        }

        return $arr;
    }
}
