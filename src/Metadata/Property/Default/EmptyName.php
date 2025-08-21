<?php

namespace Fasano\PrimitivesLib\Metadata\Property\Default;

use Fasano\PrimitivesLib\Metadata\Property\Name;

final readonly class EmptyName extends Name
{
    public function __construct()
    {
        parent::__construct('');
    }
}