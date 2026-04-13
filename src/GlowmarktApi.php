<?php

declare(strict_types=1);

namespace GlowmarktPhp;

// Documentation: https://docs.glowmarkt.com/GlowmarktAPIDataRetrievalDocumentationIndividualUserForBright.pdf
use GlowmarktPhp\CacheDrivers\NullCacheDriver;
use GlowmarktPhp\Enums\AggregateFunction;
use GlowmarktPhp\Enums\AggregatePeriod;
use GlowmarktPhp\Enums\ResourceType;
use GlowmarktPhp\Requests\GetAccessTokenRequest;
use GlowmarktPhp\Requests\GetResourceRequest;
use GlowmarktPhp\Requests\GetVirtualEntityRequest;
use Illuminate\Contracts\Cache\Repository;
use League\Flysystem\Filesystem;
use Psr\Cache\CacheItemPoolInterface;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\FlysystemDriver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Drivers\PsrCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;

class GlowmarktApi extends Connector implements Cacheable
{
    use HasTimeout;
    use AlwaysThrowOnErrors;
    use HasCaching;

    protected const BASE_URL = 'https://api.glowmarkt.com/api/v0-1';

    // This looks like it's hard-coded, but this exact value is actually required by the glowmarkt API .
    protected const APPLICATION_ID = 'b0f1b774-a586-4f72-9edd-27ead8aa7a8d';

    protected int $connectTimeout = 60;
    protected int $requestTimeout = 120;

    /** @disregard P1009 Undefined type */
    protected readonly CacheItemPoolInterface|Filesystem|Repository|null $cacheDriver;

    protected int $cacheExpiryInSeconds = 3600;
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

    public function cacheExpiryInSeconds(): int
    {
        return $this->cacheExpiryInSeconds;
    }

    public function allowNonCachedRequests(): void
    {
        $this->allowNonCachedRequests = true;
    }

    public function resolveCacheDriver(): Driver
    {
        /** @disregard P1009 Undefined type */
        if ($this->cacheDriver instanceof CacheItemPoolInterface) {
            /** @disregard P1006 Expected type */
            /** @disregard P1009 Undefined type */
            return new PsrCacheDriver(new Psr16Cache($this->cacheDriver));
        }

        /** @disregard P1009 Undefined type */
        if ($this->cacheDriver instanceof Repository) {
            return new LaravelCacheDriver($this->cacheDriver);
        }

        /** @disregard P1009 Undefined type */
        if ($this->cacheDriver instanceof Filesystem) {
            return new FlysystemDriver($this->cacheDriver);
        }

        return new NullCacheDriver();
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
                resolvedCacheDriver: $this->resolveCacheDriver(),
            ));
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to authenticate with the Glowmarkt API: '.$e->getMessage(), $e->getCode(), $e);
        }

        $token = $authResponse->json()['token'] ?? null;
        if (null === $token) {
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

    public function getResourceReadings(
        string $id,
        \DateTime $from,
        \DateTime $to,
        ?AggregateFunction $aggregateFunction = null,
        ?AggregatePeriod $aggregatePeriod = null,
    ): array {
        return $this->getResourceGivenType($id, ResourceType::Readings, $from, $to, $aggregateFunction, $aggregatePeriod);
    }

    protected function defaultHeaders(): array
    {
        return [
            'applicationId' => self::APPLICATION_ID,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    private function getResourceGivenType(
        string $id,
        ResourceType $type,
        \DateTime $from,
        \DateTime $to,
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
}
