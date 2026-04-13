<?php

declare(strict_types=1);

namespace GlowmarktPhp\Requests;

use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetAccessTokenRequest extends Request implements HasBody, Cacheable
{
    use HasJsonBody;
    use HasCaching;

    protected Method $method = Method::POST;

    protected int $cacheExpiryInSeconds = 3600;

    public function __construct(
        private readonly string $username,
        private readonly string $password,
        private readonly Driver $resolvedCacheDriver,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/auth';
    }

    public function resolveCacheDriver(): Driver
    {
        return $this->resolvedCacheDriver;
    }

    public function cacheExpiryInSeconds(): int
    {
        return $this->cacheExpiryInSeconds;
    }

    protected function defaultBody(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    protected function getCacheableMethods(): array
    {
        return [Method::POST];
    }
}
