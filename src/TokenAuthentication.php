<?php

declare(strict_types=1);

namespace GlowmarktPhp;

use Saloon\Http\PendingRequest;
use Saloon\Contracts\Authenticator;

class TokenAuthentication implements Authenticator
{
    public function __construct(
        private string $token,
    ) {
    }

    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('token', $this->token);
    }
}
