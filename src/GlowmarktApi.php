<?php

declare(strict_types=1);

namespace GlowmarktPhp;

/**
 * Documentation: https://docs.glowmarkt.com/GlowmarktAPIDataRetrievalDocumentationIndividualUserForBright.pdf
 */

use DateTime;
use GlowmarktPhp\Enums\AggregateFunction;
use GlowmarktPhp\Enums\AggregatePeriod;
use GlowmarktPhp\Enums\ResourceType;
use GlowmarktPhp\Requests\GetAccessTokenRequest;
use GlowmarktPhp\Requests\GetResourceRequest;
use GlowmarktPhp\Requests\GetVirtualEntityRequest;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;

class GlowmarktApi extends Connector
{
    use HasTimeout;
    use AlwaysThrowOnErrors;

    protected int $connectTimeout = 60;
    protected int $requestTimeout = 120;

    protected const BASE_URL = 'https://api.glowmarkt.com/api/v0-1';

    // This looks like it's hard-coded, but this exact value is actually required by the glowmarkt API .
    protected const APPLICATION_ID = 'b0f1b774-a586-4f72-9edd-27ead8aa7a8d';

    /** @disregard P1009 Undefined type */
    protected readonly \Psr\Cache\CacheItemPoolInterface|\Illuminate\Contracts\Cache\Repository|\League\Flysystem\Filesystem|null $cacheDriver;

    protected $allowNonCachedRequests = false;

    public function __construct(
        private readonly string $username,
        private readonly string $password,
        $cacheDriver = null,
        private readonly string $applicationId = self::APPLICATION_ID,
    ) {
        $this->cacheDriver = $cacheDriver;
    }

    public function resolveBaseUrl(): string
    {
        return self::BASE_URL;
    }

    protected function defaultHeaders(): array
    {
        return [
            'applicationId' => self::APPLICATION_ID,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function allowNonCachedRequests(): void
    {
        $this->allowNonCachedRequests = true;
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        if ($pendingRequest->getRequest() instanceof GetAccessTokenRequest) {
            return;
        }

        if (!$this->allowNonCachedRequests && !$this->cacheDriver) {
            throw new \InvalidArgumentException('A cache driver must be provided, or override with allowNonCachedRequests()');
        }

        try {
            $authResponse = $this->send(new GetAccessTokenRequest(
                username: $this->username,
                password: $this->password,
                cacheDriver: $this->cacheDriver,
                applicationId: $this->applicationId
            ));
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to authenticate with the Glowmarkt API: ' . $e->getMessage());
        }

        $token = $authResponse->json()['token'] ?? null;
        if ($token === null) {
            throw new \RuntimeException('Authentication response did not contain a token');
        }
        $pendingRequest->authenticate(new TokenAuthentication($token));
    }

    public function getVirtualEntities(): array
    {
        return $this->send(new GetVirtualEntityRequest())->json();
    }

    public function getVirtualEntity(string $id): array
    {
        return $this->send(new GetVirtualEntityRequest($id))->json();
    }

    public function getResources(): array
    {
        return $this->send(new GetResourceRequest())->json();
    }

    public function getResource(string $id): array
    {
        return $this->send(new GetResourceRequest($id))->json();
    }

    private function getResourceGivenType(
        string $id,
        ResourceType $type,
        DateTime $from,
        DateTime $to,
        ?AggregateFunction $aggregateFunction,
        ?AggregatePeriod $aggregatePeriod,
    ): array {
        return $this->send(new GetResourceRequest(
            $id,
            $type,
            $from,
            $to,
            $aggregateFunction,
            $aggregatePeriod,
        ))->json();
    }

    public function getResourceReadings(string $id, DateTime $from, DateTime $to, ?AggregateFunction $aggregateFunction = null, ?AggregatePeriod $aggregatePeriod = null): array
    {
        return $this->getResourceGivenType($id, ResourceType::Readings, $from, $to, $aggregateFunction, $aggregatePeriod);
    }

}
