<?php

namespace Fasano\PrimitivesLib\Metadata;

use Fasano\PrimitivesLib\Metadata\Property\Description;
use Fasano\PrimitivesLib\Metadata\Property\Example;
use Fasano\PrimitivesLib\Metadata\Property\Name;
use Fasano\PrimitivesLib\Metadata\Property\Type;
use Fasano\PrimitivesLib\Metadata\Property\Fqcn;

readonly class PrimitiveMetadata
{
    public function __construct(
        public Fqcn $fqcn,
        public Type $type,
        public Name $name,
        public Example $example,
        public Description $description,
    ) {}
}