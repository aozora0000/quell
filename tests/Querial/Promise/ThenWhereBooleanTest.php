<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereBoolean;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereBooleanTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 真偽値がtrue系の値の時_boolean等価条件が適用される(): void
    {
        // is_active=true を 1 として扱い、等価絞り込み
        $request = Request::create('/', 'GET', ['is_active' => 'true']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereBoolean('is_active');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`is_active` = 1
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'booleanのtrue等価クエリが一致しません');
    }
}
