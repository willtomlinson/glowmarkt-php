<?php

declare(strict_types=1);

namespace GlowmarktPhp;

use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

class TokenAuthentication implements Authenticator
{
    public function __construct(
        private readonly string $token,
    ) {
    }

    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('token', $this->token);
    }
}
