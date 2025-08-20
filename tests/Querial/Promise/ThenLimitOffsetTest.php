<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenLimitOffset;
use Tests\Querial\WithEloquentModelTestCase;

class ThenLimitOffsetTest extends WithEloquentModelTestCase
{
    #[Test]
    public function per_pageとpageからlimit_offsetが適用される(): void
    {
        // per_page=10, page=2 → LIMIT 10 OFFSET 10
        $request = Request::create('/', 'GET', ['per_page' => '10', 'page' => '2']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenLimitOffset;

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
LIMIT
  10
OFFSET
  10
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'limit/offsetのクエリが一致しません');
    }
}
