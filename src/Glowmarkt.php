<?php

declare(strict_types=1);

namespace WillTomlinson\GlowmarktPhp;

class Glowmarkt
{
    public function __construct(
        private string $username,
        private string $password,
    ) {
    }

    public function getData()
    {
        return [
            'data' => 'This is some data from Glowmarkt API',
        ];
    }
}
