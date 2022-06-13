<?php

namespace Querial\Contracts\Support;

use Illuminate\Database\Eloquent\Builder;
use Querial\Contracts\PromiseInterface;

abstract class PromiseQuery implements PromiseInterface
{
    protected ?string $table = null;

    /**
     * @return string|null
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * @param string $table
     * @return void
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @param Builder $builder
     * @param string  $attribute
     *
     * @return string
     */
    public function createAttributeFromTable(Builder $builder, string $attribute): string
    {
        if($this->table === null) {
            return sprintf('%s.%s', $builder->getModel()->getTable(), $attribute);
        }
        return sprintf('%s.%s', $this->table, $attribute);
    }
}