<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereEndsWith;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereEndsWithTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 指定文字列で後方一致の_like検索が行われる(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereEndsWith('name');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`name` LIKE '%test'
EOT;
        $actual = $this->format($instance->resolve($request, $builder));
        $this->assertSame(mb_strtolower($expected), $actual, '後方一致のLIKEクエリが一致しません');
    }
}
