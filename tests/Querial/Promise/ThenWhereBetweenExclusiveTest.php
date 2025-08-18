<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereBetweenExclusive;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereBetweenExclusiveTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 最小最大が揃っている時は_排他的範囲でクエリを実行する(): void
    {
        $request = Request::create('/', 'GET', ['score_min' => '50', 'score_max' => '80']);

        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereBetweenExclusive('score', null);
        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`score` > '50'
  AND `users`.`score` < '80'
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '排他的範囲のクエリが一致しません');
    }
}
