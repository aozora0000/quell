<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereBetweenDays;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereBetweenDaysTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    #[Test]
    public function 最小最大が揃っている時は_betwee_nでクエリを実行する(): void
    {
        $request = Request::create('/', 'GET', ['created_at_min' => '2022-01-01', 'created_at_max' => '2022-12-31']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetweenDays('created_at', null, 'Y-m-d');
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."created_at" BETWEEN '2022-01-01 00:00:00'
  AND '2022-12-31 23:59:59'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $query)));
    }

    /**
     * @test
     */
    #[Test]
    public function 最小のみが揃っている時は_moretha_nでクエリを実行する(): void
    {
        $request = Request::create('/', 'GET', ['created_at_min' => '2022-01-01']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetweenDays('created_at', null, 'Y-m-d');
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."created_at" >= '2022-01-01 00:00:00'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $query)));
    }

    /**
     * @test
     */
    #[Test]
    public function 最大のみが揃っている時は_lesstha_nでクエリを実行する(): void
    {
        $request = Request::create('/', 'GET', ['created_at_max' => '2022-12-31']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetweenDays('created_at', null, 'Y-m-d');
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."created_at" <= '2022-12-31 23:59:59'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $query)));
    }
}
