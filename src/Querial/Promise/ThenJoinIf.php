<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * ホワイトリストに基づき、指定されたJOINを適用するPromise。
 *
 * 例: join=items → join `items` on `items`.`user_id` = `users`.`id`
 */
class ThenJoinIf extends PromiseQuery
{
    protected ScalarTarget $target;

    /**
     * @param array<string,array{table:string, first:string, operator:string, second:string, type?:'inner'|'left'}> $allowedJoins
     * @param string $inputTarget 入力キー（既定: join）
     */
    public function __construct(
        protected array $allowedJoins,
        protected string $inputTarget = 'join',
    ) {
        $this->target = new ScalarTarget($this->inputTarget);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $name = $request->str($this->inputTarget, '')->value();
        if (! isset($this->allowedJoins[$name])) {
            return $builder;
        }

        $def = $this->allowedJoins[$name];
        $type = ($def['type'] ?? 'inner') === 'left' ? 'left' : 'inner';

        // first/second は "table.column" 形式を想定（呼び出し時に適切に設定）
        [$firstTable, $firstCol] = explode('.', $def['first'], 2);
        [$secondTable, $secondCol] = explode('.', $def['second'], 2);

        if ($type === 'left') {
            return $builder->leftJoin($def['table'], sprintf('%s.%s', $firstTable, $firstCol), $def['operator'], sprintf('%s.%s', $secondTable, $secondCol));
        }
        return $builder->join($def['table'], sprintf('%s.%s', $firstTable, $firstCol), $def['operator'], sprintf('%s.%s', $secondTable, $secondCol));
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }
}
