<?php declare(strict_types=1);
namespace ShopwarePsysh\Caster;

use Shopware\Core\Framework\Struct\Struct;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarDumper\Exception\ThrowingCasterException;

class ShopwareCaster implements ShopwareCasterInterface
{
    public function cast($object, $array, Stub $stub, $isNested, $filter): array
    {
        if (!$object instanceof Struct) {
            return $array;
        }

        try {
            return $object->jsonSerialize();
        } catch (ThrowingCasterException $exception) {
            return $array;
        }
    }
}
