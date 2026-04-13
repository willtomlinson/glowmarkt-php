<?php

declare(strict_types=1);

use GlowmarktPhp\Requests\GetAccessTokenRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use GlowmarktPhp\Requests\GetVirtualEntityRequest;

test('it 404s when getting resource reading for incorrect id', function(): void {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
        GetVirtualEntityRequest::class => MockResponse::fixture('virtual-entity-not-found'),
    ]);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Not Found (404)');

    $api = getApi();
    $api->allowNonCachedRequests();

    $api->getVirtualEntity('incorrect-id');
});

test('it can get all virtual entities', function(): void {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
        GetVirtualEntityRequest::class => MockResponse::fixture('virtual-entities'),
    ]);

    $api = getApi();
    $api->allowNonCachedRequests();

    $virtualEntities = $api->getVirtualEntities();

    expect($virtualEntities)->toBeArray();
    expect($virtualEntities)->toHaveCount(1);
    expect($virtualEntities[0]['resources'])->toBeArray();
    expect($virtualEntities[0]['resources'])->toHaveCount(5);
});

test('it can get one virtual entity', function(): void {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
        GetVirtualEntityRequest::class => MockResponse::fixture('virtual-entity'),
    ]);

    $api = getApi();
    $api->allowNonCachedRequests();

    $virtualEntity = $api->getVirtualEntity('virtual-entity-id');

    expect($virtualEntity)->toBeArray();
    expect($virtualEntity['resources'])->toBeArray();
    expect($virtualEntity['resources'])->toHaveCount(5);
});