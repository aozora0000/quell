<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 08:42
 */
namespace Querial\Contracts\Support;

use Illuminate\Database\Eloquent\Builder;

trait CreateAttributeFromTable
{
    /**
     * @param Builder $builder
     * @param string  $attribute
     *
     * @return string
     */
    public function createAttributeFromTable(Builder $builder, string $attribute): string
    {
        return sprintf('%s.%s', $builder->getModel()->getTable(), $attribute);
    }
}
