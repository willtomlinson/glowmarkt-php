<?php

declare(strict_types=1);

namespace GlowmarktPhp\Enums;

enum AggregatePeriod: string
{
    case OneMinute = 'PT1M';
    case ThirtyMinutes = 'PT30M';
    case OneHour = 'PT1H';
    case OneDay = 'P1D';
    case OneWeek = 'P1W';
    case OneMonth = 'P1M';
    case OneYear = 'P1Y';
}
