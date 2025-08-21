<?php

namespace Fasano\PrimitivesLib\Metadata\Property\Default;

use Fasano\PrimitivesLib\Metadata\Property\Example;

final readonly class EmptyExample extends Example
{
    public function __construct()
    {
        parent::__construct('');
    }
}