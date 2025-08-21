<?php

namespace Fasano\PrimitivesLib\Metadata\Attribute;

use Attribute;
use Fasano\PrimitivesLib\Metadata\Property;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Name extends Property\Name
{
}