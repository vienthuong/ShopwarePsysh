<?php declare(strict_types=1);
namespace ShopwarePsysh\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;

interface ShopwareCasterInterface
{
    public function cast($object, $array, Stub $stub, $isNested, $filter): array;
}
