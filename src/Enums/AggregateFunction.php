<?php

declare(strict_types=1);

namespace GlowmarktPhp\Enums;

enum AggregateFunction: string
{
    case Sum = 'sum';
    case Average = 'avg';
}
