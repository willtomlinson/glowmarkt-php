<?php

declare(strict_types=1);

use GlowmarktPhp\GlowmarktApi;
use GlowmarktPhp\Requests\GetAccessTokenRequest;
use GlowmarktPhp\Requests\GetVirtualEntityRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('it can authenticate', function (): void {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
        GetVirtualEntityRequest::class => MockResponse::fixture('virtual-entities'),
    ]);

    $api = getApi();
    $api->allowNonCachedRequests();

    try {
        // Make any API request
        $api->getVirtualEntities();

        // If we're here, no exception thrown
        $this->addToAssertionCount(1);
    } catch (Exception $e) {
        $this->fail($e->getMessage());
    }
});

test('it fails authentication', function (): void {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::fixture('failed-authentication'),
        GetVirtualEntityRequest::class => MockResponse::fixture('virtual-entities'),
    ]);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Unauthorized (401)');

    $api = new GlowmarktApi(
        username: getenv('GLOWMARKT_USERNAME'),
        password: getenv('GLOWMARKT_PASSWORD'),
    );
    $api->allowNonCachedRequests();

    $api->getVirtualEntities();
});
