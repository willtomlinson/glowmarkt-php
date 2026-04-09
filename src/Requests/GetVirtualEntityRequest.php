<?php

declare(strict_types=1);

namespace GlowmarktPhp\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Documentation: https://api.glowmarkt.com/api-docs/v0-1/vesys/#/Virtual%20Entity/virtualentity.findById
 */

class GetVirtualEntityRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly ?string $id = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        if ($this->id) {
            return '/virtualentity/' . $this->id . '/resources';
        }

        return '/virtualentity';
    }

}
