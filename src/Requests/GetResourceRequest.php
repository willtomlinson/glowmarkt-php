<?php

declare(strict_types=1);

namespace GlowmarktPhp\Requests;

use DateTime;
use GlowmarktPhp\Enums\AggregateFunction;
use GlowmarktPhp\Enums\AggregatePeriod;
use GlowmarktPhp\Enums\ResourceType;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Documentation: https://api.glowmarkt.com/api-docs/v0-1/resourcesys/#/Resource/resource.findById.
 */
class GetResourceRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly ?string $id = null,
        private readonly ?ResourceType $type = null,
        private readonly ?DateTime $from = null,
        private readonly ?DateTime $to = null,
        private readonly ?AggregateFunction $aggregateFunction = null,
        private readonly ?AggregatePeriod $aggregatePeriod = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        $endpoint = '/resource';

        if ($this->id) {
            $endpoint .= '/'.$this->id;

            if ($this->type instanceof ResourceType) {
                $endpoint .= '/'.$this->type->value;
            }
        }

        return $endpoint;
    }

    protected function defaultQuery(): array
    {
        $query = [
            'nulls' => 1, // Any missing values from a time series resource will return as null instead of zero
        ];

        if ($this->from instanceof DateTime) {
            $query['from'] = $this->from->format('Y-m-d\TH:i:s');
        }
        if ($this->to instanceof DateTime) {
            $query['to'] = $this->to->format('Y-m-d\TH:i:s');
        }
        if ($aggregateFunction = $this->aggregateFunction ?? AggregateFunction::Sum) {
            $query['function'] = $aggregateFunction->value;
        }
        if ($aggregatePeriod = $this->aggregatePeriod ?? AggregatePeriod::OneDay) {
            $query['period'] = $aggregatePeriod->value;
        }

        return $query;
    }
}
