<?php

declare(strict_types=1);

namespace Illuminate\Contracts\Cache;

interface Repository
{
    public function get($key, $default = null);
    public function set($key, $value, $seconds = null);
}
