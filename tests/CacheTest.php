<?php

declare(strict_types=1);

use Illuminate\Contracts\Cache\Repository;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use GlowmarktPhp\Requests\GetAccessTokenRequest;
use GlowmarktPhp\Requests\GetVirtualEntityRequest;

test('it throws an exception when a cache driver is not provided and non-caches requests are not allowed', function (): void {
    $api = getApi();

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('A cache driver must be provided, or override with allowNonCachedRequests()');

    $api->send(new GetVirtualEntityRequest());
});

test('it does not throw an exception when a cache driver is not provided and non-cached requests are allowed', function (): void {
    try {
        MockClient::global([
            GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
            GetVirtualEntityRequest::class => MockResponse::fixture('virtual-entity'),
        ]);

        $api = getApi();
        $api->allowNonCachedRequests();

        // Need to send a request to ensure it doesn't throw an exception
        $api->send(new GetVirtualEntityRequest());

        // If we're here, no exception thrown
        $this->addToAssertionCount(1);
    } catch (Exception $e) {
        $this->fail($e->getMessage());   
    }
});

test('it does not throw an exception when a cache driver is provided', function (): void {
    try {
        MockClient::global([
            GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
            GetVirtualEntityRequest::class => MockResponse::fixture('virtual-entity'),
        ]);

        $api = getApi($this->createMock(Repository::class));

        $api->send(new GetVirtualEntityRequest());

        $this->addToAssertionCount(1);
    } catch (Exception $e) {
        $this->fail($e->getMessage());
    }
});
