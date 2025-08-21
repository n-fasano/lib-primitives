<?php

namespace Fasano\PrimitivesLib\Metadata\Property\Default;

use Fasano\PrimitivesLib\Metadata\Property\Description;

final readonly class EmptyDescription extends Description
{
    public function __construct()
    {
        parent::__construct('');
    }
}