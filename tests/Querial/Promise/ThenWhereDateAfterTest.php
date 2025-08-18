<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereDateAfter;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereDateAfterTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 指定日付以降のクエリが適用される(): void
    {
        $request = Request::create('/', 'GET', ['from' => '2025-01-01']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereDateAfter('created_at', 'from', 'Y-m-d');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`created_at` >= '2025-01-01 00:00:00'
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'date afterのクエリが一致しません');
    }
}
