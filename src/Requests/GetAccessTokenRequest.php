<?php

declare(strict_types=1);

namespace GlowmarktPhp\Requests;

use GlowmarktPhp\CacheDrivers\NullCacheDriver;
use Saloon\CachePlugin\Drivers\PsrCacheDriver;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\FlysystemDriver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetAccessTokenRequest extends Request implements HasBody, Cacheable
{
    use HasJsonBody;
    use HasCaching;

    protected int $cacheExpiryInSeconds = 3600;

    protected Method $method = Method::POST;

    /** @disregard P1009 Undefined type */
    private readonly \Psr\Cache\CacheItemPoolInterface|\Illuminate\Contracts\Cache\Repository|\League\Flysystem\Filesystem|null $cacheDriver;

    public function __construct(
        private readonly string $username,
        private readonly string $password,
        $cacheDriver = null,
        private readonly string $applicationId = '',
    ) {
        $this->cacheDriver = $cacheDriver;
    }

    protected function defaultBody(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'applicationId' => $this->applicationId,
        ];
    }

    protected function getCacheableMethods(): array
    {
        return [Method::POST];
    }

    public function resolveCacheDriver(): Driver
    {
        /** @disregard P1009 Undefined type */
        if ($this->cacheDriver instanceof \Psr\Cache\CacheItemPoolInterface) {
            /** @disregard P1006 Expected type */
            /** @disregard P1009 Undefined type */
            return new PsrCacheDriver(new Psr16Cache($this->cacheDriver));
        }

        /** @disregard P1009 Undefined type */
        if ($this->cacheDriver instanceof \Illuminate\Contracts\Cache\Repository) {
            return new LaravelCacheDriver($this->cacheDriver);
        }

        /** @disregard P1009 Undefined type */
        if ($this->cacheDriver instanceof \League\Flysystem\Filesystem) {
            return new FlysystemDriver($this->cacheDriver);
        }

        return new NullCacheDriver();
    }

    public function cacheExpiryInSeconds(): int
    {
        return $this->cacheExpiryInSeconds;
    }

    public function resolveEndpoint(): string
    {
        return '/auth';
    }
}
