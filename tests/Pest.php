<?php

declare(strict_types=1);

use GlowmarktPhp\GlowmarktApi;
use Saloon\Config;
use Saloon\Http\Faking\MockClient;

require_once __DIR__ . '/Stubs/Illuminate/Contracts/Cache/Repository.php';

uses()
    ->beforeEach(fn () => MockClient::destroyGlobal())
    ->in(__DIR__);

Config::preventStrayRequests();

function getApi($cacheDriver = null): GlowmarktApi {
    return new GlowmarktApi(
        username: getenv('GLOWMARKT_USERNAME'),
        password: getenv('GLOWMARKT_PASSWORD'),
        cacheDriver: $cacheDriver
    );
}