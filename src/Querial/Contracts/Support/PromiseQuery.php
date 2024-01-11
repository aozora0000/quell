<?php

namespace Querial\Contracts\Support;

use Illuminate\Database\Eloquent\Builder;
use Querial\Contracts\PromiseInterface;

abstract class PromiseQuery implements PromiseInterface
{
    protected ?string $table = null;

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function createAttributeFromTable(Builder $builder, string $attribute): string
    {
        if ($this->table === null) {
            return sprintf('%s.%s', $builder->getModel()->getTable(), $attribute);
        }

        return sprintf('%s.%s', $this->table, $attribute);
    }
}
