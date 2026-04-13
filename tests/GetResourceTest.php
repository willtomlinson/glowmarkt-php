<?php

declare(strict_types=1);

use Carbon\Carbon;
use GlowmarktPhp\Enums\AggregateFunction;
use GlowmarktPhp\Enums\AggregatePeriod;
use GlowmarktPhp\Requests\GetAccessTokenRequest;
use GlowmarktPhp\Requests\GetResourceRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('it 404s when getting resource reading for incorrect id', function (): void {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
        GetResourceRequest::class => MockResponse::fixture('resource-not-found'),
    ]);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Not Found (404)');

    $api = getApi();
    $api->allowNonCachedRequests();

    $api->getResourceReadings(
        id: 'incorrect-id',
        from: Carbon::parse('april 2026')->startOfMonth(),
        to: Carbon::parse('april 2026')->endOfMonth(),
    );
});

test('it can get gas resource readings', function (): void {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
        GetResourceRequest::class => MockResponse::fixture('resource-gas'),
    ]);

    $api = getApi();
    $api->allowNonCachedRequests();

    $readings = $api->getResourceReadings(
        id: 'gas-id',
        from: Carbon::parse('april 2026')->startOfMonth(),
        to: Carbon::parse('april 2026')->endOfMonth(),
        aggregateFunction: AggregateFunction::Sum,
        aggregatePeriod: AggregatePeriod::OneDay
    );

    expect($readings)->toBeArray();
    expect($readings['name'])->toBe('gas consumption');
    expect($readings['data'])->toHaveCount(30); // days in april
});

test('it can get electricity resource readings', function (): void {
    MockClient::global([
        GetAccessTokenRequest::class => MockResponse::fixture('authentication'),
        GetResourceRequest::class => MockResponse::fixture('resource-electricity'),
    ]);

    $api = getApi();
    $api->allowNonCachedRequests();

    $readings = $api->getResourceReadings(
        id: 'electricity-id',
        from: Carbon::parse('april 2026')->startOfMonth(),
        to: Carbon::parse('april 2026')->endOfMonth(),
        aggregateFunction: AggregateFunction::Sum,
        aggregatePeriod: AggregatePeriod::OneDay
    );

    expect($readings)->toBeArray();
    expect($readings['name'])->toBe('electricity consumption');
    expect($readings['data'])->toHaveCount(30); // days in april
});
