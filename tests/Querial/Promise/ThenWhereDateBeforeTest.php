<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereDateBefore;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereDateBeforeTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 指定日付以前のクエリが適用される(): void
    {
        $request = Request::create('/', 'GET', ['to' => '2025-12-31']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereDateBefore('created_at', 'to', 'Y-m-d');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`created_at` <= '2025-12-31 23:59:59'
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'date beforeのクエリが一致しません');
    }
}
