<?php

declare(strict_types=1);

namespace App\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;

final readonly class TableFilter
{
    /**
     * @param  array<int|string, string>  $options  Labels keyed by allowed value.
     * @param  Closure(Builder<*>, string): mixed  $apply
     */
    public function __construct(
        public string $label,
        public array $options,
        private Closure $apply,
    ) {}

    /**
     * The clause gets its own group, so an OR inside it cannot escape the
     * owner scope.
     *
     * @param  Builder<*>  $query
     * @return Builder<*>
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_string($value) || ! array_key_exists($value, $this->options)) {
            return $query;
        }

        return $query->where(fn (Builder $group) => ($this->apply)($group, $value));
    }
}
