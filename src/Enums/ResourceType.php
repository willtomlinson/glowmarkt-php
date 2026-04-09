<?php

declare(strict_types=1);

namespace GlowmarktPhp\Enums;

enum ResourceType: string
{
    case Readings = 'readings';
    case Current = 'current';
    case FirstTime = 'first-time';
    case LastTime = 'last-time';
    case MeterRead = 'meterread';
    case Tariff = 'tariff';
    case TarriffList = 'tariff-list';
    case Catchup = 'catchup';
    case GlowBinary = 'glowbinary';
}
