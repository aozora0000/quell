<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereBetween;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereBetweenTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 最小最大が揃っている時は_betwee_nでクエリを実行する(): void
    {
        $request = Request::create('/', 'GET', ['price_min' => '1', 'price_max' => '100']);

        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereBetween('price', null);
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`price` BETWEEN '1'
  AND '100'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $builder)));
    }

    #[Test]
    public function 最小のみが揃っている時は_moretha_nでクエリを実行する(): void
    {
        $request = Request::create('/', 'GET', ['price_min' => '1']);

        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereBetween('price', null);
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`price` >= '1'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $builder)));
    }

    #[Test]
    public function 最大のみが揃っている時は_lesstha_nでクエリを実行する(): void
    {
        $request = Request::create('/', 'GET', ['price_max' => '100']);

        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereBetween('price', null);
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`price` <= '100'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $builder)));
    }
}
