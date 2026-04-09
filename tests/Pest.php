<?php

use Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');

use Saloon\Http\PendingRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

function mockClient(): MockClient
{
    return new MockClient([
        '*' => function (PendingRequest $pendingRequest) {
            $endpoint = $pendingRequest->getRequest()->resolveEndpoint();
            $method = $pendingRequest->getMethod()->value;

            return MockResponse::fixture(implode('/', [$endpoint, $method]));
        },
    ]);
}
