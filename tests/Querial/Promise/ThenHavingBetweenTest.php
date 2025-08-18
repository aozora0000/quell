<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenHavingBetween;
use Tests\Querial\WithEloquentModelTestCase;

class ThenHavingBetweenTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 最小最大が揃っている時は_having_betweenでクエリを実行する(): void
    {
        $request = Request::create('/', 'GET', ['total_min' => '10', 'total_max' => '20']);
        $model = $this->createModel();

        // 集計列とグループ化を用意
        $builder = $model->newQuery()->selectRaw('count(*) as total')->groupBy('users.id');

        $instance = new ThenHavingBetween('total', 'total');

        $expected = <<<'EOT'
SELECT
  count(*) as total
FROM
  `users`
GROUP BY
  `users`.`id`
HAVING
  `total` BETWEEN '10'
  AND '20'
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'having between のクエリが一致しません');
    }
}
